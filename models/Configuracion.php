<?php
require_once __DIR__ . '/../config/database.php';

class Configuracion {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function obtener(string $clave): ?array {
        $sql  = "SELECT clave, valor, descripcion,
                        to_char(actualizado_en, 'DD/MM/YYYY HH24:MI:SS') AS actualizado_en
                 FROM configuracion WHERE clave = :clave";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':clave' => $clave]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function actualizar(string $clave, string $valorNuevo, string $valorAnterior, string $descripcion = ''): bool {
        // Actualizar valor en configuracion
        $sql  = "UPDATE configuracion SET valor = :valor, actualizado_en = NOW() WHERE clave = :clave";
        $stmt = $this->pdo->prepare($sql);
        $ok   = $stmt->execute([':valor' => $valorNuevo, ':clave' => $clave]);

        if ($ok) {
            // Registrar en auditoría
            $desc  = $descripcion ?: 'Modificación manual desde editor';
            $sql2  = "INSERT INTO configuracion_auditoria (clave, valor_anterior, valor_nuevo, descripcion)
                      VALUES (:clave, :anterior, :nuevo, :desc)";
            $stmt2 = $this->pdo->prepare($sql2);
            $stmt2->execute([
                ':clave'    => $clave,
                ':anterior' => $valorAnterior,
                ':nuevo'    => $valorNuevo,
                ':desc'     => $desc
            ]);
        }

        return $ok;
    }

    public function auditoria(string $clave): array {
        $sql  = "SELECT id, clave, descripcion,
                        to_char(modificado_en, 'DD/MM/YYYY HH24:MI:SS') AS modificado_en
                 FROM configuracion_auditoria
                 WHERE clave = :clave
                 ORDER BY modificado_en DESC
                 LIMIT 100";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':clave' => $clave]);
        return $stmt->fetchAll();
    }

    public function auditoriaItem(int $id): ?array {
        $sql  = "SELECT id, clave, valor_anterior, valor_nuevo, descripcion,
                        to_char(modificado_en, 'DD/MM/YYYY HH24:MI:SS') AS modificado_en
                 FROM configuracion_auditoria
                 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
