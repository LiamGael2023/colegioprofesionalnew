<?php

namespace Controllers;

use Core\Controller;
use Models\Persona;
use Models\Colegiado;
use Models\Cuota;
use Models\Pago;

/**
 * Controlador Home - Dashboard
 */
class HomeController extends Controller {

    /**
     * Dashboard principal
     */
    public function index() {
        // Verificar autenticación
        $this->requireAuth();

        // Obtener estadísticas
        $personaModel = new Persona();
        $colegiadoModel = new Colegiado();
        $cuotaModel = new Cuota();
        $pagoModel = new Pago();

        $stats = [
            'total_personas' => $personaModel->count(),
            'total_colegiados' => $colegiadoModel->count(),
            'colegiados_habilitados' => $colegiadoModel->count('habilitado = 1'),
            'colegiados_inhabilitados' => $colegiadoModel->count('habilitado = 0'),
            'cuotas_pendientes' => $cuotaModel->count("estado = 'PENDIENTE'"),
            'cuotas_vencidas' => $cuotaModel->count("estado = 'VENCIDO'"),
            'recaudacion_mes' => $pagoModel->getRecaudacionMes(),
            'recaudacion_hoy' => $pagoModel->getRecaudacionHoy(),
        ];

        // Obtener últimos pagos
        $ultimos_pagos = $pagoModel->getUltimos(10);

        // Obtener colegiados con más deuda
        $deudores = $colegiadoModel->getDeudores(10);

        $this->view('home.index', [
            'stats' => $stats,
            'ultimos_pagos' => $ultimos_pagos,
            'deudores' => $deudores
        ]);
    }
}
