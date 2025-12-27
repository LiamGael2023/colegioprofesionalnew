<?php

namespace Models;

use Core\Model;

/**
 * Modelo Recibo
 */
class Recibo extends Model {
    protected $table = 'recibos';

    /**
     * Generar recibo para un pago
     */
    public function generar($pagoId) {
        // Obtener configuración de serie y correlativo
        $configuracionModel = new Configuracion();
        $serie = $configuracionModel->getValor('serie_recibo', 'R001');
        $correlativo = (int) $configuracionModel->getValor('correlativo_recibo', 1);

        // Generar número de recibo
        $numeroRecibo = sprintf('%s-%08d', $serie, $correlativo);

        // Insertar recibo
        $reciboId = $this->insert([
            'numero_recibo' => $numeroRecibo,
            'pago_id' => $pagoId,
            'serie' => $serie,
            'correlativo' => $correlativo
        ]);

        // Actualizar correlativo
        $configuracionModel->setValor('correlativo_recibo', $correlativo + 1);

        return $reciboId;
    }

    /**
     * Obtener recibo por pago ID
     */
    public function getByPagoId($pagoId) {
        return $this->findBy('pago_id', $pagoId);
    }

    /**
     * Obtener recibo con detalles completos
     */
    public function getConDetalles($id) {
        $sql = "SELECT r.*, p.*, per.dni, per.apellido_paterno, per.apellido_materno,
                per.nombres, per.direccion, per.email,
                u.nombre_completo as cajero
                FROM {$this->table} r
                INNER JOIN pagos p ON p.id = r.pago_id
                INNER JOIN personas per ON per.id = p.persona_id
                INNER JOIN usuarios u ON u.id = p.cajero_id
                WHERE r.id = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Buscar recibo por número
     */
    public function buscarPorNumero($numeroRecibo) {
        return $this->findBy('numero_recibo', $numeroRecibo);
    }
}
