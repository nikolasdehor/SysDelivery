<?php

namespace App\Controllers;

use App\Models\Avaliacoes as AvaliacoesModel;
use App\Models\Produtos as ProdutosModel;
use App\Models\Pedidos as PedidosModel;

class AvaliacoesController extends BaseController
{
    private $avaliacoes;
    private $produtos;
    private $pedidos;
    private $session;

    public function __construct()
    {
        $this->avaliacoes = new AvaliacoesModel();
        $this->produtos = new ProdutosModel();
        $this->pedidos = new PedidosModel();
        $this->session = \Config\Services::session();
        helper('functions');
    }

    /**
     * Lista avaliações de um produto
     */
    public function produto($produtoId)
    {
        $produto = $this->produtos->find($produtoId);
        if (!$produto) {
            return redirect()->to('/')->with('msg', msg('Produto não encontrado', 'danger'));
        }

        $data['title'] = 'Avaliações - ' . $produto->produtos_nome;
        $data['produto'] = $produto;
        $data['avaliacoes'] = $this->avaliacoes->getAvaliacoesProduto($produtoId);
        $data['media'] = $this->avaliacoes->getMediaAvaliacoes($produtoId);
        $data['total_avaliacoes'] = $this->avaliacoes->contarAvaliacoes($produtoId);
        $data['estatisticas'] = $this->avaliacoes->getEstatisticasAvaliacoes($produtoId);
        
        // Verifica se usuário já avaliou
        if ($this->verificarLogin()) {
            $usuarioId = $this->session->get('login')->usuarios_id;
            $data['ja_avaliou'] = $this->avaliacoes->usuarioJaAvaliou($produtoId, $usuarioId);
        } else {
            $data['ja_avaliou'] = false;
        }

        return view('avaliacoes/produto', $data);
    }

