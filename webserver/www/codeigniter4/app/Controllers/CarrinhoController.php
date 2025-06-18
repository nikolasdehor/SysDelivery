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
        $data['total_itens'] = $this->carrinho->contarItens($usuarioId);
        $data['cupons_disponiveis'] = $this->cupons->getCuponsDisponiveis();

        // Calcular total manualmente como fallback
        $totalManual = 0;
        foreach ($data['itens'] as $item) {
            $totalManual += $item->carrinho_quantidade * $item->carrinho_preco_unitario;
        }

        $totalModelo = $this->carrinho->calcularTotal($usuarioId);

        // Usar o total manual se o do modelo for zero
        $data['total'] = ($totalModelo > 0) ? $totalModelo : $totalManual;

        // Debug
        log_message('info', "Carrinho index - Usuario: {$usuarioId}, Total modelo: {$totalModelo}, Total manual: {$totalManual}, Total final: {$data['total']}, Itens: {$data['total_itens']}");
        log_message('info', "Itens no carrinho: " . print_r($data['itens'], true));

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

        // Receber dados JSON ou POST
        $input = $this->request->getJSON(true);
        if (!$input) {
            $input = $this->request->getPost();
        }

        $produtoId = $input['produto_id'] ?? null;
        $quantidade = $input['quantidade'] ?? 1;

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

        // Log para debug
        log_message('info', "Tentando adicionar ao carrinho: Usuario={$usuarioId}, Produto={$produtoId}, Quantidade={$quantidade}, Preco={$produto->produtos_preco_venda}");

        // Adiciona ao carrinho
        $sucesso = $this->carrinho->adicionarItem(
            $usuarioId,
            $produtoId,
            $quantidade,
            $produto->produtos_preco_venda
        );

        log_message('info', "Resultado adicionar carrinho: " . ($sucesso ? 'sucesso' : 'falha'));

        if ($sucesso) {
            $totalItens = $this->carrinho->contarItens($usuarioId);
            log_message('info', "Total de itens no carrinho: {$totalItens}");

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
            $itens = $this->carrinho->getCarrinhoComProdutos($usuarioId);

            // Calcular total manualmente
            $total = 0;
            foreach ($itens as $item) {
                $total += $item->carrinho_quantidade * $item->carrinho_preco_unitario;
            }

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
            $itens = $this->carrinho->getCarrinhoComProdutos($usuarioId);

            // Calcular total manualmente
            $total = 0;
            foreach ($itens as $item) {
                $total += $item->carrinho_quantidade * $item->carrinho_preco_unitario;
            }

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

        // Calcular total manualmente
        $itens = $this->carrinho->getCarrinhoComProdutos($usuarioId);
        $totalCarrinho = 0;
        foreach ($itens as $item) {
            $totalCarrinho += $item->carrinho_quantidade * $item->carrinho_preco_unitario;
        }

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

    /**
     * Página de checkout
     */
    public function checkout()
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login')->with('msg', msg('Faça login para finalizar o pedido', 'warning'));
        }

        $usuarioId = $this->session->get('login')->usuarios_id;

        // Verificar se há itens no carrinho
        $itens = $this->carrinho->getCarrinhoComProdutos($usuarioId);
        if (empty($itens)) {
            return redirect()->to('/carrinho')->with('msg', msg('Seu carrinho está vazio!', 'warning'));
        }

        // Buscar endereços do usuário
        $enderecosModel = new \App\Models\Enderecos();
        $enderecos = $enderecosModel
            ->join('cidades', 'cidades.cidades_id = enderecos.enderecos_cidade_id')
            ->select('enderecos.*, cidades.cidades_nome, cidades.cidades_uf')
            ->where('enderecos_usuario_id', $usuarioId)
            ->where('enderecos_status', 1)
            ->findAll();

        $data = [
            'title' => 'Finalizar Pedido',
            'itens' => $itens,
            'total' => $this->carrinho->calcularTotal($usuarioId),
            'total_itens' => $this->carrinho->contarItens($usuarioId),
            'enderecos' => $enderecos,
            'cupom_aplicado' => $this->session->get('cupom_aplicado')
        ];

        return view('carrinho/checkout', $data);
    }

    /**
     * Processa o pedido
     */
    public function processarPedido()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $usuarioId = $this->session->get('login')->usuarios_id;

        // Verificar se há itens no carrinho
        $itens = $this->carrinho->getCarrinhoComProdutos($usuarioId);
        if (empty($itens)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Carrinho vazio'
            ]);
        }

        $enderecoId = $this->request->getPost('endereco_id');
        $formaPagamento = $this->request->getPost('forma_pagamento');
        $observacoes = $this->request->getPost('observacoes') ?? '';

        if (!$enderecoId || !$formaPagamento) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Endereço e forma de pagamento são obrigatórios'
            ]);
        }

        // Buscar cliente
        $clientesModel = new \App\Models\Clientes();
        $cliente = $clientesModel->where('clientes_usuario_id', $usuarioId)->first();

        if (!$cliente) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cliente não encontrado'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calcular total
            $total = $this->carrinho->calcularTotal($usuarioId);
            $cupomAplicado = $this->session->get('cupom_aplicado');

            if ($cupomAplicado) {
                $total -= $cupomAplicado['desconto'];
            }

            // Criar pedido
            $pedidosModel = new \App\Models\Pedidos();
            $pedidoId = $pedidosModel->insert([
                'clientes_id' => $cliente->clientes_id,
                'enderecos_id' => $enderecoId,
                'total_pedido' => $total,
                'status' => 'pendente',
                'observacoes' => $observacoes,
                'data_pedido' => date('Y-m-d H:i:s')
            ]);

            // Criar itens do pedido
            $itensPedidoModel = new \App\Models\ItensPedido();
            foreach ($itens as $item) {
                $itensPedidoModel->insert([
                    'pedidos_id' => $pedidoId,
                    'produtos_id' => $item->carrinho_produto_id,
                    'quantidade' => $item->carrinho_quantidade
                ]);
            }

            // Criar venda
            $vendasModel = new \App\Models\Vendas();
            $vendasModel->insert([
                'pedidos_id' => $pedidoId,
                'forma_pagamento' => $formaPagamento,
                'valor_total' => $total,
                'observacoes' => $observacoes
            ]);

            // Aplicar cupom se houver
            if ($cupomAplicado) {
                $this->cupons->aplicarCupom($cupomAplicado['cupom_id']);
            }

            // Limpar carrinho
            $this->carrinho->limparCarrinho($usuarioId);

            // Remover cupom da sessão
            $this->session->remove('cupom_aplicado');

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erro na transação');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pedido realizado com sucesso!',
                'pedido_id' => $pedidoId
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro ao processar pedido: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao processar pedido. Tente novamente.'
            ]);
        }
    }

    /**
     * Método de teste para debug do carrinho
     */
    public function teste()
    {
        if (!$this->verificarLogin()) {
            return "Usuário não logado";
        }

        $usuarioId = $this->session->get('login')->usuarios_id;

        // Verificar dados diretamente no banco
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM carrinho WHERE carrinho_usuario_id = ?", [$usuarioId]);
        $carrinhoDb = $query->getResult();

        // Teste básico de adicionar item
        $resultado = $this->carrinho->adicionarItem($usuarioId, 1, 1, 10.50);

        $itens = $this->carrinho->getCarrinhoComProdutos($usuarioId);
        $total = $this->carrinho->calcularTotal($usuarioId);
        $totalItens = $this->carrinho->contarItens($usuarioId);

        // Verificar novamente após adicionar
        $query2 = $db->query("SELECT * FROM carrinho WHERE carrinho_usuario_id = ?", [$usuarioId]);
        $carrinhoDb2 = $query2->getResult();

        $debug = [
            'usuario_id' => $usuarioId,
            'carrinho_db_antes' => $carrinhoDb,
            'resultado_adicionar' => $resultado,
            'carrinho_db_depois' => $carrinhoDb2,
            'total_itens' => $totalItens,
            'total_valor' => $total,
            'itens_processados' => $itens,
            'sessao_carrinho' => $this->session->get('carrinho_' . $usuarioId),
            'usar_sessao' => $this->carrinho->usarSessao ?? 'propriedade não acessível'
        ];

        return '<pre>' . print_r($debug, true) . '</pre>';
    }

    /**
     * Debug direto do total
     */
    public function debugTotal()
    {
        if (!$this->verificarLogin()) {
            return "Usuário não logado";
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        $db = \Config\Database::connect();

        // Verificar dados no banco
        $query = $db->query("SELECT *, (carrinho_quantidade * carrinho_preco_unitario) as subtotal FROM carrinho WHERE carrinho_usuario_id = ?", [$usuarioId]);
        $itensDb = $query->getResult();

        // Calcular total manualmente
        $totalManual = 0;
        foreach ($itensDb as $item) {
            $totalManual += $item->subtotal;
        }

        // Verificar sessão
        $carrinhoSessao = $this->session->get('carrinho_' . $usuarioId) ?? [];
        $totalSessao = 0;
        foreach ($carrinhoSessao as $item) {
            $totalSessao += $item['quantidade'] * $item['preco_unitario'];
        }

        // Usar métodos do modelo
        $totalModelo = $this->carrinho->calcularTotal($usuarioId);
        $itensModelo = $this->carrinho->getCarrinhoComProdutos($usuarioId);

        $debug = [
            'usuario_id' => $usuarioId,
            'itens_banco' => $itensDb,
            'total_manual_banco' => $totalManual,
            'carrinho_sessao' => $carrinhoSessao,
            'total_sessao' => $totalSessao,
            'total_modelo' => $totalModelo,
            'count_itens_modelo' => count($itensModelo),
            'usar_sessao_flag' => $this->carrinho->usarSessao
        ];

        return '<pre>' . print_r($debug, true) . '</pre>';
    }
}
