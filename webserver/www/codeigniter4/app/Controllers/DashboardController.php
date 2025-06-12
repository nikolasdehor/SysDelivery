<?php

namespace App\Controllers;

use App\Models\Produtos as ProdutosModel;
use App\Models\Pedidos as PedidosModel;
use App\Models\Usuarios as UsuariosModel;
use App\Models\Vendas as VendasModel;
use App\Models\Avaliacoes as AvaliacoesModel;
use App\Models\Carrinho as CarrinhoModel;

class DashboardController extends BaseController
{
    private $produtos;
    private $pedidos;
    private $usuarios;
    private $vendas;
    private $avaliacoes;
    private $carrinho;
    private $session;

    public function __construct()
    {
        $this->produtos = new ProdutosModel();
        $this->pedidos = new PedidosModel();
        $this->usuarios = new UsuariosModel();
        $this->vendas = new VendasModel();
        $this->avaliacoes = new AvaliacoesModel();
        $this->carrinho = new CarrinhoModel();
        $this->session = \Config\Services::session();
        helper('functions');
    }

    /**
     * Dashboard principal (admin)
     */
    public function index()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $data['title'] = 'Dashboard';
        
        // Estatísticas gerais
        $data['stats'] = $this->getEstatisticasGerais();
        
        // Dados para gráficos
        $data['vendas_mes'] = $this->getVendasPorMes();
        $data['produtos_populares'] = $this->getProdutosMaisVendidos();
        $data['avaliacoes_recentes'] = $this->avaliacoes->getAvaliacoesRecentes(5);
        $data['pedidos_status'] = $this->getPedidosPorStatus();

