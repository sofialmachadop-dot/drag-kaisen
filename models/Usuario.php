<?php
declare(strict_types=1);

/** MODELO: solo habla con la base de datos (consultas SQL). */
class Usuario
{
    public static function existe(string $usuario): bool
    {
        $st = db()->prepare('SELECT id FROM usuarios WHERE usuario = :u LIMIT 1');
        $st->execute([':u' => $usuario]);
        return (bool)$st->fetch();
    }

    /** INSERT: devuelve el id del usuario creado. */
    public static function crear(string $usuario, string $password): int
    {
        $st = db()->prepare('INSERT INTO usuarios (usuario, password_hash) VALUES (:u, :h)');
        $st->execute([':u' => $usuario, ':h' => password_hash($password, PASSWORD_DEFAULT)]);
        return (int)db()->lastInsertId();
    }

    /** SELECT de uno (nunca devuelve el hash de la contraseña). */
    public static function buscarPorId(int $id): ?array
    {
        $st = db()->prepare('SELECT id, usuario, creado_en FROM usuarios WHERE id = :id');
        $st->execute([':id' => $id]);
        $fila = $st->fetch();
        return $fila ?: null;
    }

    /** SELECT de todos. */
    public static function todos(): array
    {
        return db()->query('SELECT id, usuario, creado_en FROM usuarios ORDER BY id')->fetchAll();
    }

    /** DELETE: true si borró algo. */
    public static function eliminar(int $id): bool
    {
        $st = db()->prepare('DELETE FROM usuarios WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->rowCount() > 0;
    }
}
