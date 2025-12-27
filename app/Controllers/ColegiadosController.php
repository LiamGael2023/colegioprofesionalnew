<?php

namespace Controllers;

use Core\Controller;
use Models\Persona;
use Models\Colegiado;
use Models\Cuota;
use Models\Auditoria;

class ColegiadosController extends Controller {

    public function index() {
        $this->requireAuth();

        $colegiadoModel = new Colegiado();
        $search = $this->get('search');
        $habilitado = $this->get('habilitado');
        $page = (int) ($this->get('page') ?? 1);

        $data = $colegiadoModel->getConPersona($page, 20, $search, $habilitado);

        $this->view('colegiados.index', [
            'colegiados' => $data['items'],
            'pagination' => $data,
            'search' => $search,
            'habilitado' => $habilitado
        ]);
    }

    public function convert($id) {
        $this->requireAuth();

        $personaModel = new Persona();
        $persona = $personaModel->find($id);

        if (!$persona) {
            $this->setFlash('error', 'Persona no encontrada');
            redirect(url('personas'));
        }

        if ($persona['es_colegiado']) {
            $this->setFlash('error', 'La persona ya es colegiado');
            redirect(url('personas'));
        }

        $this->view('colegiados.convert', ['persona' => $persona]);
    }

    public function doConvert($id) {
        $this->requireAuth();

        if (!$this->isPost()) {
            redirect(url("colegiados/convertir/{$id}"));
        }

        $personaModel = new Persona();
        $persona = $personaModel->find($id);

        if (!$persona || $persona['es_colegiado']) {
            $this->setFlash('error', 'Operación no válida');
            redirect(url('personas'));
        }

        $colegiadoModel = new Colegiado();

        $dataColegiado = [
            'persona_id' => $id,
            'especialidad' => sanitize($this->post('especialidad')),
            'subespecialidad' => sanitize($this->post('subespecialidad')),
            'universidad' => sanitize($this->post('universidad')),
            'fecha_colegiatura' => $this->post('fecha_colegiatura') ?? date('Y-m-d'),
            'habilitado' => 1,
            'observaciones' => sanitize($this->post('observaciones')),
            'creado_por' => $this->getUser()
        ];

        $colegiadoId = $colegiadoModel->crear($dataColegiado);

        if ($colegiadoId) {
            // Marcar persona como colegiado
            $personaModel->marcarComoColegiado($id);

            // Auditoría
            $auditoriaModel = new Auditoria();
            $auditoriaModel->registrar($this->getUser(), 'COLEGIADOS', 'CONVERTIR', 'colegiados', $colegiadoId, null, $dataColegiado);

            $this->setFlash('success', 'Colegiado creado exitosamente');
            redirect(url('colegiados'));
        } else {
            $this->setFlash('error', 'Error al crear colegiado');
            redirect(url("colegiados/convertir/{$id}"));
        }
    }

    public function show($id) {
        $this->requireAuth();

        $colegiadoModel = new Colegiado();
        $personaModel = new Persona();
        $cuotaModel = new Cuota();

        $colegiado = $colegiadoModel->find($id);

        if (!$colegiado) {
            $this->setFlash('error', 'Colegiado no encontrado');
            redirect(url('colegiados'));
        }

        $persona = $personaModel->find($colegiado['persona_id']);
        $cuotas = $cuotaModel->getCuotasByPersona($colegiado['persona_id']);
        $resumenDeuda = $cuotaModel->getResumenDeuda($colegiado['persona_id']);

        $this->view('colegiados.show', [
            'colegiado' => $colegiado,
            'persona' => $persona,
            'cuotas' => $cuotas,
            'resumen' => $resumenDeuda
        ]);
    }

    public function updateHabilitacion() {
        $this->requireRole('ADMIN');

        if (!$this->isPost()) {
            $this->json(['error' => 'Método no permitido'], 405);
        }

        $id = $this->post('id');
        $habilitado = (int) $this->post('habilitado');

        $colegiadoModel = new Colegiado();

        if ($colegiadoModel->actualizarHabilitacion($id, $habilitado)) {
            $auditoriaModel = new Auditoria();
            $auditoriaModel->registrar(
                $this->getUser(),
                'COLEGIADOS',
                'ACTUALIZAR_HABILITACION',
                'colegiados',
                $id,
                null,
                ['habilitado' => $habilitado]
            );

            $this->json(['success' => true, 'message' => 'Habilitación actualizada']);
        } else {
            $this->json(['error' => 'Error al actualizar habilitación'], 500);
        }
    }
}
