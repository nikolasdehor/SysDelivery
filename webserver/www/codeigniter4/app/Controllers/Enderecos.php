<?php

namespace App\Controllers;

use App\Models\Enderecos as Endereco;
use App\Models\Cidades as Cidade;
use App\Models\Usuarios as Usuario;

class Enderecos extends BaseController
{
    private $enderecos;
    private $cidades;
    private $usuarios;

    public function __construct()
    {
        $this->enderecos = new Endereco();
        $this->cidades = new Cidade();
        $this->usuarios = new Usuario();
        helper('functions');
    }

    public function index(): string
    {
        $data['title'] = 'Endereços';
        $data['enderecos'] = $this->enderecos
            ->join('cidades', 'enderecos_cidade_id = cidades_id')
            ->join('usuarios', 'enderecos_usuario_id = usuarios_id')
            ->findAll();

        return view('enderecos/index', $data);
    }

    public function new(): string
    {
        $nivel = session('login')->usuarios_nivel ?? 0;
        if ($nivel == 2) {
            $data['title'] = 'Novo Endereço';
            $data['op'] = 'create';
            $data['form'] = 'Cadastrar';
            $data['cidades'] = $this->cidades->findAll();
            $data['usuarios'] = $this->usuarios->findAll();
            $data['enderecos'] = (object)[
                'enderecos_rua' => '',
                'enderecos_numero' => '',
                'enderecos_complemento' => '',
                'enderecos_status' => '',
                'enderecos_cidade_id' => '',
                'enderecos_usuario_id' => ''
            ];
            return view('enderecos/form', $data);
        }elseif ($nivel == 0) {
            $data['title'] = 'Novo Endereço';
            $data['op'] = 'create';
            $data['form'] = 'Adicionar';
            $data['usuarios'] = $this->usuarios->find(session('login')->usuarios_id);
            $data['enderecos'] = (object)[
                'enderecos_rua' => '',
                'enderecos_numero' => '',
                'enderecos_complemento' => '',
                'enderecos_status' => '',
                'enderecos_cidade_nome' => '',
                'enderecos_cidade_uf' => ''
            ];
            return view('enderecos/form', $data);
        }

    }

    public function create()
    {
        $nivel = session('login')->usuarios_nivel ?? 0;
        if ($nivel == 2) {
            
            $validationRules = [
                'enderecos_rua' => 'required|max_length[255]',
                'enderecos_numero' => 'required|max_length[10]',
                'enderecos_status' => 'required|in_list[0,1]',
                'enderecos_cidade_id' => 'required|integer',
                'enderecos_usuario_id' => 'required|integer',
            ];
    
            if (!$this->validate($validationRules)) {
                $data['enderecos'] = (object) $this->request->getPost();
                $data['title'] = 'Novo Endereço';
                $data['form'] = 'Cadastrar';
                $data['op'] = 'create';
                $data['cidades'] = $this->cidades->findAll();
                $data['usuarios'] = $this->usuarios->findAll();
                return view('enderecos/form', $data);
            }
    
            $this->enderecos->save($this->request->getPost());
    
            $data['msg'] = msg('Cadastrado com sucesso!', 'success');
            $data['enderecos'] = $this->enderecos
                ->join('cidades', 'enderecos_cidade_id = cidades_id')
                ->join('usuarios', 'enderecos_usuario_id = usuarios_id')
                ->findAll();
            $data['title'] = 'Endereços';
    
            return view('enderecos/index', $data);
        }elseif ($nivel == 0) {
            $validationRules = [
                'enderecos_rua' => 'required|max_length[255]',
                'enderecos_numero' => 'required|max_length[10]',
                'enderecos_cidade_nome' => 'required|max_length[100]',
                'enderecos_cidade_uf' => 'required|max_length[2]',
            ];

            if (!$this->validate($validationRules)) {
                $data['enderecos'] = (object) $this->request->getPost();
                $data['title'] = 'Novo Endereço';
                $data['form'] = 'Adicionar';
                $data['enderecos']->enderecos_status = 1;
                $data['op'] = 'create';
                $data['cidades'] = $this->cidades->findAll();
                $data['usuarios'] = $this->usuarios->findAll();
                return view('enderecos/form', $data);
            }

            
            $db = \Config\Database::connect();
            $db->transStart();
            
            $usuarioId = session('login')->usuarios_id ?? 0;

            if ($usuarioId <= 0) {
                $data['msg'] = msg('Usuário não encontrado!', 'danger');
                return view('enderecos/form', $data);
            }

            $cidadeData = [
                'cidades_nome' => $this->request->getPost('enderecos_cidade_nome'),
                'cidades_uf' => $this->request->getPost('enderecos_cidade_uf')
            ];
            
            $this->cidades->save($cidadeData);

            $cidadeId = $this->cidades->insertID();

            $enderecoData = [
                'enderecos_rua' => $this->request->getPost('enderecos_rua'),
                'enderecos_numero' => $this->request->getPost('enderecos_numero'),
                'enderecos_complemento' => $this->request->getPost('enderecos_complemento'),
                'enderecos_status' => 1,
                'enderecos_cidade_id' => $cidadeId,
                'enderecos_usuario_id' => $usuarioId
            ];

            $this->enderecos->save($enderecoData);

            $db->transComplete();

            if ($db->transStatus() === false) {
                $data['msg'] = msg('Erro ao cadastrar endereço!', 'danger');
                return view('enderecos/form', $data);
            }

            return redirect()->to(base_url('usuarios/perfil/' . session('login')->usuarios_id));
        }
        else {
            $data['msg'] = msg('Sem permissão de acesso!', 'danger');
            session()->destroy();
            return view('/home', $data);
        }
    }


