<?php

namespace Models;

use Core\Model;

/**
 * Modelo Auditoria
 */
class Auditoria extends Model {
    protected $table = 'auditoria';

    /**
     * Registrar evento de auditoría
     */
    public function registrar($usuarioId, $modulo, $accion, $tablaAfectada = null, $registroId = null, $datosAnteriores = null, $datosNuevos = null) {
        $data = [
            'usuario_id' => $usuarioId,
            'modulo' => $modulo,
            'accion' => $accion,
            'tabla_afectada' => $tablaAfectada,
            'registro_id' => $registroId,
            'ip_address' => getClientIP(),
            'user_agent' => getUserAgent()
        ];

        if ($datosAnteriores !== null) {
            $data['datos_anteriores'] = json_encode($datosAnteriores);
        }

        if ($datosNuevos !== null) {
            $data['datos_nuevos'] = json_encode($datosNuevos);
        }

        return $this->insert($data);
    }

    /**
     * Obtener auditoría por usuario
     */
    public function getByUsuario($usuarioId, $limit = 50) {
        $sql = "SELECT * FROM {$this->table} WHERE usuario_id = ? ORDER BY creado_en DESC LIMIT ?";
        return $this->query($sql, [$usuarioId, $limit]);
    }

    /**
     * Obtener auditoría por módulo
     */
    public function getByModulo($modulo, $limit = 50) {
        $sql = "SELECT * FROM {$this->table} WHERE modulo = ? ORDER BY creado_en DESC LIMIT ?";
        return $this->query($sql, [$modulo, $limit]);
    }

    /**
     * Obtener auditoría por tabla
     */
    public function getByTabla($tabla, $registroId = null, $limit = 50) {
        if ($registroId) {
            $sql = "SELECT * FROM {$this->table} WHERE tabla_afectada = ? AND registro_id = ? ORDER BY creado_en DESC LIMIT ?";
            return $this->query($sql, [$tabla, $registroId, $limit]);
        } else {
            $sql = "SELECT * FROM {$this->table} WHERE tabla_afectada = ? ORDER BY creado_en DESC LIMIT ?";
            return $this->query($sql, [$tabla, $limit]);
        }
    }
}
