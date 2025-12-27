<?php

namespace Models;

use Core\Model;

/**
 * Modelo Persona
 */
class Persona extends Model {
    protected $table = 'personas';

    /**
     * Buscar persona por DNI
     */
    public function findByDni($dni) {
        return $this->findBy('dni', $dni);
    }

    /**
     * Buscar personas por nombre
     */
    public function searchByName($search) {
        $search = "%{$search}%";
        $sql = "SELECT * FROM {$this->table}
                WHERE nombres LIKE ?
                OR apellido_paterno LIKE ?
                OR apellido_materno LIKE ?
                ORDER BY apellido_paterno, apellido_materno, nombres";
        return $this->query($sql, [$search, $search, $search]);
    }

    /**
     * Verificar si DNI existe
     */
    public function dniExists($dni, $excludeId = null) {
        return $this->exists('dni', $dni, $excludeId);
    }

    /**
     * Obtener personas que son colegiados
     */
    public function getColegiados() {
        return $this->where('es_colegiado', 1);
    }

    /**
     * Obtener personas que NO son colegiados
     */
    public function getNoColegiados() {
        return $this->where('es_colegiado', 0);
    }

    /**
     * Marcar como colegiado
     */
    public function marcarComoColegiado($id) {
        return $this->update($id, ['es_colegiado' => 1]);
    }

    /**
     * Obtener nombre completo
     */
    public static function getNombreCompleto($persona) {
        return trim("{$persona['apellido_paterno']} {$persona['apellido_materno']}, {$persona['nombres']}");
    }

    /**
     * Obtener con información de colegiado
     */
    public function getWithColegiado($id) {
        $sql = "SELECT p.*, c.numero_colegiatura, c.especialidad, c.subespecialidad,
                c.universidad, c.fecha_colegiatura, c.habilitado, c.meses_impagos
                FROM {$this->table} p
                LEFT JOIN colegiados c ON c.persona_id = p.id
                WHERE p.id = ?";
        return $this->queryOne($sql, [$id]);
    }

    /**
     * Listar todas con paginación
     */
    public function listar($page = 1, $perPage = 20, $search = null) {
        $where = null;
        $params = [];

        if ($search) {
            $searchParam = "%{$search}%";
            $where = "dni LIKE ? OR nombres LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ?";
            $params = [$searchParam, $searchParam, $searchParam, $searchParam];
        }

        return $this->paginate($page, $perPage, $where, $params);
    }
}
