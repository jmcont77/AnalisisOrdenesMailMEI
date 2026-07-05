<?php
require_once __DIR__ . '/../config/database.php';

class Adjunto {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listarPorOrden(int $ordenId): array {
        $sql = "SELECT id, nombre_archivo, mime_type, extension, tamano_bytes
                FROM ordenes_mei_adjuntos
                WHERE orden_id = :orden_id
                ORDER BY id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':orden_id' => $ordenId]);
        return $stmt->fetchAll();
    }

    public function obtenerContenido(int $id): ?array {
        $sql = "SELECT nombre_archivo, mime_type, extension, tamano_bytes,
                       encode(contenido, 'base64') AS contenido_b64
                FROM ordenes_mei_adjuntos
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function servir(int $id): void {
        $sql = "SELECT nombre_archivo, mime_type, contenido
                FROM ordenes_mei_adjuntos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo 'Adjunto no encontrado';
            return;
        }

        $mime = $row['mime_type'] ?: 'application/octet-stream';
        header("Content-Type: {$mime}");
        header("Content-Disposition: inline; filename=\"{$row['nombre_archivo']}\"");
        header("Cache-Control: no-cache");

        // contenido viene como recurso de stream desde pgsql bytea
        if (is_resource($row['contenido'])) {
            fpassthru($row['contenido']);
        } else {
            echo $row['contenido'];
        }
    }
}
