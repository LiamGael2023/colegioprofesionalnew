<?php

namespace Models;

use Core\Model;

/**
 * Modelo Usuario
 */
class Usuario extends Model {
    protected $table = 'usuarios';

    /**
     * Autenticar usuario
     */
    public function authenticate($username, $password) {
        $user = $this->findBy('username', $username);

        if (!$user) {
            return false;
        }

        // Verificar contraseña
        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Verificar que está activo
        if (!$user['activo']) {
            return false;
        }

        // Actualizar último acceso
        $this->update($user['id'], [
            'ultimo_acceso' => date('Y-m-d H:i:s')
        ]);

        return $user;
    }

    /**
     * Crear usuario
     */
    public function create($data) {
        // Hashear password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], SECURITY_CONFIG['password_algo'], [
                'cost' => SECURITY_CONFIG['password_cost']
            ]);
        }

        return $this->insert($data);
    }

    /**
     * Actualizar usuario
     */
    public function updateUser($id, $data) {
        // Hashear password si se está actualizando
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], SECURITY_CONFIG['password_algo'], [
                'cost' => SECURITY_CONFIG['password_cost']
            ]);
        } else {
            unset($data['password']);
        }

        return $this->update($id, $data);
    }

    /**
     * Verificar si username existe
     */
    public function usernameExists($username, $excludeId = null) {
        return $this->exists('username', $username, $excludeId);
    }

    /**
     * Verificar si email existe
     */
    public function emailExists($email, $excludeId = null) {
        return $this->exists('email', $email, $excludeId);
    }

    /**
     * Obtener usuarios activos
     */
    public function getActivos() {
        return $this->where('activo', 1);
    }

    /**
     * Obtener usuarios por rol
     */
    public function getByRol($rol) {
        return $this->where('rol', $rol);
    }
}
