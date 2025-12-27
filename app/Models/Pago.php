<?php

namespace Models;

use Core\Model;

/**
 * Modelo Pago
 */
class Pago extends Model {
    protected $table = 'pagos';

    /**
     * Procesar pago de cuotas
     */
    public function procesarPago($personaId, $cuotasIds, $metodoPago, $numeroOperacion = null, $observaciones = null, $cajeroId = null) {
        $this->beginTransaction();

        try {
            $cuotaModel = new Cuota();
            $montoTotal = 0;

            // Calcular monto total y verificar cuotas
            foreach ($cuotasIds as $cuotaId) {
                $cuota = $cuotaModel->find($cuotaId);

                if (!$cuota || $cuota['persona_id'] != $personaId) {
                    throw new \Exception("Cuota inválida: {$cuotaId}");
                }

                if ($cuota['estado'] !== 'PENDIENTE') {
                    throw new \Exception("La cuota {$cuotaId} ya está pagada");
                }

                $montoTotal += $cuota['monto'];
            }

            // Crear registro de pago
            $pagoId = $this->insert([
                'persona_id' => $personaId,
                'monto_total' => $montoTotal,
                'metodo_pago' => $metodoPago,
                'numero_operacion' => $numeroOperacion,
                'observaciones' => $observaciones,
                'cajero_id' => $cajeroId ?? $_SESSION['user_id']
            ]);

            // Relacionar pago con cuotas y marcar como pagadas
            $pagoCuotaModel = new PagoCuota();

            foreach ($cuotasIds as $cuotaId) {
                $cuota = $cuotaModel->find($cuotaId);

                // Crear relación pago-cuota
                $pagoCuotaModel->insert([
                    'pago_id' => $pagoId,
                    'cuota_id' => $cuotaId,
                    'monto_aplicado' => $cuota['monto']
                ]);

                // Marcar cuota como pagada
                $cuotaModel->marcarPagada($cuotaId);
            }

            // Actualizar meses impagos del colegiado
            $this->actualizarMesesImpagos($personaId);

            // Generar recibo
            $reciboModel = new Recibo();
            $reciboId = $reciboModel->generar($pagoId);

            $this->commit();

            return [
                'success' => true,
                'pago_id' => $pagoId,
                'recibo_id' => $reciboId,
                'monto_total' => $montoTotal
            ];

        } catch (\Exception $e) {
            $this->rollback();
            logger("Error procesando pago: " . $e->getMessage(), 'ERROR', 'pagos.log');
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar meses impagos del colegiado
     */
    private function actualizarMesesImpagos($personaId) {
        $cuotaModel = new Cuota();
        $colegiadoModel = new Colegiado();

        $pendientes = $cuotaModel->count("persona_id = ? AND estado = 'PENDIENTE'", [$personaId]);

        $colegiado = $colegiadoModel->getByPersonaId($personaId);
        if ($colegiado) {
            $colegiadoModel->actualizarMesesImpagos($colegiado['id'], $pendientes);
        }
    }

    /**
     * Obtener últimos pagos
     */
    public function getUltimos($limit = 10) {
        $sql = "SELECT p.*, per.dni, per.apellido_paterno, per.apellido_materno, per.nombres,
                u.nombre_completo as cajero
                FROM {$this->table} p
                INNER JOIN personas per ON per.id = p.persona_id
                INNER JOIN usuarios u ON u.id = p.cajero_id
                ORDER BY p.fecha_pago DESC
                LIMIT ?";
        return $this->query($sql, [$limit]);
    }

    /**
     * Obtener recaudación del mes actual
     */
    public function getRecaudacionMes() {
        $sql = "SELECT COALESCE(SUM(monto_total), 0) as total
                FROM {$this->table}
                WHERE MONTH(fecha_pago) = MONTH(CURDATE())
                AND YEAR(fecha_pago) = YEAR(CURDATE())";
        $result = $this->queryOne($sql);
        return $result['total'] ?? 0;
    }

    /**
     * Obtener recaudación de hoy
     */
    public function getRecaudacionHoy() {
        $sql = "SELECT COALESCE(SUM(monto_total), 0) as total
                FROM {$this->table}
                WHERE DATE(fecha_pago) = CURDATE()";
        $result = $this->queryOne($sql);
        return $result['total'] ?? 0;
    }

    /**
     * Obtener recaudación por rango de fechas
     */
    public function getRecaudacionPorRango($desde, $hasta) {
        $sql = "SELECT DATE(fecha_pago) as fecha, SUM(monto_total) as total, COUNT(*) as cantidad
                FROM {$this->table}
                WHERE DATE(fecha_pago) BETWEEN ? AND ?
                GROUP BY DATE(fecha_pago)
                ORDER BY fecha DESC";
        return $this->query($sql, [$desde, $hasta]);
    }

    /**
     * Obtener pagos de una persona
     */
    public function getByPersona($personaId) {
        $sql = "SELECT p.*, u.nombre_completo as cajero
                FROM {$this->table} p
                INNER JOIN usuarios u ON u.id = p.cajero_id
                WHERE p.persona_id = ?
                ORDER BY p.fecha_pago DESC";
        return $this->query($sql, [$personaId]);
    }

    /**
     * Obtener pago con detalles completos
     */
    public function getConDetalles($id) {
        $sql = "SELECT p.*, per.dni, per.apellido_paterno, per.apellido_materno, per.nombres,
                per.direccion, per.email, per.celular,
                u.nombre_completo as cajero
                FROM {$this->table} p
                INNER JOIN personas per ON per.id = p.persona_id
                INNER JOIN usuarios u ON u.id = p.cajero_id
                WHERE p.id = ?";
        return $this->queryOne($sql, [$id]);
    }
}
