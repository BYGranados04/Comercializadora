<?php

class ComprasModel extends Model
{
    /** @var PDO */
    private $db;

    private string $tableCompras = "compras";
    private string $tableDetalle = "compras_detalle";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Listado simple de compras (para la tabla del módulo).
     */
    public function listar(int $limite = 50): array
    {
        // Alias numero_factura como numero_doc para que las vistas sigan funcionando
        $sql = "SELECT
                    id,
                    fecha,
                    numero_factura AS numero_doc,
                    total,
                    estado,
                    proveedor_id,
                    usuario_id
                FROM {$this->tableCompras}
                ORDER BY id DESC
                LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una compra con su detalle.
     */
    public function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT *
                FROM {$this->tableCompras}
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $compra = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$compra) {
            return null;
        }

        $sqlDet = "SELECT d.*, p.nombre AS producto_nombre
                   FROM {$this->tableDetalle} d
                   JOIN productos p ON p.id = d.producto_id
                   WHERE d.compra_id = :id";

        $stmtDet = $this->db->prepare($sqlDet);
        $stmtDet->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtDet->execute();
        $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

        $compra['detalles'] = $detalles;
        return $compra;
    }

    /**
     * Crea la compra + detalle y actualiza inventario.
     *
     * $header: datos de encabezado que vienen del controlador:
     *   proveedor_id, usuario_id, fecha_compra, numero_doc,
     *   total_bruto, descuento, total_neto, estado, notas (opcional),
     *   iva (opcional), serie_factura (opcional)
     *
     * $detalles: arreglo de renglones:
     *   [producto_id, cantidad, costo_unitario, descuento, subtotal]
     */
    public function crearCompra(array $header, array $detalles): int
    {
        try {
            $this->db->beginTransaction();

            // Normalización de datos de encabezado según su esquema real
            $proveedorId   = (int)$header['proveedor_id'];
            $usuarioId     = (int)$header['usuario_id'];
            $fecha         = $header['fecha_compra'];                 // DATE (Y-m-d)
            $serieFactura  = $header['serie_factura'] ?? '';          // opcional
            $numeroFactura = $header['numero_doc'] ?? '';             // viene del form
            $subtotal      = (float)$header['total_bruto'];           // se mapea a columna subtotal
            $descuentoCab  = (float)($header['descuento'] ?? 0);      // solo para lógica interna
            $iva           = (float)($header['iva'] ?? 0);            // si no se manda, 0
            $totalNeto     = (float)$header['total_neto'];            // se mapea a columna total
            $estado        = $header['estado'] ?? 'REGISTRADA';
            // $notas       = $header['notas'] ?? '';                 // NO hay columna notas en la tabla

            // Insert encabezado en la tabla compras
            $sql = "INSERT INTO {$this->tableCompras} (
                        proveedor_id,
                        usuario_id,
                        fecha,
                        serie_factura,
                        numero_factura,
                        subtotal,
                        iva,
                        total,
                        estado
                    ) VALUES (
                        :proveedor_id,
                        :usuario_id,
                        :fecha,
                        :serie_factura,
                        :numero_factura,
                        :subtotal,
                        :iva,
                        :total,
                        :estado
                    )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':proveedor_id'   => $proveedorId,
                ':usuario_id'     => $usuarioId,
                ':fecha'          => $fecha,
                ':serie_factura'  => $serieFactura,
                ':numero_factura' => $numeroFactura,
                ':subtotal'       => $subtotal,
                ':iva'            => $iva,
                ':total'          => $totalNeto,
                ':estado'         => $estado,
            ]);

            $compraId = (int)$this->db->lastInsertId();

            // Insert detalle + actualizar inventario
            $sqlDet = "INSERT INTO {$this->tableDetalle} (
                           compra_id,
                           producto_id,
                           cantidad,
                           costo_unitario,
                           descuento,
                           subtotal
                       ) VALUES (
                           :compra_id,
                           :producto_id,
                           :cantidad,
                           :costo_unitario,
                           :descuento,
                           :subtotal
                       )";

            $stmtDet = $this->db->prepare($sqlDet);

            // OJO: aquí uso 'costo' (coincide con su form de productos)
            $sqlUpdProd = "UPDATE productos
               SET stock = stock + :cantidad,
                   costo_actual = :costo_unitario
               WHERE id = :producto_id";

            $stmtProd = $this->db->prepare($sqlUpdProd);

            foreach ($detalles as $item) {
                $productoId    = (int)$item['producto_id'];
                $cantidad      = (float)$item['cantidad'];
                $costoUnitario = (float)$item['costo_unitario'];
                $descuentoDet  = (float)($item['descuento'] ?? 0);
                $subtotalDet   = (float)$item['subtotal'];

                // saltar renglones vacíos
                if ($productoId <= 0 || $cantidad <= 0) {
                    continue;
                }

                // Insert detalle
                $stmtDet->execute([
                    ':compra_id'      => $compraId,
                    ':producto_id'    => $productoId,
                    ':cantidad'       => $cantidad,
                    ':costo_unitario' => $costoUnitario,
                    ':descuento'      => $descuentoDet,
                    ':subtotal'       => $subtotalDet,
                ]);

                // Actualizar producto (stock y costo)
                $stmtProd->execute([
                    ':cantidad'       => $cantidad,
                    ':costo_unitario' => $costoUnitario,
                    ':producto_id'    => $productoId,
                ]);
            }

            $this->db->commit();
            return $compraId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
