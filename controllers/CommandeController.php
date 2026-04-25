<?php
require_once __DIR__ . '/../config/database.php';

class CommandeController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getByEmail(string $email): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM commandes WHERE user_email = ? ORDER BY date_commande DESC"
        );
        $stmt->execute([$email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM commandes ORDER BY date_commande DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM commandes WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): array
    {
        if (empty(trim($data['user_name'] ?? ''))) {
            return ['success' => false, 'message' => 'Le nom est obligatoire.'];
        }
        if (empty(trim($data['user_email'] ?? '')) || !filter_var($data['user_email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }
        if (empty($data['recettes'])) {
            return ['success' => false, 'message' => 'Aucune recette sélectionnée.'];
        }

        $stmt = $this->db->prepare(
            "INSERT INTO commandes (user_name, user_email, user_phone, recettes, status)
             VALUES (:name, :email, :phone, :recettes, 'pending')"
        );
        $stmt->execute([
            ':name'     => trim($data['user_name']),
            ':email'    => trim($data['user_email']),
            ':phone'    => trim($data['user_phone'] ?? ''),
            ':recettes' => is_array($data['recettes']) ? json_encode($data['recettes']) : $data['recettes'],
        ]);

        return ['success' => true, 'id' => $this->db->lastInsertId()];
    }

    public function accept(int $id, string $message): array
    {
        $stmt = $this->db->prepare(
            "UPDATE commandes SET status='accepted', admin_message=:msg, date_traitement=NOW() WHERE id=:id"
        );
        $stmt->execute([':msg' => trim($message), ':id' => $id]);
        return ['success' => true];
    }

    public function reject(int $id, string $message): array
    {
        $stmt = $this->db->prepare(
            "UPDATE commandes SET status='rejected', admin_message=:msg, date_traitement=NOW() WHERE id=:id"
        );
        $stmt->execute([':msg' => trim($message), ':id' => $id]);
        return ['success' => true];
    }

    public function delete(int $id): array
    {
        $stmt = $this->db->prepare("DELETE FROM commandes WHERE id = ?");
        $stmt->execute([$id]);
        return ['success' => true];
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM commandes WHERE status = ?");
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }
}
