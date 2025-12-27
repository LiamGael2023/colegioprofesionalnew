<?php

namespace Models;

use Core\Model;

/**
 * Modelo Cuota
 */
class Cuota extends Model {
    protected $table = 'cuotas';

    /**
     * Generar cuotas mensuales para todos los colegiados
     */
    public function generarCuotasMensuales($mes = null, $anio = null) {
        if (!$mes) $mes = date('n');
        if (!$anio) $anio = date('Y');

        // Obtener todos los colegiados activos
        $colegiadoModel = new Colegiado();
        $sql = "SELECT persona_id FROM colegiados";
        $colegiados = $this->query($sql);

        $generadas = 0;
        $errores = 0;

        foreach ($colegiados as $colegiado) {
            try {
                // Verificar si ya existe la cuota
                if (!$this->existeCuota($colegiado['persona_id'], $mes, $anio)) {
                    $this->crearCuota($colegiado['persona_id'], $mes, $anio);
                    $generadas++;
                }
            } catch (\Exception $e) {
                $errores++;
                logger("Error generando cuota para persona {$colegiado['persona_id']}: " . $e->getMessage(), 'ERROR', 'cuotas.log');
            }
        }

        return [
            'generadas' => $generadas,
            'errores' => $errores,
            'total_colegiados' => count($colegiados)
        ];
    }

    /**
     * Crear cuota individual
     */
    public function crearCuota($personaId, $mes, $anio, $monto = null) {
        if (!$monto) {
            $monto = CUOTAS_CONFIG['monto_default'];
        }

        // Calcular fecha de vencimiento (último día del mes)
        $fechaVencimiento = date('Y-m-t', strtotime("{$anio}-{$mes}-01"));

        return $this->insert([
            'persona_id' => $personaId,
            'mes' => $mes,
            'anio' => $anio,
            'monto' => $monto,
            'estado' => 'PENDIENTE',
            'fecha_vencimiento' => $fechaVencimiento
        ]);
    }

    /**
     * Verificar si existe cuota
     */
    public function existeCuota($personaId, $mes, $anio) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE persona_id = ? AND mes = ? AND anio = ?";
        $result = $this->queryOne($sql, [$personaId, $mes, $anio]);
        return $result['total'] > 0;
    }

    /**
     * Obtener cuotas pendientes de una persona
     */
    public function getCuotasPendientes($personaId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE persona_id = ? AND estado = 'PENDIENTE'
                ORDER BY anio ASC, mes ASC";
        return $this->query($sql, [$personaId]);
    }

    /**
     * Obtener cuotas vencidas
     */
    public function getCuotasVencidas() {
        $sql = "SELECT c.*, p.dni, p.apellido_paterno, p.apellido_materno, p.nombres
                FROM {$this->table} c
                INNER JOIN personas p ON p.id = c.persona_id
                WHERE c.estado = 'PENDIENTE' AND c.fecha_vencimiento < CURDATE()
                ORDER BY c.fecha_vencimiento ASC";
        return $this->query($sql);
    }

    /**
     * Marcar cuota como pagada
     */
    public function marcarPagada($id, $fechaPago = null) {
        if (!$fechaPago) {
            $fechaPago = date('Y-m-d');
        }

        return $this->update($id, [
            'estado' => 'PAGADO',
            'fecha_pago' => $fechaPago
        ]);
    }

    /**
     * Marcar cuotas vencidas
     */
    public function marcarVencidas() {
        $sql = "UPDATE {$this->table}
                SET estado = 'VENCIDO'
                WHERE estado = 'PENDIENTE' AND fecha_vencimiento < CURDATE()";
        return $this->execute($sql);
    }

    /**
     * Obtener cuotas por persona con detalles
     */
    public function getCuotasByPersona($personaId) {
        $sql = "SELECT * FROM {$this->table}
                WHERE persona_id = ?
                ORDER BY anio DESC, mes DESC";
        return $this->query($sql, [$personaId]);
    }

    /**
     * Obtener resumen de deuda de una persona
     */
    public function getResumenDeuda($personaId) {
        $sql = "SELECT
                COUNT(*) as total_pendientes,
                SUM(monto) as monto_total,
                MIN(CONCAT(anio, '-', LPAD(mes, 2, '0'), '-01')) as desde,
                MAX(CONCAT(anio, '-', LPAD(mes, 2, '0'), '-01')) as hasta
                FROM {$this->table}
                WHERE persona_id = ? AND estado = 'PENDIENTE'";
        return $this->queryOne($sql, [$personaId]);
    }

    /**
     * Obtener nombre del mes
     */
    public static function getNombreMes($mes) {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $meses[$mes] ?? '';
    }
}
