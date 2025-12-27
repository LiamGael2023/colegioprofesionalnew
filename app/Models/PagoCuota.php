<?php

namespace Models;

use Core\Model;

/**
 * Modelo PagoCuota - Relación N:N entre pagos y cuotas
 */
class PagoCuota extends Model {
    protected $table = 'pago_cuotas';

    /**
     * Obtener cuotas de un pago
     */
    public function getCuotasByPago($pagoId) {
        $sql = "SELECT pc.*, c.mes, c.anio, c.monto
                FROM {$this->table} pc
                INNER JOIN cuotas c ON c.id = pc.cuota_id
                WHERE pc.pago_id = ?
                ORDER BY c.anio, c.mes";
        return $this->query($sql, [$pagoId]);
    }

    /**
     * Obtener pagos de una cuota
     */
    public function getPagosByCuota($cuotaId) {
        $sql = "SELECT pc.*, p.fecha_pago, p.metodo_pago
                FROM {$this->table} pc
                INNER JOIN pagos p ON p.id = pc.pago_id
                WHERE pc.cuota_id = ?
                ORDER BY p.fecha_pago DESC";
        return $this->query($sql, [$cuotaId]);
    }
}
