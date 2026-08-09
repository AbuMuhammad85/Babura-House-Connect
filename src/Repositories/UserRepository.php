<?php

namespace App\Repositories;

use App\Core\Database;

class UserRepository
{
    public function findById(int $id): ?array
    {
        $user = Database::fetch("SELECT * FROM users WHERE id = :id", ['id' => $id]);
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $user = Database::fetch("SELECT * FROM users WHERE email = :email", ['email' => $email]);
        return $user ?: null;
    }

    public function findByPhone(string $phone): ?array
    {
        $user = Database::fetch("SELECT * FROM users WHERE phone = :phone", ['phone' => $phone]);
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO users (role, full_name, email, phone, password_hash, status) 
                VALUES (:role, :full_name, :email, :phone, :password_hash, :status)";
        
        Database::query($sql, [
            'role' => $data['role'],
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password_hash' => $data['password_hash'],
            'status' => $data['status'] ?? 'active'
        ]);

        return (int) Database::lastInsertId();
    }

    public function createTenantProfile(int $userId, array $profileData = []): void
    {
        $sql = "INSERT INTO tenant_profiles (user_id, occupation, preferred_area, bio) 
                VALUES (:user_id, :occupation, :preferred_area, :bio)";
        Database::query($sql, [
            'user_id' => $userId,
            'occupation' => $profileData['occupation'] ?? null,
            'preferred_area' => $profileData['preferred_area'] ?? null,
            'bio' => $profileData['bio'] ?? null
        ]);
    }

    public function createLandlordProfile(int $userId, array $profileData = []): void
    {
        $sql = "INSERT INTO landlord_profiles (user_id, address, bio, verification_status) 
                VALUES (:user_id, :address, :bio, :verification_status)";
        Database::query($sql, [
            'user_id' => $userId,
            'address' => $profileData['address'] ?? null,
            'bio' => $profileData['bio'] ?? null,
            'verification_status' => $profileData['verification_status'] ?? 'pending'
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE users SET 
                full_name = :full_name, 
                email = :email, 
                phone = :phone, 
                status = :status 
                WHERE id = :id";
        
        $stmt = Database::query($sql, [
            'id' => $id,
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'status' => $data['status']
        ]);

        return $stmt->rowCount() > 0;
    }

    public function updateLastLogin(int $id): void
    {
        Database::query(
            "UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id",
            ['id' => $id]
        );
    }

    public function updatePassword(int $id, string $passwordHash): bool
    {
        $stmt = Database::query(
            "UPDATE users SET password_hash = :password_hash WHERE id = :id",
            ['id' => $id, 'password_hash' => $passwordHash]
        );
        return $stmt->rowCount() > 0;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = Database::query(
            "UPDATE users SET status = :status WHERE id = :id",
            ['id' => $id, 'status' => $status]
        );
        return $stmt->rowCount() > 0;
    }
}
