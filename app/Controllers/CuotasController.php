<?php

namespace Controllers;

use Core\Controller;
use Models\Cuota;
use Models\Colegiado;
use Models\Auditoria;

class CuotasController extends Controller {

    public function index() {
        $this->requireAuth();

        $cuotaModel = new Cuota();

        $cuotasPendientes = $cuotaModel->count("estado = 'PENDIENTE'");
        $cuotasVencidas = $cuotaModel->count("estado = 'VENCIDO'");
        $montoPendiente = $cuotaModel->queryOne("SELECT SUM(monto) as total FROM cuotas WHERE estado = 'PENDIENTE'");

        $this->view('cuotas.index', [
            'cuotas_pendientes' => $cuotasPendientes,
            'cuotas_vencidas' => $cuotasVencidas,
            'monto_pendiente' => $montoPendiente['total'] ?? 0
        ]);
    }

    public function generate() {
        $this->requireRole('ADMIN');
        $this->view('cuotas.generate');
    }

    public function doGenerate() {
        $this->requireRole('ADMIN');

        if (!$this->isPost()) {
            redirect(url('cuotas/generar'));
        }

        $mes = (int) $this->post('mes', date('n'));
        $anio = (int) $this->post('anio', date('Y'));

        $cuotaModel = new Cuota();
        $resultado = $cuotaModel->generarCuotasMensuales($mes, $anio);

        // Auditoría
        $auditoriaModel = new Auditoria();
        $auditoriaModel->registrar(
            $this->getUser(),
            'CUOTAS',
            'GENERAR_MENSUAL',
            'cuotas',
            null,
            null,
            ['mes' => $mes, 'anio' => $anio, 'resultado' => $resultado]
        );

        $this->setFlash('success', "Se generaron {$resultado['generadas']} cuotas de {$resultado['total_colegiados']} colegiados");
        redirect(url('cuotas'));
    }

    public function adelantar($id) {
        $this->requireAuth();

        $colegiadoModel = new Colegiado();
        $personaModel = new \Models\Persona();

        $colegiado = $colegiadoModel->find($id);

        if (!$colegiado) {
            $this->setFlash('error', 'Colegiado no encontrado');
            redirect(url('colegiados'));
        }

        $persona = $personaModel->find($colegiado['persona_id']);

        $this->view('cuotas.adelantar', [
            'colegiado' => $colegiado,
            'persona' => $persona
        ]);
    }

    public function doAdelantar($id) {
        $this->requireAuth();

        if (!$this->isPost()) {
            redirect(url("cuotas/adelantar/{$id}"));
        }

        $colegiadoModel = new Colegiado();
        $colegiado = $colegiadoModel->find($id);

        if (!$colegiado) {
            $this->setFlash('error', 'Colegiado no encontrado');
            redirect(url('colegiados'));
        }

        $meses = (int) $this->post('meses', 1);
        $cuotaModel = new Cuota();

        $generadas = 0;
        $mesActual = (int) date('n');
        $anioActual = (int) date('Y');

        for ($i = 1; $i <= $meses; $i++) {
            $mes = $mesActual + $i;
            $anio = $anioActual;

            if ($mes > 12) {
                $mes = $mes - 12;
                $anio++;
            }

            if (!$cuotaModel->existeCuota($colegiado['persona_id'], $mes, $anio)) {
                $cuotaModel->crearCuota($colegiado['persona_id'], $mes, $anio);
                $generadas++;
            }
        }

        $this->setFlash('success', "Se generaron {$generadas} cuotas adelantadas");
        redirect(url("colegiados/ver/{$id}"));
    }
}
