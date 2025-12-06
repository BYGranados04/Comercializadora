<?php

class ClientesModel extends Model
{
    private PDO $db;
    private string $table = "clientes";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Buscar clientes por NIT o nombre (para el buscador de ventas)
     */
    public function buscar(string $q): array
    {
        $q = trim($q);
        if ($q === "") {
            return [];
        }

        $sql = "SELECT id, nit, nombre, direccion, telefono, correo
                FROM {$this->table}
                WHERE nit LIKE :q OR nombre LIKE :q2
                ORDER BY nombre ASC
                LIMIT 10";

        $stmt = $this->db->prepare($sql);
        $like = "%{$q}%";
        $stmt->bindValue(":q",  $like, PDO::PARAM_STR);
        $stmt->bindValue(":q2", $like, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crear cliente rápido (por si luego quiere guardar “NIT nuevo” desde ventas)
     */
    public function crearRapido(array $data): int
    {
        $sql = "INSERT INTO {$this->table}
                (nit, nombre, direccion, telefono, correo, activo, created_at)
                VALUES (:nit, :nombre, :direccion, :telefono, :correo, 1, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":nit"       => $data["nit"],
            ":nombre"    => $data["nombre"],
            ":direccion" => $data["direccion"] ?? "",
            ":telefono"  => $data["telefono"] ?? "",
            ":correo"    => $data["correo"] ?? "",
        ]);

        return (int)$this->db->lastInsertId();
    }
}
