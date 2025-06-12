<?php

namespace App\Controllers;

use App\Models\Cupons as CuponsModel;

class CuponsController extends BaseController
{
    private $cupons;
    private $session;

    public function __construct()
    {
        $this->cupons = new CuponsModel();
        $this->session = \Config\Services::session();
        helper('functions');
    }

    /**
     * Lista cupons (admin)
     */
    public function index()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $data['title'] = 'Gerenciar Cupons';
        $data['cupons'] = $this->cupons->orderBy('cupons_id', 'DESC')->findAll();
        $data['estatisticas'] = $this->cupons->getEstatisticasCupons();

        return view('cupons/index', $data);
    }

    /**
     * Formulário para novo cupom
     */
    public function novo()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $data['title'] = 'Novo Cupom';
        $data['action'] = 'criar';
        $data['cupom'] = (object) [
            'cupons_codigo' => '',
            'cupons_descricao' => '',
            'cupons_tipo' => 'percentual',
            'cupons_valor' => '',
            'cupons_valor_minimo' => '',
            'cupons_data_inicio' => date('Y-m-d'),
            'cupons_data_fim' => date('Y-m-d', strtotime('+30 days')),
            'cupons_limite_uso' => '',
            'cupons_ativo' => 1
        ];

        return view('cupons/form', $data);
    }

    /**
     * Salva novo cupom
     */
    public function criar()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $dados = [
            'cupons_codigo' => $this->request->getPost('cupons_codigo'),
            'cupons_descricao' => $this->request->getPost('cupons_descricao'),
            'cupons_tipo' => $this->request->getPost('cupons_tipo'),
            'cupons_valor' => $this->request->getPost('cupons_valor'),
            'cupons_valor_minimo' => $this->request->getPost('cupons_valor_minimo') ?: 0,
            'cupons_data_inicio' => $this->request->getPost('cupons_data_inicio'),
            'cupons_data_fim' => $this->request->getPost('cupons_data_fim'),
            'cupons_limite_uso' => $this->request->getPost('cupons_limite_uso') ?: null,
            'cupons_ativo' => $this->request->getPost('cupons_ativo') ? 1 : 0
        ];

        // Gera código automaticamente se não informado
        if (empty($dados['cupons_codigo'])) {
            $dados['cupons_codigo'] = $this->cupons->gerarCodigoUnico();
        }

        if ($this->cupons->insert($dados)) {
            return redirect()->to('/cupons')->with('msg', msg('Cupom criado com sucesso', 'success'));
        } else {
            $errors = $this->cupons->errors();
            $errorMsg = implode(', ', $errors);
            return redirect()->back()->withInput()->with('msg', msg($errorMsg, 'danger'));
        }
    }

    /**
     * Formulário para editar cupom
     */
    public function editar($cupomId)
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $cupom = $this->cupons->find($cupomId);
        if (!$cupom) {
            return redirect()->to('/cupons')->with('msg', msg('Cupom não encontrado', 'danger'));
        }

        $data['title'] = 'Editar Cupom';
        $data['action'] = 'atualizar';
        $data['cupom'] = $cupom;

        return view('cupons/form', $data);
    }

    /**
     * Atualiza cupom
     */
    public function atualizar()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $cupomId = $this->request->getPost('cupom_id');
        $cupom = $this->cupons->find($cupomId);

        if (!$cupom) {
            return redirect()->to('/cupons')->with('msg', msg('Cupom não encontrado', 'danger'));
        }

        $dados = [
            'cupons_codigo' => $this->request->getPost('cupons_codigo'),
            'cupons_descricao' => $this->request->getPost('cupons_descricao'),
            'cupons_tipo' => $this->request->getPost('cupons_tipo'),
            'cupons_valor' => $this->request->getPost('cupons_valor'),
            'cupons_valor_minimo' => $this->request->getPost('cupons_valor_minimo') ?: 0,
            'cupons_data_inicio' => $this->request->getPost('cupons_data_inicio'),
            'cupons_data_fim' => $this->request->getPost('cupons_data_fim'),
            'cupons_limite_uso' => $this->request->getPost('cupons_limite_uso') ?: null,
            'cupons_ativo' => $this->request->getPost('cupons_ativo') ? 1 : 0
        ];

        if ($this->cupons->update($cupomId, $dados)) {
            return redirect()->to('/cupons')->with('msg', msg('Cupom atualizado com sucesso', 'success'));
        } else {
            $errors = $this->cupons->errors();
            $errorMsg = implode(', ', $errors);
            return redirect()->back()->withInput()->with('msg', msg($errorMsg, 'danger'));
        }
    }

    /**
     * Remove cupom
     */
    public function remover($cupomId)
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $cupom = $this->cupons->find($cupomId);
        if (!$cupom) {
            return redirect()->to('/cupons')->with('msg', msg('Cupom não encontrado', 'danger'));
        }

        if ($this->cupons->delete($cupomId)) {
            return redirect()->to('/cupons')->with('msg', msg('Cupom removido com sucesso', 'success'));
        } else {
            return redirect()->to('/cupons')->with('msg', msg('Erro ao remover cupom', 'danger'));
        }
    }

    /**
     * Ativa/Desativa cupom
     */
    public function toggleStatus()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $cupomId = $this->request->getPost('cupom_id');
        $status = $this->request->getPost('status');

        $cupom = $this->cupons->find($cupomId);
        if (!$cupom) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cupom não encontrado'
            ]);
        }

        $sucesso = $this->cupons->update($cupomId, ['cupons_ativo' => $status]);

        if ($sucesso) {
            $statusText = $status ? 'ativado' : 'desativado';
            return $this->response->setJSON([
                'success' => true,
                'message' => "Cupom {$statusText} com sucesso"
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao alterar status do cupom'
            ]);
        }
    }

    /**
     * Valida cupom via AJAX
     */
    public function validar()
    {
        $codigo = $this->request->getPost('codigo');
        $valorPedido = $this->request->getPost('valor_pedido') ?: 0;

        if (!$codigo) {
            return $this->response->setJSON([
                'valido' => false,
                'erro' => 'Código não informado'
            ]);
        }

        $validacao = $this->cupons->validarCupom($codigo, $valorPedido);

        if ($validacao['valido']) {
            $cupom = $validacao['cupom'];
            $desconto = $this->cupons->calcularDesconto($cupom, $valorPedido);

            return $this->response->setJSON([
                'valido' => true,
                'cupom' => [
                    'id' => $cupom->cupons_id,
                    'codigo' => $cupom->cupons_codigo,
                    'descricao' => $cupom->cupons_descricao,
                    'tipo' => $cupom->cupons_tipo,
                    'valor' => $cupom->cupons_valor,
                    'desconto' => $desconto
                ]
            ]);
        } else {
            return $this->response->setJSON($validacao);
        }
    }

    /**
     * Lista cupons disponíveis para o cliente
     */
    public function disponiveis()
    {
        $data['title'] = 'Cupons Disponíveis';
        $data['cupons'] = $this->cupons->getCuponsDisponiveis();

        return view('cupons/disponiveis', $data);
    }

    /**
     * Gera código único para cupom
     */
    public function gerarCodigo()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $prefixo = $this->request->getPost('prefixo') ?: 'CUP';
        $codigo = $this->cupons->gerarCodigoUnico($prefixo);

        return $this->response->setJSON([
            'success' => true,
            'codigo' => $codigo
        ]);
    }

    /**
     * Verifica permissão de administrador
     */
    private function verificarPermissaoAdmin()
    {
        return $this->session->has('login') && 
               $this->session->get('login')->logged_in && 
               $this->session->get('login')->usuarios_nivel == 2;
    }
}
