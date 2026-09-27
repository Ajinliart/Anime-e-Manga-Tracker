<?php
declare(strict_types=1);

class UserRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, username, email, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Cerca per username o email (usato dal login), incluso l'hash della password. */
    public function findForLogin(string $login): ?array
    {
        $stmt = $this->db->prepare('SELECT id, username, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->execute([$login, $login]);
        return $stmt->fetch() ?: null;
    }

    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return (bool) $stmt->fetchColumn();
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetchColumn();
    }

    public function create(string $username, string $email, string $password): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
        return (int) $this->db->lastInsertId();
    }

    public function updatePasswordHash(int $id, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$hash, $id]);
    }
}
