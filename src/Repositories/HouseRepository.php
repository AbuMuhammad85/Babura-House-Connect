<?php

namespace App\Repositories;

use App\Core\Database;

class HouseRepository
{
    public function findById(int $id): ?array
    {
        $house = Database::fetch("SELECT * FROM houses WHERE id = :id", ['id' => $id]);
        return $house ?: null;
    }

    public function findDetailsById(int $id): ?array
    {
        $sql = "SELECT h.*, a.name AS area_name, u.full_name AS landlord_name, 
                       u.email AS landlord_email, u.phone AS landlord_phone,
                       lp.id AS landlord_profile_id, lp.verification_status AS landlord_verification
                FROM houses h
                JOIN areas a ON h.area_id = a.id
                JOIN landlord_profiles lp ON h.landlord_id = lp.id
                JOIN users u ON lp.user_id = u.id
                WHERE h.id = :id";
        
        $house = Database::fetch($sql, ['id' => $id]);
        return $house ?: null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO houses (landlord_id, area_id, title, description, address, 
                                   house_type, rent_amount, rent_period, size, amenities, 
                                   bedrooms, bathrooms, status, availability, featured) 
                VALUES (:landlord_id, :area_id, :title, :description, :address, 
                        :house_type, :rent_amount, :rent_period, :size, :amenities, 
                        :bedrooms, :bathrooms, :status, :availability, :featured)";
        
        Database::query($sql, [
            'landlord_id' => $data['landlord_id'],
            'area_id' => $data['area_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'address' => $data['address'],
            'house_type' => $data['house_type'],
            'rent_amount' => $data['rent_amount'],
            'rent_period' => $data['rent_period'] ?? 'year',
            'size' => $data['size'] ?? null,
            'amenities' => $data['amenities'] ?? null,
            'bedrooms' => $data['bedrooms'] ?? 1,
            'bathrooms' => $data['bathrooms'] ?? 1,
            'status' => $data['status'] ?? 'pending_approval',
            'availability' => $data['availability'] ?? 'available',
            'featured' => $data['featured'] ?? 0
        ]);

        return (int) Database::lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE houses SET 
                area_id = :area_id,
                title = :title,
                description = :description,
                address = :address,
                house_type = :house_type,
                rent_amount = :rent_amount,
                rent_period = :rent_period,
                size = :size,
                amenities = :amenities,
                bedrooms = :bedrooms,
                bathrooms = :bathrooms,
                status = :status,
                availability = :availability
                WHERE id = :id";
        
        $stmt = Database::query($sql, [
            'id' => $id,
            'area_id' => $data['area_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'address' => $data['address'],
            'house_type' => $data['house_type'],
            'rent_amount' => $data['rent_amount'],
            'rent_period' => $data['rent_period'],
            'size' => $data['size'] ?? null,
            'amenities' => $data['amenities'] ?? null,
            'bedrooms' => $data['bedrooms'],
            'bathrooms' => $data['bathrooms'],
            'status' => $data['status'],
            'availability' => $data['availability']
        ]);

        return $stmt->rowCount() > 0;
    }

    public function addImages(int $houseId, array $imagePaths): void
    {
        $sql = "INSERT INTO house_images (house_id, file_path, is_primary) VALUES (:house_id, :file_path, :is_primary)";
        foreach ($imagePaths as $index => $path) {
            Database::query($sql, [
                'house_id' => $houseId,
                'file_path' => $path,
                'is_primary' => ($index === 0) ? 1 : 0
            ]);
        }
    }

    public function addVideo(int $houseId, string $videoPath): void
    {
        $sql = "INSERT INTO house_videos (house_id, file_path) VALUES (:house_id, :file_path)";
        Database::query($sql, [
            'house_id' => $houseId,
            'file_path' => $videoPath
        ]);
    }

    public function getImages(int $houseId): array
    {
        return Database::fetchAll("SELECT * FROM house_images WHERE house_id = :house_id", ['house_id' => $houseId]);
    }

    public function getVideo(int $houseId): ?array
    {
        $video = Database::fetch("SELECT * FROM house_videos WHERE house_id = :house_id", ['house_id' => $houseId]);
        return $video ?: null;
    }

    public function getLandlordListings(int $landlordId): array
    {
        $sql = "SELECT h.*, a.name AS area_name, 
                       (SELECT file_path FROM house_images WHERE house_id = h.id AND is_primary = 1 LIMIT 1) AS thumbnail
                FROM houses h
                JOIN areas a ON h.area_id = a.id
                WHERE h.landlord_id = :landlord_id
                ORDER BY h.id DESC";
        return Database::fetchAll($sql, ['landlord_id' => $landlordId]);
    }

    public function getPendingListings(): array
    {
        $sql = "SELECT h.*, a.name AS area_name, u.full_name AS landlord_name
                FROM houses h
                JOIN areas a ON h.area_id = a.id
                JOIN landlord_profiles lp ON h.landlord_id = lp.id
                JOIN users u ON lp.user_id = u.id
                WHERE h.status = 'pending_approval'
                ORDER BY h.id ASC";
        return Database::fetchAll($sql);
    }

    public function getAllAdminListings(): array
    {
        $sql = "SELECT h.*, a.name AS area_name, u.full_name AS landlord_name
                FROM houses h
                JOIN areas a ON h.area_id = a.id
                JOIN landlord_profiles lp ON h.landlord_id = lp.id
                JOIN users u ON lp.user_id = u.id
                ORDER BY h.id DESC";
        return Database::fetchAll($sql);
    }

    public function getApprovedListings(array $filters = []): array
    {
        $sql = "SELECT h.*, a.name AS area_name, lp.verification_status AS landlord_verification,
                       (SELECT file_path FROM house_images WHERE house_id = h.id AND is_primary = 1 LIMIT 1) AS thumbnail
                FROM houses h
                JOIN areas a ON h.area_id = a.id
                JOIN landlord_profiles lp ON h.landlord_id = lp.id
                WHERE h.status = 'published' AND h.availability = 'available'";
        
        $params = [];
        
        if (!empty($filters['area_id'])) {
            $sql .= " AND h.area_id = :area_id";
            $params['area_id'] = $filters['area_id'];
        }

        if (!empty($filters['type'])) {
            $sql .= " AND h.house_type = :type";
            $params['type'] = $filters['type'];
        }

        if (!empty($filters['price'])) {
            if ($filters['price'] === 'under-50k') {
                $sql .= " AND h.rent_amount < 50000";
            } elseif ($filters['price'] === '50k-150k') {
                $sql .= " AND h.rent_amount BETWEEN 50000 AND 150000";
            } elseif ($filters['price'] === 'over-150k') {
                $sql .= " AND h.rent_amount > 150000";
            }
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND h.rent_amount >= :min_price";
            $params['min_price'] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND h.rent_amount <= :max_price";
            $params['max_price'] = (float)$filters['max_price'];
        }

        if (!empty($filters['bedrooms'])) {
            if ($filters['bedrooms'] === '4+') {
                $sql .= " AND h.bedrooms >= 4";
            } else {
                $sql .= " AND h.bedrooms = :bedrooms";
                $params['bedrooms'] = (int)$filters['bedrooms'];
            }
        }

        if (!empty($filters['bathrooms'])) {
            $sql .= " AND h.bathrooms = :bathrooms";
            $params['bathrooms'] = (int)$filters['bathrooms'];
        }

        if (!empty($filters['rent_period'])) {
            $sql .= " AND h.rent_period = :rent_period";
            $params['rent_period'] = $filters['rent_period'];
        }

        if (!empty($filters['amenities'])) {
            $amList = is_array($filters['amenities']) ? $filters['amenities'] : array_map('trim', explode(',', $filters['amenities']));
            foreach ($amList as $index => $am) {
                if (!empty($am)) {
                    $key = 'am_' . $index;
                    $sql .= " AND h.amenities LIKE :{$key}";
                    $params[$key] = '%' . $am . '%';
                }
            }
        }

        if (!empty($filters['featured'])) {
            $sql .= " AND h.featured = 1";
        }

        $sql .= " ORDER BY h.id DESC";
        return Database::fetchAll($sql, $params);
    }

    public function getApprovedLandlordListings(int $landlordId): array
    {
        $sql = "SELECT h.*, a.name AS area_name,
                       (SELECT file_path FROM house_images WHERE house_id = h.id AND is_primary = 1 LIMIT 1) AS thumbnail
                FROM houses h
                JOIN areas a ON h.area_id = a.id
                WHERE h.landlord_id = :landlord_id AND h.status = 'published'
                ORDER BY h.id DESC";
        return Database::fetchAll($sql, ['landlord_id' => $landlordId]);
    }

    public function countApprovedLandlordListings(int $landlordId): int
    {
        $res = Database::fetch(
            "SELECT COUNT(*) AS total FROM houses WHERE landlord_id = :landlord_id AND status = 'published'",
            ['landlord_id' => $landlordId]
        );
        return (int) ($res['total'] ?? 0);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = Database::query(
            "UPDATE houses SET status = :status WHERE id = :id",
            ['id' => $id, 'status' => $status]
        );
        return $stmt->rowCount() > 0;
    }

    public function updateFeatured(int $id, int $featured): bool
    {
        $stmt = Database::query(
            "UPDATE houses SET featured = :featured WHERE id = :id",
            ['id' => $id, 'featured' => $featured]
        );
        return $stmt->rowCount() > 0;
    }

    public function clearImages(int $houseId): array
    {
        $images = $this->getImages($houseId);
        Database::query("DELETE FROM house_images WHERE house_id = :house_id", ['house_id' => $houseId]);
        return array_column($images, 'file_path');
    }

    public function clearVideo(int $houseId): ?string
    {
        $video = $this->getVideo($houseId);
        Database::query("DELETE FROM house_videos WHERE house_id = :house_id", ['house_id' => $houseId]);
        return $video ? $video['file_path'] : null;
    }
}
