<?php
declare(strict_types=1);

class UploadController {
    public function upload(array $p): void {
        Auth::requireLogin();
        header('Content-Type: application/json');

        $file = $_FILES['file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'No file or upload error']);
            return;
        }

        $allowed = ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml'];
        if (!in_array($file['type'], $allowed, true)) {
            http_response_code(400);
            echo json_encode(['error' => 'File type not allowed']);
            return;
        }

        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = bin2hex(random_bytes(8)) . '.' . strtolower($ext);
        $dest = __DIR__ . '/../../public/uploads/' . $name;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to move file']);
            return;
        }

        echo json_encode(['url' => '/uploads/' . $name]);
    }
}
