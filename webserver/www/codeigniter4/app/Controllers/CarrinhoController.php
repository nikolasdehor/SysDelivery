<?php

namespace App\Controllers;

use App\Models\Carrinho as CarrinhoModel;
use App\Models\Produtos as ProdutosModel;
use App\Models\Cupons as CuponsModel;

class CarrinhoController extends BaseController
{
    private $carrinho;
    private $produtos;
    private $cupons;
    private $session;

    public function __construct()
    {
        $this->carrinho = new CarrinhoModel();
        $this->produtos = new ProdutosModel();
        $this->cupons = new CuponsModel();
        $this->session = \Config\Services::session();
        helper('functions');
    }

    /**
     * Exibe o carrinho
     */
    public function index()
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login')->with('msg', msg('Faça login para acessar o carrinho', 'warning'));
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        
        $data['title'] = 'Meu Carrinho';
        $data['itens'] = $this->carrinho->getCarrinhoComProdutos($usuarioId);
        $data['total'] = $this->carrinho->calcularTotal($usuarioId);
        $data['total_itens'] = $this->carrinho->contarItens($usuarioId);
        $data['cupons_disponiveis'] = $this->cupons->getCuponsDisponiveis();

        return view('carrinho/index', $data);
    }

    /**
     * Adiciona item ao carrinho
     */
    public function adicionar()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Faça login para adicionar itens ao carrinho'
            ]);
        }

        $produtoId = $this->request->getPost('produto_id');
        $quantidade = $this->request->getPost('quantidade') ?? 1;

        if (!$produtoId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Produto não informado'
            ]);
        }

        // Busca informações do produto
        $produto = $this->produtos->find($produtoId);
        if (!$produto) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Produto não encontrado'
            ]);
        }

        $usuarioId = $this->session->get('login')->usuarios_id;

        // Adiciona ao carrinho
        $sucesso = $this->carrinho->adicionarItem(
            $usuarioId,
            $produtoId,
            $quantidade,
            $produto->produtos_preco_venda
        );

        if ($sucesso) {
            $totalItens = $this->carrinho->contarItens($usuarioId);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item adicionado ao carrinho',
                'total_itens' => $totalItens
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao adicionar item ao carrinho'
            ]);
        }
    }

    /**
     * Atualiza quantidade de um item
     */
    public function atualizarQuantidade()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $carrinhoId = $this->request->getPost('carrinho_id');
        $quantidade = $this->request->getPost('quantidade');

        if (!$carrinhoId || !$quantidade || $quantidade < 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos'
            ]);
        }

        $sucesso = $this->carrinho->atualizarQuantidade($carrinhoId, $quantidade);

        if ($sucesso) {
            $usuarioId = $this->session->get('login')->usuarios_id;
            $total = $this->carrinho->calcularTotal($usuarioId);
            $totalItens = $this->carrinho->contarItens($usuarioId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Quantidade atualizada',
                'total' => number_format($total, 2, ',', '.'),
                'total_itens' => $totalItens
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar quantidade'
            ]);
        }
    }

    /**
     * Remove item do carrinho
     */
    public function remover()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $carrinhoId = $this->request->getPost('carrinho_id');

        if (!$carrinhoId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item não informado'
            ]);
        }

        $sucesso = $this->carrinho->removerItem($carrinhoId);

        if ($sucesso) {
            $usuarioId = $this->session->get('login')->usuarios_id;
            $total = $this->carrinho->calcularTotal($usuarioId);
            $totalItens = $this->carrinho->contarItens($usuarioId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item removido do carrinho',
                'total' => number_format($total, 2, ',', '.'),
                'total_itens' => $totalItens
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao remover item'
            ]);
        }
    }

    /**
     * Limpa todo o carrinho
     */
    public function limpar()
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login');
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        $sucesso = $this->carrinho->limparCarrinho($usuarioId);

        if ($sucesso) {
            return redirect()->to('/carrinho')->with('msg', msg('Carrinho limpo com sucesso', 'success'));
        } else {
            return redirect()->to('/carrinho')->with('msg', msg('Erro ao limpar carrinho', 'danger'));
        }
    }

    /**
     * Aplica cupom de desconto
     */
    public function aplicarCupom()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $codigo = $this->request->getPost('codigo_cupom');
        $usuarioId = $this->session->get('login')->usuarios_id;
        $totalCarrinho = $this->carrinho->calcularTotal($usuarioId);

        if (!$codigo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Código do cupom não informado'
            ]);
        }

        // Valida o cupom
        $validacao = $this->cupons->validarCupom($codigo, $totalCarrinho);

        if (!$validacao['valido']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $validacao['erro']
            ]);
        }

        $cupom = $validacao['cupom'];
        $desconto = $this->cupons->calcularDesconto($cupom, $totalCarrinho);
        $totalComDesconto = $totalCarrinho - $desconto;

        // Salva o cupom na sessão
        $this->session->set('cupom_aplicado', [
            'cupom_id' => $cupom->cupons_id,
            'codigo' => $cupom->cupons_codigo,
            'desconto' => $desconto,
            'tipo' => $cupom->cupons_tipo,
            'valor' => $cupom->cupons_valor
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Cupom aplicado com sucesso',
            'desconto' => number_format($desconto, 2, ',', '.'),
            'total_com_desconto' => number_format($totalComDesconto, 2, ',', '.'),
            'cupom' => $cupom->cupons_descricao
        ]);
    }

    /**
     * Remove cupom aplicado
     */
    public function removerCupom()
    {
        $this->session->remove('cupom_aplicado');
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Cupom removido'
        ]);
    }

    /**
     * Conta itens no carrinho (para badge)
     */
    public function contarItens()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON(['total_itens' => 0]);
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        $totalItens = $this->carrinho->contarItens($usuarioId);

        return $this->response->setJSON(['total_itens' => $totalItens]);
    }

    /**
     * Verifica se usuário está logado
     */
    private function verificarLogin()
    {
        return $this->session->has('login') && $this->session->get('login')->logged_in;
    }
}
