<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Categorie.php';

class CategorieController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ─── READ ALL ─────────────────────────────── */
    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM categorie ORDER BY nom ASC");
        return array_map([Categorie::class, 'fromArray'], $stmt->fetchAll());
    }

    /* ─── READ ONE ─────────────────────────────── */
    public function getById(int $id): ?Categorie
    {
        $stmt = $this->db->prepare("SELECT * FROM categorie WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? Categorie::fromArray($row) : null;
    }

    /* ─── CREATE ───────────────────────────────── */
    public function create(array $data): array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check for duplicate name
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM categorie WHERE nom = ?");
        $stmt->execute([trim($data['nom'])]);
        if ((int) $stmt->fetchColumn() > 0) {
            return ['success' => false, 'errors' => ['nom' => 'Cette catégorie existe déjà.']];
        }

        $stmt = $this->db->prepare("INSERT INTO categorie (nom) VALUES (:nom)");
        $stmt->execute([':nom' => trim($data['nom'])]);

        return ['success' => true, 'id' => $this->db->lastInsertId()];
    }

    /* ─── UPDATE ───────────────────────────────── */
    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check for duplicate name (excluding current record)
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM categorie WHERE nom = ? AND id != ?");
        $stmt->execute([trim($data['nom']), $id]);
        if ((int) $stmt->fetchColumn() > 0) {
            return ['success' => false, 'errors' => ['nom' => 'Cette catégorie existe déjà.']];
        }

        $stmt = $this->db->prepare("UPDATE categorie SET nom = :nom WHERE id = :id");
        $stmt->execute([
            ':nom' => trim($data['nom']),
            ':id' => $id,
        ]);

        return ['success' => true];
    }

    /* ─── DELETE ───────────────────────────────── */
    public function delete(int $id): array
    {
        // The foreign key constraints with ON DELETE CASCADE will automatically
        // remove entries from recette_categorie and aliment_categorie tables
        $stmt = $this->db->prepare("DELETE FROM categorie WHERE id = ?");
        $stmt->execute([$id]);

        return ['success' => true];
    }

    /* ─── VALIDATION ───────────────────────────── */
    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['nom'] ?? ''))) {
            $errors['nom'] = "Le nom de la catégorie est obligatoire.";
        } elseif (mb_strlen(trim($data['nom'])) < 2) {
            $errors['nom'] = "Le nom doit contenir au moins 2 caractères.";
        } elseif (mb_strlen(trim($data['nom'])) > 100) {
            $errors['nom'] = "Le nom ne peut pas dépasser 100 caractères.";
        }

        return $errors;
    }

    /* ─── GET CATEGORIES FOR RECETTE ───────────── */
    public function getCategoriesForRecette(int $recetteId): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.* FROM categorie c
             INNER JOIN recette_categorie rc ON rc.categorie_id = c.id
             WHERE rc.recette_id = ?
             ORDER BY c.nom ASC"
        );
        $stmt->execute([$recetteId]);
        return array_map([Categorie::class, 'fromArray'], $stmt->fetchAll());
    }

    /* ─── GET CATEGORIES FOR ALIMENT ───────────── */
    public function getCategoriesForAliment(int $alimentId): array
    {
        $stmt = $this->db->prepare(
            "SELECT c.* FROM categorie c
             INNER JOIN aliment_categorie ac ON ac.categorie_id = c.id
             WHERE ac.aliment_id = ?
             ORDER BY c.nom ASC"
        );
        $stmt->execute([$alimentId]);
        return array_map([Categorie::class, 'fromArray'], $stmt->fetchAll());
    }

    /* ─── SYNC RECETTE CATEGORIES ──────────────── */
    public function syncRecetteCategories(int $recetteId, array $categorieIds): void
    {
        // Remove existing
        $stmt = $this->db->prepare("DELETE FROM recette_categorie WHERE recette_id = ?");
        $stmt->execute([$recetteId]);

        // Insert new
        if (!empty($categorieIds)) {
            $insert = $this->db->prepare(
                "INSERT INTO recette_categorie (recette_id, categorie_id) VALUES (?, ?)"
            );
            foreach ($categorieIds as $catId) {
                $insert->execute([$recetteId, (int) $catId]);
            }
        }
    }

    /* ─── SYNC ALIMENT CATEGORIES ──────────────── */
    public function syncAlimentCategories(int $alimentId, array $categorieIds): void
    {
        // Remove existing
        $stmt = $this->db->prepare("DELETE FROM aliment_categorie WHERE aliment_id = ?");
        $stmt->execute([$alimentId]);

        // Insert new
        if (!empty($categorieIds)) {
            $insert = $this->db->prepare(
                "INSERT INTO aliment_categorie (aliment_id, categorie_id) VALUES (?, ?)"
            );
            foreach ($categorieIds as $catId) {
                $insert->execute([$alimentId, (int) $catId]);
            }
        }
    }

    /* ─── ASSIGN RECETTES TO CATEGORY ──────────── */
    public function assignRecettesToCategory(int $categorieId, array $recetteIds): void
    {
        // Remove existing for this category
        $stmt = $this->db->prepare("DELETE FROM recette_categorie WHERE categorie_id = ?");
        $stmt->execute([$categorieId]);

        // Insert new
        if (!empty($recetteIds)) {
            $insert = $this->db->prepare(
                "INSERT INTO recette_categorie (recette_id, categorie_id) VALUES (?, ?)"
            );
            foreach ($recetteIds as $recetteId) {
                $insert->execute([(int) $recetteId, $categorieId]);
            }
        }
    }

    /* ─── ASSIGN ALIMENTS TO CATEGORY ──────────── */
    public function assignAlimentsToCategory(int $categorieId, array $alimentIds): void
    {
        // Remove existing for this category
        $stmt = $this->db->prepare("DELETE FROM aliment_categorie WHERE categorie_id = ?");
        $stmt->execute([$categorieId]);

        // Insert new
        if (!empty($alimentIds)) {
            $insert = $this->db->prepare(
                "INSERT INTO aliment_categorie (aliment_id, categorie_id) VALUES (?, ?)"
            );
            foreach ($alimentIds as $alimentId) {
                $insert->execute([(int) $alimentId, $categorieId]);
            }
        }
    }

    /* ─── GET RECETTE IDS FOR CATEGORY ─────────── */
    public function getRecetteIdsForCategory(int $categorieId): array
    {
        $stmt = $this->db->prepare("SELECT recette_id FROM recette_categorie WHERE categorie_id = ?");
        $stmt->execute([$categorieId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'recette_id');
    }

    /* ─── GET ALIMENT IDS FOR CATEGORY ─────────── */
    public function getAlimentIdsForCategory(int $categorieId): array
    {
        $stmt = $this->db->prepare("SELECT aliment_id FROM aliment_categorie WHERE categorie_id = ?");
        $stmt->execute([$categorieId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'aliment_id');
    }
}
