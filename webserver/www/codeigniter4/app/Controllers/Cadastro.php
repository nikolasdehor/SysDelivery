<?php

namespace App\Controllers;
use App\Models\Usuarios as Usuarios_cadastro;
use App\Models\Clientes as Clientes_cadastro;
use CodeIgniter\HTTP\RedirectResponse;
class Cadastro extends BaseController
{
    private $usuarios;

    public function __construct()
    {
        $this->usuarios = new Usuarios_cadastro();
        $this->clientes = new Clientes_cadastro();
        helper('functions');
        $data['msg'] = ''; 
    }

    public function index(): string
    {
        $data['title'] = 'Cadastro';
        return view('cadastro', $data);
    }

    public function salvar(): RedirectResponse
    {
        if (!$this->validate([
            'nome' => 'required',
            'sobrenome' => 'required',
            'email' => 'required|valid_email',
            'cpf' => 'required|exact_length[14]',
            'telefone' => 'required',
            'senha' => 'required|min_length[6]',
            'confirmar_senha' => 'matches[senha]',
            'data_nasc' => 'required|valid_date[Y-m-d]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->usuarios->save([
            'usuarios_nome' => $this->request->getPost('nome'),
            'usuarios_sobrenome' => $this->request->getPost('sobrenome'),
            'usuarios_email' => $this->request->getPost('email'),
            'usuarios_cpf' => $this->request->getPost('cpf'),
            'usuarios_fone' => $this->request->getPost('telefone'),
            'usuarios_senha' => md5($this->request->getPost('senha')),
            'usuarios_data_nasc' => date('Y-m-d', strtotime($this->request->getPost('data_nasc'))),
            'usuarios_data_cadastro' => date('Y-m-d H:i:s'),
            'usuarios_nivel' => 0,

        ]);

        $usuarioId = $this->usuarios->insertID();

        if ($usuarioId) {
            $this->clientes->save([
                'clientes_usuario_id' => $usuarioId,
                'clientes_observacoes' => $this->request->getPost('observacoes') ?: null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }


        return redirect()->to('/login')->with(
            'msg',
            msg('Cadastro realizado com sucesso! Você já pode fazer login.', 'success')
        );
    }
}