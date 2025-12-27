<?php

namespace Core;

use PDO;
use PDOException;

/**
 * Clase Database - Singleton para conexión a base de datos
 */
class Database {
    private static $instance = null;
    private $connection = null;

    /**
     * Constructor privado (Singleton)
     */
    private function __construct() {
        $this->connect();
    }

    /**
     * Obtener instancia única de Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Conectar a la base de datos
     */
    private function connect() {
        try {
            $config = DB_CONFIG;
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";

            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );

        } catch (PDOException $e) {
            logger('Error de conexión a base de datos: ' . $e->getMessage(), 'ERROR', 'database.log');
            die('Error de conexión a la base de datos');
        }
    }

    /**
     * Obtener conexión PDO
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Ejecutar query SELECT
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            logger("Error en query: {$sql} - " . $e->getMessage(), 'ERROR', 'database.log');
            throw $e;
        }
    }

    /**
     * Ejecutar query y retornar una sola fila
     */
    public function queryOne($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            logger("Error en queryOne: {$sql} - " . $e->getMessage(), 'ERROR', 'database.log');
            throw $e;
        }
    }

    /**
     * Ejecutar INSERT, UPDATE, DELETE
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute($params);
            return $result;
        } catch (PDOException $e) {
            logger("Error en execute: {$sql} - " . $e->getMessage(), 'ERROR', 'database.log');
            throw $e;
        }
    }

    /**
     * Obtener último ID insertado
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    /**
     * Obtener número de filas afectadas
     */
    public function rowCount($stmt) {
        return $stmt->rowCount();
    }

    /**
     * Iniciar transacción
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * Revertir transacción
     */
    public function rollback() {
        return $this->connection->rollBack();
    }

    /**
     * Prevenir clonación (Singleton)
     */
    private function __clone() {}

    /**
     * Prevenir deserialización (Singleton)
     */
    public function __wakeup() {
        throw new \Exception("No se puede deserializar un singleton");
    }
}
