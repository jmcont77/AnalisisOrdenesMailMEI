<?php
require_once __DIR__ . '/../config/database.php';

class Orden {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function listar(): array {
        $sql = "SELECT id, fecha_procesamiento, eps, nombre_paciente,
                       linea_servicio, municipio, departamento, es_orden_mei
                FROM ordenes_mei
                ORDER BY fecha_procesamiento DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtener(int $id): ?array {
        $sql = "SELECT * FROM ordenes_mei WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function actualizar(int $id, array $datos): bool {
        $campos = [
            'fecha_hora_correo', 'proveedor', 'eps', 'entidades', 'asignado_a',
            'nombre_paciente', 'primer_nombre', 'segundo_nombre', 'primer_apellido',
            'segundo_apellido', 'tipo_documento_paciente', 'documento_paciente',
            'fecha_nacimiento', 'edad', 'rango_edad', 'genero', 'diagnostico',
            'ips_formula', 'tipo_documento_medico', 'documento_medico',
            'nombre_medico_formulador', 'fecha_formulacion', 'linea_servicio',
            'equipos_oxigeno', 'flujo_oxigeno_litros', 'consumo_horas',
            'requiere_bpp', 'insumos_oxigeno_terapia', 'salida_hospitalaria',
            'equipo_sahos', 'presiones', 'tipo_mascara', 'talla_mascara',
            'duracion_tratamiento', 'regimen', 'categoria', 'nivel_ibc',
            'tipo_afiliado', 'tipo_autorizacion', 'numero_autorizacion',
            'departamento', 'municipio', 'direccion', 'telefonos',
            'correo_electronico', 'observaciones'
        ];

        $sets = [];
        $params = [':id' => $id];
        foreach ($campos as $campo) {
            if (isset($datos[$campo])) {
                $sets[] = "{$campo} = :{$campo}";
                $params[":{$campo}"] = $datos[$campo];
            }
        }

        if (empty($sets)) return false;

        $sql = "UPDATE ordenes_mei SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
