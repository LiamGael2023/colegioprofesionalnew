<?php

namespace Models;

use Core\Model;

/**
 * Modelo Colegiado
 */
class Colegiado extends Model {
    protected $table = 'colegiados';

    /**
     * Crear colegiado
     */
    public function crear($data) {
        // Generar número de colegiatura
        if (!isset($data['numero_colegiatura'])) {
            $data['numero_colegiatura'] = $this->generarNumeroColegiatura();
        }

        return $this->insert($data);
    }

    /**
     * Generar número de colegiatura único
     */
    private function generarNumeroColegiatura() {
        $year = date('Y');

        // Obtener el último número del año
        $sql = "SELECT numero_colegiatura FROM {$this->table}
                WHERE numero_colegiatura LIKE ?
                ORDER BY numero_colegiatura DESC LIMIT 1";

        $result = $this->queryOne($sql, ["{$year}%"]);

        if ($result) {
            // Extraer el correlativo y sumar 1
            $parts = explode('-', $result['numero_colegiatura']);
            $correlativo = intval($parts[1]) + 1;
        } else {
            $correlativo = 1;
        }

        return sprintf('%s-%04d', $year, $correlativo);
    }

    /**
     * Obtener por persona ID
     */
    public function getByPersonaId($personaId) {
        return $this->findBy('persona_id', $personaId);
    }

    /**
     * Obtener habilitados
     */
    public function getHabilitados() {
        return $this->where('habilitado', 1);
    }

    /**
     * Obtener inhabilitados
     */
    public function getInhabilitados() {
        return $this->where('habilitado', 0);
    }

    /**
     * Actualizar estado de habilitación
     */
    public function actualizarHabilitacion($id, $habilitado) {
        return $this->update($id, ['habilitado' => $habilitado]);
    }

    /**
     * Actualizar meses impagos
     */
    public function actualizarMesesImpagos($id, $meses) {
        $data = ['meses_impagos' => $meses];

        // Si supera la tolerancia, inhabilitar
        if ($meses > CUOTAS_CONFIG['tolerancia_meses']) {
            $data['habilitado'] = 0;
        } else {
            $data['habilitado'] = 1;
        }

        return $this->update($id, $data);
    }

    /**
     * Obtener colegiados con información completa
     */
    public function getConPersona($page = 1, $perPage = 20, $search = null, $soloHabilitados = null) {
        $where = "1=1";
        $params = [];

        if ($search) {
            $searchParam = "%{$search}%";
            $where .= " AND (p.dni LIKE ? OR p.nombres LIKE ? OR p.apellido_paterno LIKE ? OR p.apellido_materno LIKE ? OR c.numero_colegiatura LIKE ?)";
            $params = [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam];
        }

        if ($soloHabilitados !== null) {
            $where .= " AND c.habilitado = ?";
            $params[] = $soloHabilitados;
        }

        $offset = ($page - 1) * $perPage;

        $sql = "SELECT c.*, p.dni, p.apellido_paterno, p.apellido_materno, p.nombres,
                p.email, p.celular, p.telefono
                FROM {$this->table} c
                INNER JOIN personas p ON p.id = c.persona_id
                WHERE {$where}
                ORDER BY c.numero_colegiatura DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $items = $this->query($sql, $params);

        $total = $this->count($where, $params);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

    /**
     * Obtener deudores (más meses impagos)
     */
    public function getDeudores($limit = 10) {
        $sql = "SELECT c.*, p.dni, p.apellido_paterno, p.apellido_materno, p.nombres
                FROM {$this->table} c
                INNER JOIN personas p ON p.id = c.persona_id
                WHERE c.meses_impagos > 0
                ORDER BY c.meses_impagos DESC, p.apellido_paterno
                LIMIT ?";
        return $this->query($sql, [$limit]);
    }

    /**
     * Verificar si número de colegiatura existe
     */
    public function numeroColegiatura Exists($numero, $excludeId = null) {
        return $this->exists('numero_colegiatura', $numero, $excludeId);
    }
}
