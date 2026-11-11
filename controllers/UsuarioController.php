<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Usuario.php';

/** CONTROLADOR: valida, llama al modelo y arma la respuesta (mensaje + código HTTP). */
class UsuarioController
{
    private function responder(int $codigo, array $datos): void
    {
        http_response_code($codigo);
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** GET y DELETE exigen la clave del .env en el header X-API-KEY. */
    private function exigirApiKey(): void
    {
        $clave = $_SERVER['HTTP_X_API_KEY'] ?? '';
        if (API_KEY === '' || !hash_equals(API_KEY, $clave)) {
            $this->responder(401, ['status' => 'error', 'message' => 'API key inválida o ausente']);
        }
    }

    // POST /api/usuarios
    public function crear(): void
    {
        $d = json_decode(file_get_contents('php://input') ?: '', true);
        if (!is_array($d)) {
            $this->responder(400, ['status' => 'error', 'message' => 'JSON inválido']);
        }
        $usuario  = is_string($d['usuario'] ?? null) ? trim($d['usuario']) : '';
        $password = is_string($d['password'] ?? null) ? $d['password'] : '';

        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $usuario)) {
            $this->responder(422, ['status' => 'error', 'message' => 'Usuario: 3 a 20 caracteres (letras, números o _)']);
        }
        if (strlen($password) < 6 || strlen($password) > 72) {
            $this->responder(422, ['status' => 'error', 'message' => 'La contraseña debe tener entre 6 y 72 caracteres']);
        }
        if (Usuario::existe($usuario)) {
            $this->responder(409, ['status' => 'error', 'message' => 'Ese nombre de usuario ya está registrado']);
        }
        $id = Usuario::crear($usuario, $password);
        $this->responder(201, ['status' => 'success', 'message' => 'Registro creado exitosamente', 'id' => $id]);
    }

    // GET /api/usuarios
    public function listar(): void
    {
        $this->exigirApiKey();
        $this->responder(200, ['status' => 'success', 'data' => Usuario::todos()]);
    }

    // GET /api/usuarios/{id}
    public function obtener(int $id): void
    {
        $this->exigirApiKey();
        $u = Usuario::buscarPorId($id);
        if ($u === null) {
            $this->responder(404, ['status' => 'error', 'message' => 'Usuario no encontrado']);
        }
        $this->responder(200, $u);
    }

    // DELETE /api/usuarios/{id}
    public function eliminar(int $id): void
    {
        $this->exigirApiKey();
        if (!Usuario::eliminar($id)) {
            $this->responder(404, ['status' => 'error', 'message' => 'Usuario no encontrado']);
        }
        $this->responder(200, ['status' => 'success', 'message' => 'Usuario eliminado']);
    }

    public function noEncontrado(): void
    {
        $this->responder(404, ['status' => 'error', 'message' => 'Ruta no encontrada']);
    }

    public function metodoNoPermitido(): void
    {
        $this->responder(405, ['status' => 'error', 'message' => 'Método no permitido']);
    }
}
