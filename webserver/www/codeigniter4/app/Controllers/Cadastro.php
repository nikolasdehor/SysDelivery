<?php

namespace App\Controllers;
use App\Models\Usuarios as Usuarios_cadastro;
use App\Models\Clientes as Clientes_cadastro;
use CodeIgniter\HTTP\RedirectResponse;
class Cadastro extends BaseController
{
    private $usuarios;
    private $clientes;

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
        helper('security');

        // Log dos dados recebidos
        log_message('debug', 'Dados recebidos no cadastro: ' . json_encode($this->request->getPost()));

        if (!$this->validate([
            'nome' => 'required',
            'sobrenome' => 'required',
            'email' => 'required|valid_email',
            'cpf' => 'required|min_length[11]',
            'telefone' => 'required',
            'senha' => 'required|min_length[8]',
            'confirmar_senha' => 'matches[senha]',
            'data_nasc' => 'required|valid_date[Y-m-d]'
        ])) {
            log_message('error', 'Erro de validação no cadastro: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validação adicional de força da senha
        $senha = $this->request->getPost('senha');
        $passwordErrors = validate_password_strength($senha, 8);

        if (!empty($passwordErrors)) {
            log_message('error', 'Senha não atende aos requisitos de segurança: ' . json_encode($passwordErrors));
            return redirect()->back()->withInput()->with('errors', ['senha' => 'A senha deve atender aos seguintes requisitos: ' . implode(', ', $passwordErrors)]);
        }

        log_message('debug', 'Validação passou, tentando salvar usuário...');

        $dadosUsuario = [
            'usuarios_nome' => $this->request->getPost('nome'),
            'usuarios_sobrenome' => $this->request->getPost('sobrenome'),
            'usuarios_email' => $this->request->getPost('email'),
            'usuarios_cpf' => $this->request->getPost('cpf'),
            'usuarios_fone' => $this->request->getPost('telefone'),
            'usuarios_senha' => hash_password_secure($senha),
            'usuarios_data_nasc' => date('Y-m-d', strtotime($this->request->getPost('data_nasc'))),
            'usuarios_data_cadastro' => date('Y-m-d H:i:s'),
            'usuarios_nivel' => 0,
        ];

        log_message('debug', 'Dados do usuário para salvar: ' . json_encode($dadosUsuario));

        $resultadoUsuario = $this->usuarios->save($dadosUsuario);
        log_message('debug', 'Resultado do save do usuário: ' . ($resultadoUsuario ? 'true' : 'false'));

        $usuarioId = $this->usuarios->insertID();
        log_message('debug', 'ID do usuário inserido: ' . $usuarioId);

        if ($usuarioId) {
            $dadosCliente = [
                'clientes_usuario_id' => $usuarioId,
                'clientes_observacoes' => $this->request->getPost('observacoes') ?: null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            log_message('debug', 'Dados do cliente para salvar: ' . json_encode($dadosCliente));
            $resultadoCliente = $this->clientes->save($dadosCliente);
            log_message('debug', 'Resultado do save do cliente: ' . ($resultadoCliente ? 'true' : 'false'));
        }

        log_message('debug', 'Cadastro finalizado, redirecionando para login...');

        return redirect()->to('/login')->with(
            'msg',
            msg('Cadastro realizado com sucesso! Você já pode fazer login.', 'success')
        );
    }
}