<?php

namespace Models;

use Core\Model;

/**
 * Modelo Configuración
 */
class Configuracion extends Model {
    protected $table = 'configuracion';

    /**
     * Obtener valor de configuración
     */
    public function getValor($clave, $default = null) {
        $config = $this->findBy('clave', $clave);
        return $config ? $config['valor'] : $default;
    }

    /**
     * Establecer valor de configuración
     */
    public function setValor($clave, $valor, $usuarioId = null) {
        $config = $this->findBy('clave', $clave);

        if ($config) {
            // Actualizar
            return $this->update($config['id'], [
                'valor' => $valor,
                'actualizado_por' => $usuarioId ?? $_SESSION['user_id'] ?? null
            ]);
        } else {
            // Crear
            return $this->insert([
                'clave' => $clave,
                'valor' => $valor,
                'actualizado_por' => $usuarioId ?? $_SESSION['user_id'] ?? null
            ]);
        }
    }

    /**
     * Obtener todas las configuraciones
     */
    public function getAll() {
        $configs = $this->all();
        $result = [];

        foreach ($configs as $config) {
            $result[$config['clave']] = $config['valor'];
        }

        return $result;
    }

    /**
     * Obtener cuota mensual actual
     */
    public function getCuotaMensual() {
        return (float) $this->getValor('cuota_mensual', CUOTAS_CONFIG['monto_default']);
    }

    /**
     * Obtener tolerancia de meses
     */
    public function getToleranciaMeses() {
        return (int) $this->getValor('tolerancia_meses_impago', CUOTAS_CONFIG['tolerancia_meses']);
    }
}
