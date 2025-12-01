<?php

class ProductosModel extends Model
{
    private $db;
    private string $table = "productos";

    public function __construct()
    {
        // Conexión directa (por si Model no expone $db)
        $this->db = Database::connect();
    }

    public function buscar(string $q = "")
    {
        if ($q !== "") {
            $sql = "SELECT * FROM {$this->table}
                    WHERE estado = 'ACTIVO'
                    AND (
                        nombre       LIKE :q
                        OR sku       LIKE :q
                        OR codigo_barra LIKE :q
                    )
                    ORDER BY nombre ASC";

            $stmt = $this->db->prepare($sql);
            $like = "%{$q}%";
            $stmt->bindParam(":q", $like);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $sql = "SELECT * FROM {$this->table}
                WHERE estado = 'ACTIVO'
                ORDER BY nombre ASC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function skuExiste(string $sku, ?int $excluirId = null): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE sku = :sku";
        if ($excluirId) {
            $sql .= " AND id != :id";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":sku", $sku);
        if ($excluirId) {
            $stmt->bindParam(":id", $excluirId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (bool)$stmt->fetch();
    }

    public function codigoExiste(string $codigoBarra, ?int $ignoreId = null): bool
    {
        $sql    = "SELECT id FROM {$this->table} WHERE codigo_barra = :codigo";
        $params = [":codigo" => $codigoBarra];

        if ($ignoreId) {
            $sql .= " AND id <> :id";
            $params[":id"] = $ignoreId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (bool)$stmt->fetch();
    }

    public function crear(array $data): bool
    {
        $sql = "INSERT INTO {$this->table}
            (sku, codigo_barra, nombre, categoria_id, marca_id, unidad_medida_id,
             precio_venta, costo_actual, stock, stock_minimo, descripcion, activo, estado)
            VALUES
            (:sku, :codigo_barra, :nombre, :categoria_id, :marca_id, :unidad_medida_id,
             :precio_venta, :costo_actual, :stock, :stock_minimo, :descripcion, :activo, :estado)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":sku"              => $data["sku"],
            ":codigo_barra"     => $data["codigo_barra"],
            ":nombre"           => $data["nombre"],
            ":categoria_id"     => $data["categoria_id"],
            ":marca_id"         => $data["marca_id"],
            ":unidad_medida_id" => $data["unidad_medida_id"],
            ":precio_venta"     => $data["precio_venta"],
            ":costo_actual"     => $data["costo_actual"],
            ":stock"            => $data["stock"],
            ":stock_minimo"     => $data["stock_minimo"],
            ":descripcion"      => $data["descripcion"],
            ":activo"           => $data["activo"],
            ":estado"           => $data["estado"],
        ]);
    }

    public function actualizar(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table}
                SET sku              = :sku,
                    codigo_barra     = :codigo_barra,
                    nombre           = :nombre,
                    categoria_id     = :categoria_id,
                    marca_id         = :marca_id,
                    unidad_medida_id = :unidad_medida_id,
                    precio_venta     = :precio_venta,
                    costo_actual     = :costo_actual,
                    stock            = :stock,
                    stock_minimo     = :stock_minimo,
                    descripcion      = :descripcion,
                    estado           = :estado,
                    updated_at       = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':sku'              => $data['sku'],
            ':codigo_barra'     => $data['codigo_barra'],
            ':nombre'           => $data['nombre'],
            ':categoria_id'     => $data['categoria_id'],
            ':marca_id'         => $data['marca_id'],
            ':unidad_medida_id' => $data['unidad_medida_id'],
            ':precio_venta'     => $data['precio_venta'],
            ':costo_actual'     => $data['costo_actual'],
            ':stock'            => $data['stock'],
            ':stock_minimo'     => $data['stock_minimo'],
            ':descripcion'      => $data['descripcion'],
            ':estado'           => $data['estado'],
            ':id'               => $id,
        ]);
    }

    public function desactivar(int $id): bool
    {
        $sql = "UPDATE {$this->table}
                SET estado = 'INACTIVO', updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function listarActivos(): array
{
    $sql = "SELECT id, sku, nombre FROM productos ORDER BY nombre ASC";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
