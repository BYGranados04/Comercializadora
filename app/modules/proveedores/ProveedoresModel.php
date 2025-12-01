<?php

class ProveedoresModel extends Model
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function listarActivos()
    {
        $sql = "SELECT id, nit, nombre
                FROM proveedores
                WHERE activo = 1
                ORDER BY nombre ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
