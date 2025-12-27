<?php

namespace Controllers;

use Core\Controller;
use Models\Colegiado;
use Models\Cuota;
use Models\Pago;
use Models\Persona;

class ReportesController extends Controller {

    public function index() {
        $this->requireAuth();
        $this->view('reportes.index');
    }

    public function deudores() {
        $this->requireAuth();

        $colegiadoModel = new Colegiado();
        $cuotaModel = new Cuota();

        // Obtener todos los colegiados con deudas
        $sql = "SELECT p.id, p.dni, p.apellido_paterno, p.apellido_materno, p.nombres,
                c.numero_colegiatura, c.meses_impagos, c.habilitado,
                COUNT(cu.id) as cuotas_pendientes,
                SUM(cu.monto) as monto_total
                FROM personas p
                INNER JOIN colegiados c ON c.persona_id = p.id
                LEFT JOIN cuotas cu ON cu.persona_id = p.id AND cu.estado = 'PENDIENTE'
                GROUP BY p.id
                HAVING cuotas_pendientes > 0
                ORDER BY monto_total DESC, c.meses_impagos DESC";

        $deudores = $colegiadoModel->query($sql);

        $this->view('reportes.deudores', ['deudores' => $deudores]);
    }

    public function recaudacion() {
        $this->requireAuth();

        $desde = $this->get('desde', date('Y-m-01'));
        $hasta = $this->get('hasta', date('Y-m-d'));

        $pagoModel = new Pago();
        $recaudacion = $pagoModel->getRecaudacionPorRango($desde, $hasta);

        $total = array_sum(array_column($recaudacion, 'total'));

        $this->view('reportes.recaudacion', [
            'recaudacion' => $recaudacion,
            'desde' => $desde,
            'hasta' => $hasta,
            'total' => $total
        ]);
    }

    public function estadoCuenta($id) {
        $this->requireAuth();

        $personaModel = new Persona();
        $cuotaModel = new Cuota();
        $pagoModel = new Pago();
        $colegiadoModel = new Colegiado();

        $persona = $personaModel->find($id);

        if (!$persona) {
            $this->setFlash('error', 'Persona no encontrada');
            redirect(url('reportes'));
        }

        $colegiado = $colegiadoModel->getByPersonaId($id);
        $cuotas = $cuotaModel->getCuotasByPersona($id);
        $pagos = $pagoModel->getByPersona($id);
        $resumenDeuda = $cuotaModel->getResumenDeuda($id);

        $this->view('reportes.estado-cuenta', [
            'persona' => $persona,
            'colegiado' => $colegiado,
            'cuotas' => $cuotas,
            'pagos' => $pagos,
            'resumen' => $resumenDeuda
        ]);
    }
}
