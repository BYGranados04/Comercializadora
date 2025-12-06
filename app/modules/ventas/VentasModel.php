<?php

class VentasModel extends Model
{
    private PDO $db;
    private string $tableVentas  = "ventas";
    private string $tableDetalle = "ventas_detalle";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Lista ventas recientes
     */
    public function listar(int $limite = 50): array
    {
        $sql = "SELECT *
                FROM {$this->tableVentas}
                ORDER BY id DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea venta + detalle y descuenta stock
     */
    public function crearVenta(array $header, array $detalles): int
    {
        try {
            $this->db->beginTransaction();

            // Encabezado
            $sql = "INSERT INTO {$this->tableVentas}
                    (cliente, usuario_id, fecha_venta, numero_doc,
                     total_bruto, descuento, total_neto, estado, notas)
                    VALUES
                    (:cliente, :usuario_id, :fecha_venta, :numero_doc,
                     :total_bruto, :descuento, :total_neto, :estado, :notas)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cliente'     => $header['cliente'],
                ':usuario_id'  => $header['usuario_id'],
                ':fecha_venta' => $header['fecha_venta'],
                ':numero_doc'  => $header['numero_doc'],
                ':total_bruto' => $header['total_bruto'],
                ':descuento'   => $header['descuento'],
                ':total_neto'  => $header['total_neto'],
                ':estado'      => $header['estado'],
                ':notas'       => $header['notas'],
            ]);

            $ventaId = (int)$this->db->lastInsertId();

            // Detalle
            $sqlDet = "INSERT INTO {$this->tableDetalle}
                       (venta_id, producto_id, cantidad, precio_unitario, descuento, subtotal)
                       VALUES
                       (:venta_id, :producto_id, :cantidad, :precio_unitario, :descuento, :subtotal)";
            $stmtDet = $this->db->prepare($sqlDet);

            // Actualización de stock
            $sqlUpdProd = "UPDATE productos
                           SET stock = stock - :cantidad
                           WHERE id = :producto_id";
            $stmtProd = $this->db->prepare($sqlUpdProd);

            foreach ($detalles as $item) {
                // Insert detalle
                $stmtDet->execute([
                    ':venta_id'        => $ventaId,
                    ':producto_id'     => $item['producto_id'],
                    ':cantidad'        => $item['cantidad'],
                    ':precio_unitario' => $item['precio_unitario'],
                    ':descuento'       => $item['descuento'],
                    ':subtotal'        => $item['subtotal'],
                ]);

                // Descontar del stock
                $stmtProd->execute([
                    ':cantidad'    => $item['cantidad'],
                    ':producto_id' => $item['producto_id'],
                ]);
            }

            $this->db->commit();
            return $ventaId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
