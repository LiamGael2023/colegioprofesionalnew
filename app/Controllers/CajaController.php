<?php

namespace Controllers;

use Core\Controller;
use Models\Persona;
use Models\Cuota;
use Models\Pago;
use Models\Recibo;
use Models\PagoCuota;
use Models\Auditoria;

class CajaController extends Controller {

    public function index() {
        $this->requireAuth();
        $this->view('caja.index');
    }

    public function search() {
        $this->requireAuth();

        if (!$this->isPost()) {
            redirect(url('caja'));
        }

        $dni = sanitize($this->post('dni'));

        $personaModel = new Persona();
        $persona = $personaModel->findByDni($dni);

        if (!$persona) {
            $this->setFlash('error', 'Persona no encontrada');
            redirect(url('caja'));
        }

        // Obtener cuotas pendientes
        $cuotaModel = new Cuota();
        $cuotasPendientes = $cuotaModel->getCuotasPendientes($persona['id']);
        $resumenDeuda = $cuotaModel->getResumenDeuda($persona['id']);

        // Obtener historial de pagos
        $pagoModel = new Pago();
        $historialPagos = $pagoModel->getByPersona($persona['id']);

        $this->view('caja.index', [
            'persona' => $persona,
            'cuotas_pendientes' => $cuotasPendientes,
            'resumen_deuda' => $resumenDeuda,
            'historial_pagos' => $historialPagos
        ]);
    }

    public function procesarPago() {
        $this->requireAuth();

        if (!$this->isPost()) {
            $this->json(['error' => 'Método no permitido'], 405);
        }

        $personaId = (int) $this->post('persona_id');
        $cuotasIds = $this->post('cuotas_ids', []);
        $metodoPago = sanitize($this->post('metodo_pago'));
        $numeroOperacion = sanitize($this->post('numero_operacion'));
        $observaciones = sanitize($this->post('observaciones'));

        if (empty($cuotasIds)) {
            $this->json(['error' => 'Debe seleccionar al menos una cuota'], 400);
        }

        $pagoModel = new Pago();
        $resultado = $pagoModel->procesarPago(
            $personaId,
            $cuotasIds,
            $metodoPago,
            $numeroOperacion,
            $observaciones,
            $this->getUser()
        );

        if ($resultado['success']) {
            // Auditoría
            $auditoriaModel = new Auditoria();
            $auditoriaModel->registrar(
                $this->getUser(),
                'CAJA',
                'PROCESAR_PAGO',
                'pagos',
                $resultado['pago_id'],
                null,
                $resultado
            );

            $this->json([
                'success' => true,
                'message' => 'Pago procesado exitosamente',
                'recibo_id' => $resultado['recibo_id'],
                'pago_id' => $resultado['pago_id']
            ]);
        } else {
            $this->json(['error' => $resultado['error']], 500);
        }
    }

    public function verRecibo($id) {
        $this->requireAuth();

        $reciboModel = new Recibo();
        $recibo = $reciboModel->getConDetalles($id);

        if (!$recibo) {
            $this->setFlash('error', 'Recibo no encontrado');
            redirect(url('caja'));
        }

        // Obtener cuotas del pago
        $pagoCuotaModel = new PagoCuota();
        $cuotas = $pagoCuotaModel->getCuotasByPago($recibo['pago_id']);

        // Obtener configuración del colegio
        $configuracionModel = new \Models\Configuracion();
        $config = $configuracionModel->getAll();

        $this->view('caja.recibo', [
            'recibo' => $recibo,
            'cuotas' => $cuotas,
            'config' => $config
        ]);
    }
}
