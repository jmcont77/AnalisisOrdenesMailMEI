<?php
require_once __DIR__ . '/../models/Orden.php';
require_once __DIR__ . '/../models/Adjunto.php';

class ApiController {
    private Orden $ordenModel;
    private Adjunto $adjuntoModel;

    public function __construct() {
        $this->ordenModel   = new Orden();
        $this->adjuntoModel = new Adjunto();
    }

    public function json(mixed $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

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
}
