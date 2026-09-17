<?php
require_once __DIR__ . '/../config/database.php';

class LogCorreo {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listar(string $desde = '', string $hasta = '', string $estado = ''): array {
        $where  = [];
        $params = [];

        if ($desde) {
            $where[]          = "fecha_recepcion >= :desde";
            $params[':desde'] = $desde;
        }
        if ($hasta) {
            $where[]          = "fecha_recepcion <= :hasta";
            $params[':hasta'] = $hasta;
        }
        if ($estado) {
            $where[]           = "estado = :estado";
            $params[':estado'] = $estado;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT
                    id,
                    uid_correo,
                    message_id,
                    asunto,
                    remitente,
                    fecha_recepcion::text                                          AS fecha_recepcion,
                    hora_recepcion,
                    es_mei,
                    estado,
                    razon_clasificacion,
                    cantidad_ordenes,
                    to_char(fecha_registro AT TIME ZONE 'America/Bogota',
                            'DD/MM/YYYY HH24:MI:SS')                              AS fecha_registro_fmt
                FROM log_correos
                $whereClause
                ORDER BY fecha_recepcion DESC, hora_recepcion DESC, id DESC
                LIMIT 500";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
