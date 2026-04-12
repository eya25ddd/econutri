<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Aliment.php';

class AlimentController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ─── READ ALL ─────────────────────────────── */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM aliments ORDER BY nom ASC");
        return array_map([Aliment::class, 'fromArray'], $stmt->fetchAll());
    }

    /* ─── READ ONE ─────────────────────────────── */
    public function getById(int $id): ?Aliment
    {
        $stmt = $this->db->prepare("SELECT * FROM aliments WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? Aliment::fromArray($row) : null;
    }

    /* ─── CREATE ───────────────────────────────── */
    public function create(array $data): array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $imagePath = $this->handleImageUpload();

        $stmt = $this->db->prepare(
            "INSERT INTO aliments (nom, calories, proteines, glucides, lipides, image)
             VALUES (:nom, :calories, :proteines, :glucides, :lipides, :image)"
        );
        $stmt->execute([
            ':nom'       => trim($data['nom']),
            ':calories'  => (int) $data['calories'],
            ':proteines' => (float) $data['proteines'],
            ':glucides'  => (float) $data['glucides'],
            ':lipides'   => (float) $data['lipides'],
            ':image'     => $imagePath,
        ]);

        return ['success' => true, 'id' => $this->db->lastInsertId()];
    }

    /* ─── UPDATE ───────────────────────────────── */
    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $aliment   = $this->getById($id);
        $imagePath = $aliment ? $aliment->image : null;

        $newImage = $this->handleImageUpload();
        if ($newImage !== null) {
            // Delete old image if exists
            if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {
                @unlink(__DIR__ . '/../' . $imagePath);
            }
            $imagePath = $newImage;
        }

        $stmt = $this->db->prepare(
            "UPDATE aliments
             SET nom=:nom, calories=:calories, proteines=:proteines,
                 glucides=:glucides, lipides=:lipides, image=:image
             WHERE id=:id"
        );
        $stmt->execute([
            ':nom'       => trim($data['nom']),
            ':calories'  => (int) $data['calories'],
            ':proteines' => (float) $data['proteines'],
            ':glucides'  => (float) $data['glucides'],
            ':lipides'   => (float) $data['lipides'],
            ':image'     => $imagePath,
            ':id'        => $id,
        ]);

        return ['success' => true];
    }

    /* ─── DELETE ───────────────────────────────── */
    public function delete(int $id): array
    {
        // Check if aliment is used in recipes
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM recette_aliment WHERE aliment_id = ?");
        $stmt->execute([$id]);
        if ((int) $stmt->fetchColumn() > 0) {
            return [
                'success' => false,
                'message' => "Impossible de supprimer cet aliment car il est utilisé dans des recettes.",
            ];
        }

        // Delete image file
        $aliment = $this->getById($id);
        if ($aliment && $aliment->image && file_exists(__DIR__ . '/../' . $aliment->image)) {
            @unlink(__DIR__ . '/../' . $aliment->image);
        }

        $stmt = $this->db->prepare("DELETE FROM aliments WHERE id = ?");
        $stmt->execute([$id]);

        return ['success' => true];
    }

    /* ─── VALIDATION ───────────────────────────── */
    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['nom'] ?? ''))) {
            $errors['nom'] = "Le nom de l'aliment est obligatoire.";
        } elseif (mb_strlen(trim($data['nom'])) < 2) {
            $errors['nom'] = "Le nom doit contenir au moins 2 caractères.";
        } elseif (mb_strlen(trim($data['nom'])) > 100) {
            $errors['nom'] = "Le nom ne peut pas dépasser 100 caractères.";
        }

        if (!isset($data['calories']) || $data['calories'] === '') {
            $errors['calories'] = "Les calories sont obligatoires.";
        } elseif (!is_numeric($data['calories']) || (int)$data['calories'] < 0) {
            $errors['calories'] = "Les calories doivent être un nombre entier positif.";
        } elseif ((int)$data['calories'] > 9999) {
            $errors['calories'] = "Les calories ne peuvent pas dépasser 9999 kcal.";
        }

        foreach (['proteines' => 'Protéines', 'glucides' => 'Glucides', 'lipides' => 'Lipides'] as $field => $label) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $errors[$field] = "$label sont obligatoires.";
            } elseif (!is_numeric($data[$field]) || (float)$data[$field] < 0) {
                $errors[$field] = "$label doivent être un nombre positif.";
            } elseif ((float)$data[$field] > 999.99) {
                $errors[$field] = "$label ne peuvent pas dépasser 999.99 g.";
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

        $allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mimeType  = mime_content_type($_FILES['image']['tmp_name']);
        if (!in_array($mimeType, $allowed)) {
            return null;
        }

        $ext      = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'aliment_' . uniqid() . '.' . $ext;
        $uploadDir = __DIR__ . '/../assets/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);

        return 'assets/uploads/' . $filename;
    }
}
