<?php
require_once __DIR__ . '/../models/Orden.php';
require_once __DIR__ . '/../models/Adjunto.php';
require_once __DIR__ . '/../models/Configuracion.php';

class ApiController {
    private Orden $ordenModel;
    private Adjunto $adjuntoModel;
    private Configuracion $configModel;

    public function __construct() {
        $this->ordenModel   = new Orden();
        $this->adjuntoModel = new Adjunto();
        $this->configModel  = new Configuracion();
    }

    public function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // ── Órdenes ──────────────────────────────────────────
    public function ordenes(): void {
        $this->json($this->ordenModel->listar());
    }

    public function orden(int $id): void {
        $orden = $this->ordenModel->obtener($id);
        if (!$orden) {
            $this->json(['error' => 'No encontrado'], 404);
            return;
        }
        $this->json($orden);
    }

    public function adjuntos(int $ordenId): void {
        $this->json($this->adjuntoModel->listarPorOrden($ordenId));
    }

    public function adjunto(int $id): void {
        $this->adjuntoModel->servir($id);
    }

    public function actualizarOrden(int $id): void {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) {
            $this->json(['error' => 'Body inválido'], 400);
            return;
        }
        $ok = $this->ordenModel->actualizar($id, $body);
        $this->json(['ok' => $ok]);
    }

    // ── Configuración / Prompt ────────────────────────────
    public function obtenerConfig(string $clave): void {
        $config = $this->configModel->obtener($clave);
        if (!$config) {
            $this->json(['error' => 'Clave no encontrada'], 404);
            return;
        }
        $this->json($config);
    }

    public function actualizarConfig(string $clave): void {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body || !isset($body['valor'])) {
            $this->json(['error' => 'Body inválido'], 400);
            return;
        }
        $ok = $this->configModel->actualizar(
            $clave,
            $body['valor'],
            $body['anterior'] ?? '',
            $body['descripcion'] ?? ''
        );
        $this->json(['ok' => $ok]);
    }

    public function auditoriaConfig(string $clave): void {
        $this->json($this->configModel->auditoria($clave));
    }

    public function auditoriaConfigItem(int $id): void {
        $item = $this->configModel->auditoriaItem($id);
        if (!$item) {
            $this->json(['error' => 'No encontrado'], 404);
            return;
        }
        $this->json($item);
    }
}
