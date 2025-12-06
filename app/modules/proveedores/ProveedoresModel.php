<?php

class ProveedoresModel extends Model
{
    private $db;
    private string $table = 'proveedores';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Listar solo proveedores activos (para selects, combos, etc.)
     */
    public function listarActivos(): array
    {
        $sql = "SELECT id, nit, nombre
                FROM {$this->table}
                WHERE activo = 1
                ORDER BY nombre ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Listar todos los proveedores (para el módulo de administración)
     */
    public function listarTodos(): array
    {
        $sql = "SELECT *
                FROM {$this->table}
                ORDER BY id DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear proveedor
     */
    public function crear(array $data): int
    {
        $sql = "INSERT INTO {$this->table}
                (nit, nombre, direccion, telefono, correo, activo)
                VALUES
                (:nit, :nombre, :direccion, :telefono, :correo, :activo)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nit'       => $data['nit'],
            ':nombre'    => $data['nombre'],
            ':direccion' => $data['direccion'],
            ':telefono'  => $data['telefono'],
            ':correo'    => $data['correo'],
            ':activo'    => $data['activo'] ?? 1,
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function cambiarEstado(int $id, int $activo): bool
    {
        $sql = "UPDATE {$this->table}
                SET activo = :activo
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':activo' => $activo,
            ':id'     => $id,
        ]);
    }

    /**
     * Obtener un proveedor por id (para conocer estado actual)
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
