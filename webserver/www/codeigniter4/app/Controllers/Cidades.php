<?php

namespace App\Controllers;
use App\Models\Cidades as Cidades_model;

class Cidades extends BaseController
{
    private $cidades;

    public function __construct()
    {
        $this->cidades = new Cidades_model();
        helper('functions');
    }

    public function index(): string
    {
        $data['title'] = 'Cidades';
        $data['cidades'] = $this->cidades->findAll();
        return view('cidades/index', $data);
    }

    public function new(): string
    {
        $data['title'] = 'Cidades';
        $data['op'] = 'create';
        $data['form'] = 'Cadastrar';
        $data['cidades'] = (object) [
            'cidades_nome' => '',
            'cidades_uf' => '',
            'cidades_id' => ''
        ];
        return view('cidades/form', $data);
    }

    public function create()
    {
        if (
            !$this->validate([
                'cidades_nome' => 'required|max_length[255]|min_length[3]',
                'cidades_uf' => 'required|max_length[2]|min_length[2]'
            ])
        ) {
            $data['cidades'] = (object) [
                'cidades_nome' => $this->request->getPost('cidades_nome'),
                'cidades_uf' => $this->request->getPost('cidades_uf'),
                'cidades_id' => ''
            ];
            $data['title'] = 'Cidades';
            $data['form'] = 'Cadastrar';
            $data['op'] = 'create';
            return view('cidades/form', $data);
        }

        $this->cidades->save([
            'cidades_nome' => $this->request->getPost('cidades_nome'),
            'cidades_uf' => $this->request->getPost('cidades_uf')
        ]);

        return redirect()->to('/cidades')->with('msg', msg('Cadastrado com Sucesso!', 'success'));
    }


    public function delete($id)
    {
        $this->cidades->where('cidades_id', (int) $id)->delete();
        $data['msg'] = msg('Deletado com Sucesso!', 'success');
        $data['cidades'] = $this->cidades->findAll();
        $data['title'] = 'Cidades';
        return view('cidades/index', $data);
    }

    public function edit($id)
    {
        $cidade = $this->cidades->find((int) $id);

        if (!$cidade) {
            return redirect()->to('/cidades')->with('msg', msg('Cidade não encontrada.', 'danger'));
        }

        $data['cidades'] = $cidade;
        $data['title'] = 'Cidades';
        $data['form'] = 'Alterar';
        $data['op'] = 'update';
        return view('cidades/form', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('cidades_id');

        $dataForm = [
            'cidades_nome' => $this->request->getPost('cidades_nome'),
            'cidades_uf' => $this->request->getPost('cidades_uf'),
        ];

        $this->cidades->update($id, $dataForm);

        $data['msg'] = msg('Alterado com Sucesso!', 'success');
        $data['cidades'] = $this->cidades->findAll();
        $data['title'] = 'Cidades';
        return view('cidades/index', $data);
    }


    public function search()
    {
        $pesquisa = $_REQUEST['pesquisar'] ?? '';
        $data['cidades'] = $this->cidades->like('cidades_nome', $pesquisa)->findAll();
        $total = count($data['cidades']);
        $data['msg'] = msg("Dados Encontrados: {$total}", 'success');
        $data['title'] = 'Cidades';
        return view('cidades/index', $data);
    }
}