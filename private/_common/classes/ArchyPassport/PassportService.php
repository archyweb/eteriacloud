<?php

class PassportService
{
    private PDO $pdo;
    private string $publicBaseUrl;

    public function __construct(PDO $pdo, string $publicBaseUrl = 'https://passport.archyart.com')
    {
        $this->pdo = $pdo;
        $this->publicBaseUrl = rtrim($publicBaseUrl, '/');
    }

    public function getOrCreate(int $catalogId): array
    {
        $passport = $this->findByCatalogId($catalogId);

        if ($passport) {
            return $passport;
        }

        $uuid = $this->generateUuid();
        $token = $this->generateToken();

        $stmt = $this->pdo->prepare("
            INSERT INTO archy_passport 
                (catalog_id, uuid, public_token, enabled, created_at)
            VALUES 
                (:catalog_id, :uuid, :public_token, 0, NOW())
        ");

        $stmt->execute([
            ':catalog_id'   => $catalogId,
            ':uuid'         => $uuid,
            ':public_token' => $token,
        ]);

        return $this->findByCatalogId($catalogId);
    }

    public function enable(int $catalogId): array
    {
        $passport = $this->getOrCreate($catalogId);

        $stmt = $this->pdo->prepare("
            UPDATE archy_passport
            SET enabled = 1, updated_at = NOW()
            WHERE catalog_id = :catalog_id
        ");

        $stmt->execute([
            ':catalog_id' => $catalogId,
        ]);

        return $this->findByCatalogId($catalogId);
    }

    public function disable(int $catalogId): array
    {
        $passport = $this->getOrCreate($catalogId);

        $stmt = $this->pdo->prepare("
            UPDATE archy_passport
            SET enabled = 0, updated_at = NOW()
            WHERE catalog_id = :catalog_id
        ");

        $stmt->execute([
            ':catalog_id' => $catalogId,
        ]);

        return $this->findByCatalogId($catalogId);
    }

    public function regenerateToken(int $catalogId): array
    {
        $passport = $this->getOrCreate($catalogId);
        $newToken = $this->generateUniqueToken();

        $stmt = $this->pdo->prepare("
            UPDATE archy_passport
            SET public_token = :public_token, updated_at = NOW()
            WHERE catalog_id = :catalog_id
        ");

        $stmt->execute([
            ':public_token' => $newToken,
            ':catalog_id'   => $catalogId,
        ]);

        return $this->findByCatalogId($catalogId);
    }

    public function findByCatalogId(int $catalogId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM archy_passport
            WHERE catalog_id = :catalog_id
            LIMIT 1
        ");

        $stmt->execute([
            ':catalog_id' => $catalogId,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByToken(string $token): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM archy_passport
            WHERE public_token = :public_token
            LIMIT 1
        ");

        $stmt->execute([
            ':public_token' => $token,
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function getPublicUrl(string $token): string
    {
        return $this->publicBaseUrl . '/p/' . urlencode($token);
    }

    private function generateUniqueToken(): string
    {
        do {
            $token = $this->generateToken();
        } while ($this->findByToken($token));

        return $token;
    }

    private function generateToken(int $length = 40): string
    {
        $bytes = random_bytes(32);

        return substr(
            rtrim(strtr(base64_encode($bytes), '+/', '-_'), '='),
            0,
            $length
        );
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);

        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}