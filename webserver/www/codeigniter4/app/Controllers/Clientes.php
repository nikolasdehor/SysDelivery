<?php

namespace App\Controllers;

use App\Models\Clientes as Cliente;
use App\Models\Usuarios as Usuario;

class Clientes extends BaseController
{
    private $clientes;
    private $usuarios;

    public function __construct()
    {
        $this->clientes = new Cliente();
        $this->usuarios = new Usuario();
        helper('functions');
    }

    public function index(): string
    {
        $data['title'] = 'Clientes';
        $data['clientes'] = $this->clientes
            ->select('clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, usuarios.usuarios_cpf')
            ->join('usuarios', 'clientes.clientes_usuario_id = usuarios.usuarios_id')
            ->findAll();

        return view('clientes/index', $data);
    }

    public function new(): string
    {
        $data = [
            'title' => 'Novo Cliente',
            'op' => 'create',
            'form' => 'Cadastrar',
            'usuarios' => $this->usuarios->findAll(),
            'clientes' => (object) [
                'clientes_id' => '',
                'clientes_usuario_id' => '',
                'clientes_observacoes' => ''
            ]
        ];

        return view('clientes/form', $data);
    }

    public function create()
    {
        if (!$this->validate([
            'clientes_usuario_id' => 'required',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->clientes->save([
            'clientes_usuario_id' => $this->request->getPost('clientes_usuario_id'),
            'clientes_observacoes' => $this->request->getPost('clientes_observacoes') ?: null,
        ]);

        return redirect()->to('/clientes')->with('msg', msg('Cliente cadastrado com sucesso!', 'success'));
    }

    public function delete($id)
    {
        $this->clientes->delete($id);
        return redirect()->to('/clientes')->with('msg', msg('Deletado com sucesso!', 'success'));
    }

    public function edit($id)
    {
        $data = [
            'clientes' => $this->clientes->find($id),
            'usuarios' => $this->usuarios->findAll(),
            'title' => 'Editar Cliente',
            'form' => 'Editar',
            'op' => 'update',
        ];

        return view('clientes/form', $data);
    }

    public function update()
    {
        $this->clientes->update($this->request->getPost('clientes_id'), [
            'clientes_usuario_id' => $this->request->getPost('clientes_usuario_id'),
            'clientes_observacoes' => $this->request->getPost('clientes_observacoes'),
        ]);

        return redirect()->to('/clientes')->with('msg', msg('Cliente alterado com sucesso!', 'success'));
    }

    public function search()
    {
        $pesquisar = $this->request->getPost('pesquisar');
        $data['clientes'] = $this->clientes
            ->select('clientes.*, usuarios.usuarios_nome')
            ->join('usuarios', 'clientes.clientes_usuario_id = usuarios.usuarios_id')
            ->like('clientes_observacoes', $pesquisar)
            ->orLike('usuarios.usuarios_nome', $pesquisar)
            ->findAll();

        $data['title'] = 'Clientes';
        $data['msg'] = msg("Resultados encontrados: " . count($data['clientes']), 'info');

        return view('clientes/index', $data);
    }
}