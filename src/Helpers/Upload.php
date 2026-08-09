<?php

namespace App\Helpers;

class Upload
{
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/png',
        'application/pdf'
    ];
    protected int $maxSize = 5242880; // 5MB

    public function setAllowedTypes(array $extensions, array $mimes): self
    {
        $this->allowedExtensions = $extensions;
        $this->allowedMimeTypes = $mimes;
        return $this;
    }

    public function file(array $fileInfo, string $destinationDir): ?string
    {
        if (empty($fileInfo['tmp_name']) || $fileInfo['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($fileInfo['size'] > $this->maxSize) {
            return null;
        }

        $fileName = basename($fileInfo['name']);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowedExtensions)) {
            return null;
        }

        // Server-side MIME type detection (ignores browser-supplied header)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($fileInfo['tmp_name']);

        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            return null;
        }

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        // Generate random server-side filename
        $newFileName = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = rtrim($destinationDir, '/') . '/' . $newFileName;

        if (move_uploaded_file($fileInfo['tmp_name'], $destination)) {
            return $newFileName;
        }

        return null;
    }
}