    public function delete($id)
    {
        $nivel = session('login')->usuarios_nivel ?? 0;
        $this->enderecos->where('enderecos_id', (int)$id)->delete();
        if ($nivel == 2) {
            $data['msg'] = msg('Deletado com sucesso!', 'success');
            $data['enderecos'] = $this->enderecos
                ->join('cidades', 'enderecos_cidade_id = cidades_id')
                ->join('usuarios', 'enderecos_usuario_id = usuarios_id')
                ->findAll();
            $data['title'] = 'Endereços';
            return view('enderecos/index', $data);
        }elseif ($nivel == 0) {
            $data['msg'] = msg('Enderço excluido com sucesso!', 'danger');
            return redirect()->to(base_url('usuarios/perfil/' . session('login')->usuarios_id));
        } else {
            $data['msg'] = msg('Sem permissão de acesso!', 'danger');
            session()->destroy();
            return view('/home', $data);
        }
    }

    public function edit($id)
    {
        $nivel = session('login')->usuarios_nivel ?? 0;
        if ($nivel == 2) {
            $data['enderecos'] = $this->enderecos->find($id);
            $data['cidades'] = $this->cidades->findAll();
            $data['usuarios'] = $this->usuarios->findAll();
            $data['title'] = 'Editar Endereço';
            $data['form'] = 'Editar';
            $data['op'] = 'update';
            return view('enderecos/form', $data);
        }elseif ($nivel == 0) {
            $data['enderecos'] = $this->enderecos
                ->join('cidades', 'enderecos_cidade_id = cidades_id')
                ->join('usuarios', 'enderecos_usuario_id = usuarios_id')
                ->select('enderecos.*, cidades.cidades_nome, cidades.cidades_uf, usuarios.usuarios_id')
                ->find($id);
            $data['usuarios'] = $this->usuarios->find(session('login')->usuarios_id);
            $data['title'] = 'Endereço';
            $data['form'] = 'Editar';
            $data['op'] = 'update';
            return view('enderecos/form', $data);
        } else {
            $data['msg'] = msg('Sem permissão de acesso!', 'danger');
            session()->destroy();
            return view('/home', $data);
        }
    }

    public function update()
    {
        $nivel = session('login')->usuarios_nivel ?? 0;
        if ($nivel == 2) {
            $dataForm = [
                'enderecos_rua' => $_REQUEST['enderecos_rua'],
                'enderecos_numero' => $_REQUEST['enderecos_numero'],
                'enderecos_complemento' => $_REQUEST['enderecos_complemento'],
                'enderecos_status' => $_REQUEST['enderecos_status'],
                'enderecos_cidade_id' => $_REQUEST['enderecos_cidade_id'],
                'enderecos_usuario_id' => $_REQUEST['enderecos_usuario_id'],
            ];
    
            $this->enderecos->update($_REQUEST['enderecos_id'], $dataForm);
    
            $data['msg'] = msg('Endereço alterado com sucesso!', 'success');
            $data['enderecos'] = $this->enderecos
                ->join('cidades', 'enderecos_cidade_id = cidades_id')
                ->join('usuarios', 'enderecos_usuario_id = usuarios_id')
                ->findAll();
            $data['title'] = 'Endereços';
    
            return view('enderecos/index', $data);
        }elseif ($nivel == 0) {
            $db = \Config\Database::connect();
            $db->transStart();

            $enderecoData = [
                'enderecos_rua' => $_REQUEST['enderecos_rua'],
                'enderecos_numero' => $_REQUEST['enderecos_numero'],
                'enderecos_complemento' => $_REQUEST['enderecos_complemento'],
                'enderecos_status' => 1,
                'enderecos_usuario_id' => $_REQUEST['enderecos_usuario_id'],
            ];

            $cidadeData = [
                'cidades_nome' => $_REQUEST['enderecos_cidade_nome'],
                'cidades_uf' => $_REQUEST['enderecos_cidade_uf']
            ];
    
            $this->enderecos->update($_REQUEST['enderecos_id'], $enderecoData);
            $this->cidades->update($_REQUEST['enderecos_cidades_id'], $cidadeData);

            $db->transComplete();
            if ($db->transStatus() === false) {
                $data['msg'] = msg('Erro ao atualizar endereço!', 'danger');
                return view('enderecos/form', $data);
            }
    
            $data['msg'] = msg('Endereço alterado com sucesso!', 'success');
            return redirect()->to(base_url('usuarios/perfil/' . session('login')->usuarios_id));
        } else {
            $data['msg'] = msg('Sem permissão de acesso!', 'danger');
            session()->destroy();
            return view('/home', $data);
        }
    }

    public function search()
    {
        $pesquisar = $_REQUEST['pesquisar'] ?? '';

        $data['enderecos'] = $this->enderecos
            ->join('cidades', 'enderecos_cidade_id = cidades_id')
            ->join('usuarios', 'enderecos_usuario_id = usuarios_id')
            ->like('enderecos_rua', $pesquisar)
            ->orLike('enderecos_numero', $pesquisar)
            ->orLike('enderecos_complemento', $pesquisar)
            ->find();

        $total = count($data['enderecos']);
        $data['msg'] = msg("Endereços encontrados: {$total}", 'success');
        $data['title'] = 'Endereços';

        return view('enderecos/index', $data);
    }
}