    /**
     * Formulário para adicionar avaliação
     */
    public function adicionar($produtoId)
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login')->with('msg', msg('Faça login para avaliar produtos', 'warning'));
        }

        $produto = $this->produtos->find($produtoId);
        if (!$produto) {
            return redirect()->to('/')->with('msg', msg('Produto não encontrado', 'danger'));
        }

        $usuarioId = $this->session->get('login')->usuarios_id;

        // Verifica se já avaliou
        if ($this->avaliacoes->usuarioJaAvaliou($produtoId, $usuarioId)) {
            return redirect()->to("/avaliacoes/produto/{$produtoId}")
                ->with('msg', msg('Você já avaliou este produto', 'warning'));
        }

        $data['title'] = 'Avaliar Produto';
        $data['produto'] = $produto;
        $data['action'] = 'adicionar';

        return view('avaliacoes/form', $data);
    }

    /**
     * Salva nova avaliação
     */
    public function salvar()
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login');
        }

        $produtoId = $this->request->getPost('produto_id');
        $nota = $this->request->getPost('nota');
        $comentario = $this->request->getPost('comentario');
        $usuarioId = $this->session->get('login')->usuarios_id;

        // Validações
        if (!$produtoId || !$nota) {
            return redirect()->back()->with('msg', msg('Dados obrigatórios não informados', 'danger'));
        }

        if ($nota < 1 || $nota > 5) {
            return redirect()->back()->with('msg', msg('Nota deve ser entre 1 e 5', 'danger'));
        }

        // Verifica se produto existe
        $produto = $this->produtos->find($produtoId);
        if (!$produto) {
            return redirect()->to('/')->with('msg', msg('Produto não encontrado', 'danger'));
        }

        // Verifica se já avaliou
        if ($this->avaliacoes->usuarioJaAvaliou($produtoId, $usuarioId)) {
            return redirect()->to("/avaliacoes/produto/{$produtoId}")
                ->with('msg', msg('Você já avaliou este produto', 'warning'));
        }

        // Salva avaliação
        $sucesso = $this->avaliacoes->adicionarAvaliacao($produtoId, $usuarioId, $nota, $comentario);

        if ($sucesso) {
            return redirect()->to("/avaliacoes/produto/{$produtoId}")
                ->with('msg', msg('Avaliação adicionada com sucesso', 'success'));
        } else {
            return redirect()->back()->with('msg', msg('Erro ao salvar avaliação', 'danger'));
        }
    }

    /**
     * Formulário para editar avaliação
     */
    public function editar($avaliacaoId)
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login');
        }

        $avaliacao = $this->avaliacoes->find($avaliacaoId);
        if (!$avaliacao) {
            return redirect()->to('/')->with('msg', msg('Avaliação não encontrada', 'danger'));
        }

        $usuarioId = $this->session->get('login')->usuarios_id;

        // Verifica se é o dono da avaliação
        if ($avaliacao->avaliacoes_usuario_id != $usuarioId) {
            return redirect()->to('/')->with('msg', msg('Sem permissão para editar esta avaliação', 'danger'));
        }

        $produto = $this->produtos->find($avaliacao->avaliacoes_produto_id);

        $data['title'] = 'Editar Avaliação';
        $data['produto'] = $produto;
        $data['avaliacao'] = $avaliacao;
        $data['action'] = 'editar';

        return view('avaliacoes/form', $data);
    }

    /**
     * Atualiza avaliação
     */
    public function atualizar()
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login');
        }

        $avaliacaoId = $this->request->getPost('avaliacao_id');
        $nota = $this->request->getPost('nota');
        $comentario = $this->request->getPost('comentario');
        $usuarioId = $this->session->get('login')->usuarios_id;

        // Validações
        if (!$avaliacaoId || !$nota) {
            return redirect()->back()->with('msg', msg('Dados obrigatórios não informados', 'danger'));
        }

        if ($nota < 1 || $nota > 5) {
            return redirect()->back()->with('msg', msg('Nota deve ser entre 1 e 5', 'danger'));
        }

        // Verifica se avaliação existe e pertence ao usuário
        $avaliacao = $this->avaliacoes->find($avaliacaoId);
        if (!$avaliacao || $avaliacao->avaliacoes_usuario_id != $usuarioId) {
            return redirect()->to('/')->with('msg', msg('Avaliação não encontrada', 'danger'));
        }

        // Atualiza avaliação
        $sucesso = $this->avaliacoes->atualizarAvaliacao($avaliacaoId, $nota, $comentario);

        if ($sucesso) {
            return redirect()->to("/avaliacoes/produto/{$avaliacao->avaliacoes_produto_id}")
                ->with('msg', msg('Avaliação atualizada com sucesso', 'success'));
        } else {
            return redirect()->back()->with('msg', msg('Erro ao atualizar avaliação', 'danger'));
        }
    }

    /**
     * Remove avaliação
     */
    public function remover($avaliacaoId)
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login');
        }

        $avaliacao = $this->avaliacoes->find($avaliacaoId);
        if (!$avaliacao) {
            return redirect()->to('/')->with('msg', msg('Avaliação não encontrada', 'danger'));
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        $userLevel = $this->session->get('login')->usuarios_nivel;

        // Verifica permissão (dono da avaliação ou admin)
        if ($avaliacao->avaliacoes_usuario_id != $usuarioId && $userLevel != 2) {
            return redirect()->to('/')->with('msg', msg('Sem permissão para remover esta avaliação', 'danger'));
        }

        $produtoId = $avaliacao->avaliacoes_produto_id;
        $sucesso = $this->avaliacoes->delete($avaliacaoId);

        if ($sucesso) {
            return redirect()->to("/avaliacoes/produto/{$produtoId}")
                ->with('msg', msg('Avaliação removida com sucesso', 'success'));
        } else {
            return redirect()->back()->with('msg', msg('Erro ao remover avaliação', 'danger'));
        }
    }

    /**
     * Modera avaliação (admin)
     */
    public function moderar()
    {
        if (!$this->verificarLogin() || $this->session->get('login')->usuarios_nivel != 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $avaliacaoId = $this->request->getPost('avaliacao_id');
        $status = $this->request->getPost('status');

        $sucesso = $this->avaliacoes->moderarAvaliacao($avaliacaoId, $status);

        if ($sucesso) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status da avaliação atualizado'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar status'
            ]);
        }
    }

    /**
     * Lista avaliações recentes (admin)
     */
    public function recentes()
    {
        if (!$this->verificarLogin() || $this->session->get('login')->usuarios_nivel != 2) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $data['title'] = 'Avaliações Recentes';
        $data['avaliacoes'] = $this->avaliacoes->getAvaliacoesRecentes(50);

        return view('avaliacoes/recentes', $data);
    }

    /**
     * Verifica se usuário está logado
     */
    private function verificarLogin()
    {
        return $this->session->has('login') && $this->session->get('login')->logged_in;
    }
}
