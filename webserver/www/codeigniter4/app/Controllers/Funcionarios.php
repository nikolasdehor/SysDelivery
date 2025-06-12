<?php

namespace App\Controllers;

use App\Models\Funcionarios as Funcionario;
use App\Models\Usuarios as Usuario;

class Funcionarios extends BaseController
{
    private $funcionarios;
    private $usuarios;

    public function __construct()
    {
        $this->funcionarios = new Funcionario();
        $this->usuarios = new Usuario();
        helper('functions');
    }

    public function index(): string
    {
        $data['title'] = 'Funcionários';
        $data['funcionarios'] = $this->funcionarios
            ->select('funcionarios.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, usuarios.usuarios_cpf')
            ->join('usuarios', 'funcionarios.funcionarios_usuario_id = usuarios.usuarios_id')
            ->findAll();

        return view('funcionarios/index', $data);
    }

    public function new(): string
    {
        $data = [
            'title' => 'Novo Funcionário',
            'op' => 'create',
            'form' => 'Cadastrar',
            'usuarios' => $this->usuarios->findAll(),
            'funcionarios' => (object) [
                'funcionarios_id' => '',
                'funcionarios_usuario_id' => '',
                'funcionarios_cargo' => '',
                'funcionarios_salario' => '',
                'funcionarios_data_admissao' => '',
                'funcionarios_observacoes' => ''
            ]
        ];

        return view('funcionarios/form', $data);
    }

    public function create()
    {
        $this->funcionarios->save([
            'funcionarios_usuario_id' => $this->request->getPost('funcionarios_usuario_id'),
            'funcionarios_cargo' => $this->request->getPost('funcionarios_cargo'),
            'funcionarios_salario' => $this->request->getPost('funcionarios_salario'),
            'funcionarios_data_admissao' => $this->request->getPost('funcionarios_data_admissao'),
            'funcionarios_observacoes' => $this->request->getPost('funcionarios_observacoes'),
        ]);

        $usuarios_id = $this->request->getPost('funcionarios_usuario_id');
        if ($usuarios_id) {
            $this->usuarios->update($usuarios_id, [
                'usuarios_nivel' => 1,
            ]);
        }

        return redirect()->to('/funcionarios')->with('msg', msg('Funcionário cadastrado com sucesso!', 'success'));
    }

    public function delete($id)
    {
        $this->funcionarios->delete($id);
        return redirect()->to('/funcionarios')->with('msg', msg('Funcionário deletado com sucesso!', 'success'));
    }

    public function edit($id)
    {
        $data = [
            'funcionarios' => $this->funcionarios->find($id),
            'usuarios' => $this->usuarios->findAll(),
            'title' => 'Editar Funcionário',
            'form' => 'Editar',
            'op' => 'update',
        ];

        return view('funcionarios/form', $data);
    }

    public function update()
    {
        $this->funcionarios->update($this->request->getPost('funcionarios_id'), [
            'funcionarios_usuario_id' => $this->request->getPost('funcionarios_usuario_id'),
            'funcionarios_cargo' => $this->request->getPost('funcionarios_cargo'),
            'funcionarios_salario' => $this->request->getPost('funcionarios_salario'),
            'funcionarios_data_admissao' => $this->request->getPost('funcionarios_data_admissao'),
            'funcionarios_observacoes' => $this->request->getPost('funcionarios_observacoes'),
        ]);

        return redirect()->to('/funcionarios')->with('msg', msg('Funcionário atualizado com sucesso!', 'success'));
    }

    public function search()
    {
        $pesquisar = $this->request->getPost('pesquisar');
        $data['funcionarios'] = $this->funcionarios
            ->select('funcionarios.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
            ->join('usuarios', 'funcionarios.funcionarios_usuario_id = usuarios.usuarios_id')
            ->like('funcionarios_cargo', $pesquisar)
            ->orLike('usuarios.usuarios_nome', $pesquisar)
            ->findAll();

        $data['title'] = 'Funcionários';
        $data['msg'] = msg("Resultados encontrados: " . count($data['funcionarios']), 'info');

        return view('funcionarios/index', $data);
    }
}