        return view('dashboard/admin', $data);
    }

    /**
     * Dashboard do funcionário
     */
    public function funcionario()
    {
        if (!$this->verificarPermissaoFuncionario()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $data['title'] = 'Dashboard - Funcionário';
        
        // Estatísticas do dia
        $data['stats_hoje'] = $this->getEstatisticasHoje();
        $data['pedidos_pendentes'] = $this->getPedidosPendentes();
        $data['produtos_estoque_baixo'] = $this->getProdutosEstoqueBaixo();

        return view('dashboard/funcionario', $data);
    }

    /**
     * Dashboard do cliente
     */
    public function cliente()
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login')->with('msg', msg('Faça login para acessar', 'warning'));
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        
        $data['title'] = 'Minha Conta';
        $data['pedidos_recentes'] = $this->getPedidosUsuario($usuarioId, 5);
        $data['carrinho_itens'] = $this->carrinho->contarItens($usuarioId);
        $data['avaliacoes_usuario'] = $this->getAvaliacoesUsuario($usuarioId);

        return view('dashboard/cliente', $data);
    }

    /**
     * API para dados do dashboard
     */
    public function apiDados($tipo = null)
    {
        if (!$this->verificarPermissaoAdmin()) {
            return $this->response->setJSON(['error' => 'Sem permissão']);
        }

        switch ($tipo) {
            case 'vendas-mes':
                return $this->response->setJSON($this->getVendasPorMes());
            
            case 'produtos-populares':
                return $this->response->setJSON($this->getProdutosMaisVendidos());
            
            case 'pedidos-status':
                return $this->response->setJSON($this->getPedidosPorStatus());
            
            case 'stats-gerais':
                return $this->response->setJSON($this->getEstatisticasGerais());
            
            default:
                return $this->response->setJSON(['error' => 'Tipo de dados inválido']);
        }
    }

    /**
     * Estatísticas gerais do sistema
     */
    private function getEstatisticasGerais()
    {
        return [
            'total_usuarios' => $this->usuarios->countAll(),
            'total_produtos' => $this->produtos->countAll(),
            'total_pedidos' => $this->pedidos->countAll(),
            'total_vendas' => $this->vendas->countAll(),
            'receita_total' => $this->getReceitaTotal(),
            'receita_mes' => $this->getReceitaMes(),
            'pedidos_hoje' => $this->getPedidosHoje(),
            'novos_usuarios_mes' => $this->getNovosUsuariosMes()
        ];
    }

    /**
     * Estatísticas do dia atual
     */
    private function getEstatisticasHoje()
    {
        $hoje = date('Y-m-d');
        
        return [
            'pedidos_hoje' => $this->pedidos->where('DATE(data_pedido)', $hoje)->countAllResults(),
            'vendas_hoje' => $this->vendas->where('DATE(created_at)', $hoje)->countAllResults(),
            'receita_hoje' => $this->getReceitaHoje(),
            'novos_usuarios_hoje' => $this->usuarios->where('DATE(usuarios_data_cadastro)', $hoje)->countAllResults()
        ];
    }

    /**
     * Vendas por mês (últimos 12 meses)
     */
    private function getVendasPorMes()
    {
        $dados = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $mes = date('Y-m', strtotime("-{$i} months"));
            $vendas = $this->vendas->where('DATE_FORMAT(created_at, "%Y-%m")', $mes)->countAllResults();
            $receita = $this->getReceitaMesEspecifico($mes);
            
            $dados[] = [
                'mes' => date('M/Y', strtotime($mes . '-01')),
                'vendas' => $vendas,
                'receita' => $receita
            ];
        }
        
        return $dados;
    }

    /**
     * Produtos mais vendidos
     */
    private function getProdutosMaisVendidos($limite = 10)
    {
        return $this->produtos
            ->select('produtos.produtos_nome, COUNT(itens_pedido.produtos_id) as total_vendas')
            ->join('itens_pedido', 'produtos.produtos_id = itens_pedido.produtos_id')
            ->groupBy('produtos.produtos_id')
            ->orderBy('total_vendas', 'DESC')
            ->limit($limite)
            ->findAll();
    }

    /**
     * Pedidos por status
     */
    private function getPedidosPorStatus()
    {
        $statuses = ['pendente', 'confirmado', 'preparando', 'saiu_entrega', 'entregue', 'cancelado'];
        $dados = [];
        
        foreach ($statuses as $status) {
            $count = $this->pedidos->where('status', $status)->countAllResults();
            $dados[] = [
                'status' => ucfirst(str_replace('_', ' ', $status)),
                'count' => $count
            ];
        }
        
        return $dados;
    }

    /**
     * Pedidos pendentes
     */
    private function getPedidosPendentes()
    {
        return $this->pedidos
            ->select('pedidos.*, usuarios.usuarios_nome')
            ->join('clientes', 'pedidos.clientes_id = clientes.clientes_id')
            ->join('usuarios', 'clientes.clientes_usuario_id = usuarios.usuarios_id')
            ->whereIn('status', ['pendente', 'confirmado'])
            ->orderBy('data_pedido', 'ASC')
            ->findAll();
    }

    /**
     * Produtos com estoque baixo
     */
    private function getProdutosEstoqueBaixo($limite = 10)
    {
        return $this->produtos
            ->select('produtos.*, estoques.quantidade')
            ->join('estoques', 'produtos.produtos_id = estoques.produto_id', 'left')
            ->where('estoques.quantidade <=', 10)
            ->orWhere('estoques.quantidade IS NULL')
            ->limit($limite)
            ->findAll();
    }

    /**
     * Pedidos do usuário
     */
    private function getPedidosUsuario($usuarioId, $limite = 10)
    {
        return $this->pedidos
            ->select('pedidos.*')
            ->join('clientes', 'pedidos.clientes_id = clientes.clientes_id')
            ->where('clientes.clientes_usuario_id', $usuarioId)
            ->orderBy('data_pedido', 'DESC')
            ->limit($limite)
            ->findAll();
    }

    /**
     * Avaliações do usuário
     */
    private function getAvaliacoesUsuario($usuarioId)
    {
        return $this->avaliacoes
            ->select('avaliacoes.*, produtos.produtos_nome')
            ->join('produtos', 'avaliacoes.avaliacoes_produto_id = produtos.produtos_id')
            ->where('avaliacoes_usuario_id', $usuarioId)
            ->orderBy('avaliacoes_data', 'DESC')
            ->limit(5)
            ->findAll();
    }

    /**
     * Receita total
     */
    private function getReceitaTotal()
    {
        $result = $this->vendas->selectSum('valor_total')->first();
        return $result ? $result->valor_total : 0;
    }

    /**
     * Receita do mês atual
     */
    private function getReceitaMes()
    {
        $mesAtual = date('Y-m');
        return $this->getReceitaMesEspecifico($mesAtual);
    }

    /**
     * Receita de um mês específico
     */
    private function getReceitaMesEspecifico($mes)
    {
        $result = $this->vendas
            ->selectSum('valor_total')
            ->where('DATE_FORMAT(created_at, "%Y-%m")', $mes)
            ->first();
        return $result ? $result->valor_total : 0;
    }

    /**
     * Receita do dia atual
     */
    private function getReceitaHoje()
    {
        $hoje = date('Y-m-d');
        $result = $this->vendas
            ->selectSum('valor_total')
            ->where('DATE(created_at)', $hoje)
            ->first();
        return $result ? $result->valor_total : 0;
    }

    /**
     * Pedidos de hoje
     */
    private function getPedidosHoje()
    {
        $hoje = date('Y-m-d');
        return $this->pedidos->where('DATE(data_pedido)', $hoje)->countAllResults();
    }

    /**
     * Novos usuários do mês
     */
    private function getNovosUsuariosMes()
    {
        $mesAtual = date('Y-m');
        return $this->usuarios
            ->where('DATE_FORMAT(usuarios_data_cadastro, "%Y-%m")', $mesAtual)
            ->countAllResults();
    }

    /**
     * Verifica se usuário está logado
     */
    private function verificarLogin()
    {
        return $this->session->has('login') && $this->session->get('login')->logged_in;
    }

    /**
     * Verifica permissão de administrador
     */
    private function verificarPermissaoAdmin()
    {
        return $this->verificarLogin() && $this->session->get('login')->usuarios_nivel == 2;
    }

    /**
     * Verifica permissão de funcionário ou superior
     */
    private function verificarPermissaoFuncionario()
    {
        return $this->verificarLogin() && $this->session->get('login')->usuarios_nivel >= 1;
    }
}
