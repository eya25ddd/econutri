<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Recette.php';
require_once __DIR__ . '/../models/RecetteAliment.php';

class RecetteController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ─── READ ALL ─────────────────────────────── */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT r.*, COUNT(ra.id) AS nb_aliments
             FROM recettes r
             LEFT JOIN recette_aliment ra ON ra.recette_id = r.id
             GROUP BY r.id
             ORDER BY r.date_creation DESC"
        );
        return array_map(function ($row) {
            $recette = Recette::fromArray($row);
            $recette->nb_aliments = (int) $row['nb_aliments'];
            return $recette;
        }, $stmt->fetchAll());
    }

    /* ─── READ ONE ─────────────────────────────── */
    public function getById(int $id): ?Recette
    {
        $stmt = $this->db->prepare("SELECT * FROM recettes WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? Recette::fromArray($row) : null;
    }

    /* ─── GET ALIMENTS OF RECETTE ───────────────── */
    public function getAliments(int $recetteId): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, ra.quantite, ra.id AS ra_id
             FROM recette_aliment ra
             JOIN aliments a ON a.id = ra.aliment_id
             WHERE ra.recette_id = ?
             ORDER BY a.nom ASC"
        );
        $stmt->execute([$recetteId]);
        return $stmt->fetchAll();
    }

    /* ─── CREATE ───────────────────────────────── */
    public function create(array $data, array $ingredients): array
    {
        $errors = $this->validate($data, $ingredients);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $imagePath = $this->handleImageUpload();

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO recettes (nom, description, image, temps_preparation, difficulte)
                 VALUES (:nom, :description, :image, :temps, :difficulte)"
            );
            $stmt->execute([
                ':nom'        => trim($data['nom']),
                ':description'=> trim($data['description']),
                ':image'      => $imagePath,
                ':temps'      => (int) $data['temps_preparation'],
                ':difficulte' => $data['difficulte'],
            ]);
            $recetteId = (int) $this->db->lastInsertId();

            $this->syncIngredients($recetteId, $ingredients);
            $this->db->commit();

            return ['success' => true, 'id' => $recetteId];
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'errors' => ['global' => 'Erreur lors de la création : ' . $e->getMessage()]];
        }
    }

    /* ─── UPDATE ───────────────────────────────── */
    public function update(int $id, array $data, array $ingredients): array
    {
        $errors = $this->validate($data, $ingredients);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $recette   = $this->getById($id);
        $imagePath = $recette ? $recette->image : null;

        $newImage = $this->handleImageUpload();
        if ($newImage !== null) {
            if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
                @unlink(__DIR__ . '/../' . $imagePath);
            }
            $imagePath = $newImage;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "UPDATE recettes
                 SET nom=:nom, description=:description, image=:image,
                     temps_preparation=:temps, difficulte=:difficulte
                 WHERE id=:id"
            );
            $stmt->execute([
                ':nom'        => trim($data['nom']),
                ':description'=> trim($data['description']),
                ':image'      => $imagePath,
                ':temps'      => (int) $data['temps_preparation'],
                ':difficulte' => $data['difficulte'],
                ':id'         => $id,
            ]);

            $this->syncIngredients($id, $ingredients);
            $this->db->commit();

            return ['success' => true];
        } catch (\Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'errors' => ['global' => 'Erreur lors de la modification : ' . $e->getMessage()]];
        }
    }

    /* ─── DELETE ───────────────────────────────── */
    public function delete(int $id): array
    {
        $recette = $this->getById($id);
        if (!$recette) {
            return ['success' => false, 'message' => 'Recette introuvable.'];
        }

        // recette_aliment rows deleted via ON DELETE CASCADE
        $stmt = $this->db->prepare("DELETE FROM recettes WHERE id = ?");
        $stmt->execute([$id]);

        if ($recette->image && file_exists(__DIR__ . '/../' . $recette->image)) {
            @unlink(__DIR__ . '/../' . $recette->image);
        }

        return ['success' => true];
    }

    /* ─── SYNC INGREDIENTS ─────────────────────── */
    private function syncIngredients(int $recetteId, array $ingredients): void
    {
        // Remove existing
        $stmt = $this->db->prepare("DELETE FROM recette_aliment WHERE recette_id = ?");
        $stmt->execute([$recetteId]);

        $insert = $this->db->prepare(
            "INSERT INTO recette_aliment (recette_id, aliment_id, quantite) VALUES (?, ?, ?)"
        );

        foreach ($ingredients as $ing) {
            $alimentId = (int) ($ing['aliment_id'] ?? 0);
            $quantite  = trim($ing['quantite'] ?? '');
            if ($alimentId > 0) {
                $insert->execute([$recetteId, $alimentId, $quantite ?: null]);
            }
        }
    }

    /* ─── VALIDATION ───────────────────────────── */
    private function validate(array $data, array $ingredients): array
    {
        $errors = [];

        if (empty(trim($data['nom'] ?? ''))) {
            $errors['nom'] = "Le nom de la recette est obligatoire.";
        } elseif (mb_strlen(trim($data['nom'])) < 3) {
            $errors['nom'] = "Le nom doit contenir au moins 3 caractères.";
        } elseif (mb_strlen(trim($data['nom'])) > 150) {
            $errors['nom'] = "Le nom ne peut pas dépasser 150 caractères.";
        }

        if (empty(trim($data['description'] ?? ''))) {
            $errors['description'] = "La description est obligatoire.";
        } elseif (mb_strlen(trim($data['description'])) < 10) {
            $errors['description'] = "La description doit contenir au moins 10 caractères.";
        }

        if (!isset($data['temps_preparation']) || $data['temps_preparation'] === '') {
            $errors['temps_preparation'] = "Le temps de préparation est obligatoire.";
        } elseif (!is_numeric($data['temps_preparation']) || (int)$data['temps_preparation'] < 1) {
            $errors['temps_preparation'] = "Le temps de préparation doit être un entier supérieur à 0.";
        } elseif ((int)$data['temps_preparation'] > 1440) {
            $errors['temps_preparation'] = "Le temps de préparation ne peut pas dépasser 1440 minutes.";
        }

        $validDiff = ['facile', 'moyen', 'difficile'];
        if (empty($data['difficulte']) || !in_array($data['difficulte'], $validDiff)) {
            $errors['difficulte'] = "La difficulté doit être : facile, moyen ou difficile.";
        }

        // At least one ingredient with a quantity
        $validIng = array_filter($ingredients, fn($i) => !empty($i['aliment_id']));
        if (empty($validIng)) {
            $errors['ingredients'] = "Veuillez ajouter au moins un ingrédient à la recette.";
        } else {
            foreach ($validIng as $idx => $ing) {
                if (empty(trim($ing['quantite'] ?? ''))) {
                    $errors['ingredients'] = "Veuillez renseigner la quantité pour tous les ingrédients.";
                    break;
                }
                if (mb_strlen(trim($ing['quantite'])) > 50) {
                    $errors['ingredients'] = "La quantité ne peut pas dépasser 50 caractères.";
                    break;
                }
            }
        }

        return $errors;
    }

    /* ─── IMAGE UPLOAD ─────────────────────────── */
    private function handleImageUpload(): ?string
    {
        if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mimeType = mime_content_type($_FILES['image']['tmp_name']);
        if (!in_array($mimeType, $allowed)) {
            return null;
        }

        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'recette_' . uniqid() . '.' . $ext;
        $uploadDir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);

        return 'assets/uploads/' . $filename;
    }
}
