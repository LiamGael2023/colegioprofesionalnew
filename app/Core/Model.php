<?php

namespace Core;

use PDO;

/**
 * Clase Model - Modelo base
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todos los registros
     */
    public function all($columns = '*', $orderBy = null) {
        $sql = "SELECT {$columns} FROM {$this->table}";

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }

        return $this->db->query($sql);
    }

    /**
     * Encontrar registro por ID
     */
    public function find($id, $columns = '*') {
        $sql = "SELECT {$columns} FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->queryOne($sql, [$id]);
    }

    /**
     * Encontrar registro por condición
     */
    public function findBy($field, $value, $columns = '*') {
        $sql = "SELECT {$columns} FROM {$this->table} WHERE {$field} = ? LIMIT 1";
        return $this->db->queryOne($sql, [$value]);
    }

    /**
     * Obtener registros por condición
     */
    public function where($field, $value, $columns = '*') {
        $sql = "SELECT {$columns} FROM {$this->table} WHERE {$field} = ?";
        return $this->db->query($sql, [$value]);
    }

    /**
     * Insertar registro
     */
    public function insert($data) {
        $fields = array_keys($data);
        $values = array_values($data);

        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $fieldsList = implode(', ', $fields);

        $sql = "INSERT INTO {$this->table} ({$fieldsList}) VALUES ({$placeholders})";

        if ($this->db->execute($sql, $values)) {
            return $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Actualizar registro
     */
    public function update($id, $data) {
        $fields = [];
        $values = [];

        foreach ($data as $field => $value) {
            $fields[] = "{$field} = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $fieldsList = implode(', ', $fields);
        $sql = "UPDATE {$this->table} SET {$fieldsList} WHERE {$this->primaryKey} = ?";

        return $this->db->execute($sql, $values);
    }

    /**
     * Eliminar registro
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, [$id]);
    }

    /**
     * Contar registros
     */
    public function count($where = null, $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";

        if ($where) {
            $sql .= " WHERE {$where}";
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] ?? 0;
    }

    /**
     * Ejecutar query personalizado
     */
    public function query($sql, $params = []) {
        return $this->db->query($sql, $params);
    }

    /**
     * Ejecutar query y retornar una fila
     */
    public function queryOne($sql, $params = []) {
        return $this->db->queryOne($sql, $params);
    }

    /**
     * Ejecutar query de modificación
     */
    public function execute($sql, $params = []) {
        return $this->db->execute($sql, $params);
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     * Revertir transacción
     */
    public function rollback() {
        return $this->db->rollback();
    }

    /**
     * Verificar si existe un registro
     */
    public function exists($field, $value, $excludeId = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$field} = ?";
        $params = [$value];

        if ($excludeId) {
            $sql .= " AND {$this->primaryKey} != ?";
            $params[] = $excludeId;
        }

        $result = $this->db->queryOne($sql, $params);
        return $result['total'] > 0;
    }

    /**
     * Paginación
     */
    public function paginate($page = 1, $perPage = 10, $where = null, $params = []) {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM {$this->table}";

        if ($where) {
            $sql .= " WHERE {$where}";
        }

        $sql .= " LIMIT {$perPage} OFFSET {$offset}";

        $items = $this->db->query($sql, $params);
        $total = $this->count($where, $params);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
}
