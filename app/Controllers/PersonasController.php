<?php

namespace Controllers;

use Core\Controller;
use Models\Persona;
use Models\Auditoria;

class PersonasController extends Controller {

    public function index() {
        $this->requireAuth();

        $personaModel = new Persona();
        $search = $this->get('search');
        $page = (int) ($this->get('page') ?? 1);

        $data = $personaModel->listar($page, 20, $search);

        $this->view('personas.index', [
            'personas' => $data['items'],
            'pagination' => $data,
            'search' => $search
        ]);
    }

    public function create() {
        $this->requireAuth();
        $this->view('personas.create');
    }

    public function store() {
        $this->requireAuth();

        if (!$this->isPost()) {
            redirect(url('personas/crear'));
        }

        $data = [
            'dni' => sanitize($this->post('dni')),
            'apellido_paterno' => sanitize($this->post('apellido_paterno')),
            'apellido_materno' => sanitize($this->post('apellido_materno')),
            'nombres' => sanitize($this->post('nombres')),
            'fecha_nacimiento' => $this->post('fecha_nacimiento'),
            'sexo' => $this->post('sexo'),
            'direccion' => sanitize($this->post('direccion')),
            'distrito' => sanitize($this->post('distrito')),
            'provincia' => sanitize($this->post('provincia')),
            'departamento' => sanitize($this->post('departamento')),
            'telefono' => sanitize($this->post('telefono')),
            'celular' => sanitize($this->post('celular')),
            'email' => sanitize($this->post('email')),
            'creado_por' => $this->getUser()
        ];

        $personaModel = new Persona();

        // Validar DNI único
        if ($personaModel->dniExists($data['dni'])) {
            $this->setFlash('error', 'El DNI ya está registrado');
            redirect(url('personas/crear'));
        }

        $id = $personaModel->insert($data);

        if ($id) {
            // Auditoría
            $auditoriaModel = new Auditoria();
            $auditoriaModel->registrar($this->getUser(), 'PERSONAS', 'CREAR', 'personas', $id, null, $data);

            $this->setFlash('success', 'Persona registrada exitosamente');
            redirect(url('personas'));
        } else {
            $this->setFlash('error', 'Error al registrar persona');
            redirect(url('personas/crear'));
        }
    }

    public function edit($id) {
        $this->requireAuth();

        $personaModel = new Persona();
        $persona = $personaModel->getWithColegiado($id);

        if (!$persona) {
            $this->setFlash('error', 'Persona no encontrada');
            redirect(url('personas'));
        }

        $this->view('personas.edit', ['persona' => $persona]);
    }

    public function update($id) {
        $this->requireAuth();

        if (!$this->isPost()) {
            redirect(url("personas/editar/{$id}"));
        }

        $personaModel = new Persona();
        $personaAnterior = $personaModel->find($id);

        if (!$personaAnterior) {
            $this->setFlash('error', 'Persona no encontrada');
            redirect(url('personas'));
        }

        $data = [
            'apellido_paterno' => sanitize($this->post('apellido_paterno')),
            'apellido_materno' => sanitize($this->post('apellido_materno')),
            'nombres' => sanitize($this->post('nombres')),
            'fecha_nacimiento' => $this->post('fecha_nacimiento'),
            'sexo' => $this->post('sexo'),
            'direccion' => sanitize($this->post('direccion')),
            'distrito' => sanitize($this->post('distrito')),
            'provincia' => sanitize($this->post('provincia')),
            'departamento' => sanitize($this->post('departamento')),
            'telefono' => sanitize($this->post('telefono')),
            'celular' => sanitize($this->post('celular')),
            'email' => sanitize($this->post('email')),
            'actualizado_por' => $this->getUser()
        ];

        if ($personaModel->update($id, $data)) {
            $auditoriaModel = new Auditoria();
            $auditoriaModel->registrar($this->getUser(), 'PERSONAS', 'ACTUALIZAR', 'personas', $id, $personaAnterior, $data);

            $this->setFlash('success', 'Persona actualizada exitosamente');
        } else {
            $this->setFlash('error', 'Error al actualizar persona');
        }

        redirect(url("personas/editar/{$id}"));
    }

    public function delete($id) {
        $this->requireRole('ADMIN');

        if (!$this->isPost()) {
            $this->json(['error' => 'Método no permitido'], 405);
        }

        $personaModel = new Persona();
        $persona = $personaModel->find($id);

        if (!$persona) {
            $this->json(['error' => 'Persona no encontrada'], 404);
        }

        if ($persona['es_colegiado']) {
            $this->json(['error' => 'No se puede eliminar un colegiado'], 400);
        }

        if ($personaModel->delete($id)) {
            $auditoriaModel = new Auditoria();
            $auditoriaModel->registrar($this->getUser(), 'PERSONAS', 'ELIMINAR', 'personas', $id, $persona);

            $this->json(['success' => true, 'message' => 'Persona eliminada']);
        } else {
            $this->json(['error' => 'Error al eliminar persona'], 500);
        }
    }
}
