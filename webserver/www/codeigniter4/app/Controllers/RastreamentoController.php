<?php

namespace App\Controllers;

use App\Models\Pedidos as PedidosModel;
use App\Models\Entregas as EntregasModel;
use App\Models\Notificacoes as NotificacoesModel;

class RastreamentoController extends BaseController
{
    private $pedidos;
    private $entregas;
    private $notificacoes;
    private $session;

    public function __construct()
    {
        $this->pedidos = new PedidosModel();
        $this->entregas = new EntregasModel();
        $this->notificacoes = new NotificacoesModel();
        $this->session = \Config\Services::session();
        helper('functions');
    }

    /**
     * Página de rastreamento público
     */
    public function rastrear($pedidoId = null)
    {
        if (!$pedidoId) {
            return redirect()->to('/')->with('msg', msg('ID do pedido não informado', 'danger'));
        }

        $pedido = $this->pedidos->find($pedidoId);
        if (!$pedido) {
            return redirect()->to('/')->with('msg', msg('Pedido não encontrado', 'danger'));
        }

        $data['title'] = 'Rastrear Pedido #' . $pedidoId;
        $data['pedido'] = $pedido;
        $data['historico'] = $this->getHistoricoPedido($pedidoId);
        $data['entrega'] = $this->getInfoEntrega($pedidoId);
        $data['timeline'] = $this->getTimelinePedido($pedido);

        return view('rastreamento/rastrear', $data);
    }

    /**
     * API para atualizar status do pedido
     */
    public function atualizarStatus()
    {
        if (!$this->verificarPermissaoFuncionario()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $pedidoId = $this->request->getPost('pedido_id');
        $novoStatus = $this->request->getPost('status');
        $observacoes = $this->request->getPost('observacoes') ?? '';

        if (!$pedidoId || !$novoStatus) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados obrigatórios não informados'
            ]);
        }

        $pedido = $this->pedidos->find($pedidoId);
        if (!$pedido) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pedido não encontrado'
            ]);
        }

        // Valida transição de status
        if (!$this->validarTransicaoStatus($pedido->status, $novoStatus)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Transição de status inválida'
            ]);
        }

        // Atualiza status do pedido
        $sucesso = $this->pedidos->update($pedidoId, [
            'status' => $novoStatus,
            'observacoes' => $observacoes
        ]);

        if ($sucesso) {
            // Registra no histórico
            $this->registrarHistorico($pedidoId, $pedido->status, $novoStatus, $observacoes);

            // Envia notificação para o cliente
            $this->enviarNotificacaoStatus($pedido, $novoStatus);

            // Se o status for "saiu_entrega", cria registro de entrega
            if ($novoStatus === 'saiu_entrega') {
                $this->criarRegistroEntrega($pedidoId);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'novo_status' => $novoStatus
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar status'
            ]);
        }
    }

    /**
     * API para buscar status atual do pedido
     */
    public function statusAtual($pedidoId)
    {
        $pedido = $this->pedidos->find($pedidoId);
        if (!$pedido) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pedido não encontrado'
            ]);
        }

        $timeline = $this->getTimelinePedido($pedido);
        $entrega = $this->getInfoEntrega($pedidoId);

        return $this->response->setJSON([
            'success' => true,
            'pedido' => [
                'id' => $pedido->pedidos_id,
                'status' => $pedido->status,
                'data_pedido' => $pedido->data_pedido,
                'observacoes' => $pedido->observacoes
            ],
            'timeline' => $timeline,
            'entrega' => $entrega
        ]);
    }

    /**
     * Página de gerenciamento de pedidos (funcionário/admin)
     */
    public function gerenciar()
    {
        if (!$this->verificarPermissaoFuncionario()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $status = $this->request->getGet('status') ?? 'todos';
        
        $data['title'] = 'Gerenciar Pedidos';
        $data['pedidos'] = $this->getPedidosPorStatus($status);
        $data['status_atual'] = $status;
        $data['contadores'] = $this->getContadoresPorStatus();

        return view('rastreamento/gerenciar', $data);
    }

    /**
     * Obtém histórico do pedido
     */
    private function getHistoricoPedido($pedidoId)
    {
        // Implementação básica - em produção, você teria uma tabela de histórico
        return [
            [
                'status' => 'pendente',
                'data' => date('Y-m-d H:i:s'),
                'observacoes' => 'Pedido realizado'
            ]
        ];
    }

    /**
     * Obtém informações de entrega
     */
    private function getInfoEntrega($pedidoId)
    {
        return $this->entregas
            ->select('entregas.*, funcionarios.funcionarios_cargo, usuarios.usuarios_nome')
            ->join('funcionarios', 'entregas.funcionario_id = funcionarios.funcionarios_id', 'left')
            ->join('usuarios', 'funcionarios.funcionarios_usuario_id = usuarios.usuarios_id', 'left')
            ->where('pedido_id', $pedidoId)
            ->first();
    }

    /**
     * Gera timeline do pedido
     */
    private function getTimelinePedido($pedido)
    {
        $statusFlow = [
            'pendente' => 'Pedido Recebido',
            'confirmado' => 'Pedido Confirmado',
            'preparando' => 'Preparando Pedido',
            'saiu_entrega' => 'Saiu para Entrega',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado'
        ];

        $timeline = [];
        $statusAtual = $pedido->status;
        
        foreach ($statusFlow as $status => $descricao) {
            $timeline[] = [
                'status' => $status,
                'descricao' => $descricao,
                'ativo' => $status === $statusAtual,
                'concluido' => $this->statusConcluido($status, $statusAtual),
                'data' => $status === $statusAtual ? $pedido->data_pedido : null
            ];

            // Para de adicionar se chegou no status cancelado
            if ($statusAtual === 'cancelado' && $status === 'cancelado') {
                break;
            }
        }

        return $timeline;
    }

    /**
     * Verifica se status foi concluído
     */
    private function statusConcluido($status, $statusAtual)
    {
        $ordem = [
            'pendente' => 1,
            'confirmado' => 2,
            'preparando' => 3,
            'saiu_entrega' => 4,
            'entregue' => 5,
            'cancelado' => 0
        ];

        if ($statusAtual === 'cancelado') {
            return $status === 'cancelado';
        }

        return isset($ordem[$status]) && isset($ordem[$statusAtual]) && 
               $ordem[$status] <= $ordem[$statusAtual];
    }

    /**
     * Valida transição de status
     */
    private function validarTransicaoStatus($statusAtual, $novoStatus)
    {
        $transicoesValidas = [
            'pendente' => ['confirmado', 'cancelado'],
            'confirmado' => ['preparando', 'cancelado'],
            'preparando' => ['saiu_entrega', 'cancelado'],
            'saiu_entrega' => ['entregue', 'cancelado'],
            'entregue' => [], // Status final
            'cancelado' => [] // Status final
        ];

        return isset($transicoesValidas[$statusAtual]) && 
               in_array($novoStatus, $transicoesValidas[$statusAtual]);
    }

    /**
     * Registra histórico de mudança de status
     */
    private function registrarHistorico($pedidoId, $statusAnterior, $novoStatus, $observacoes)
    {
        // Em uma implementação completa, você salvaria em uma tabela de histórico
        log_message('info', "Pedido {$pedidoId}: {$statusAnterior} -> {$novoStatus}. Obs: {$observacoes}");
    }

    /**
     * Envia notificação de mudança de status
     */
    private function enviarNotificacaoStatus($pedido, $novoStatus)
    {
        // Busca o usuário do pedido
        $cliente = $this->pedidos
            ->select('usuarios.usuarios_id')
            ->join('clientes', 'pedidos.clientes_id = clientes.clientes_id')
            ->join('usuarios', 'clientes.clientes_usuario_id = usuarios.usuarios_id')
            ->where('pedidos.pedidos_id', $pedido->pedidos_id)
            ->first();

        if ($cliente) {
            $this->notificacoes->notificarStatusPedido(
                $cliente->usuarios_id,
                $pedido->pedidos_id,
                $novoStatus
            );
        }
    }

    /**
     * Cria registro de entrega
     */
    private function criarRegistroEntrega($pedidoId)
    {
        // Busca endereço do pedido
        $endereco = $this->pedidos
            ->select('enderecos.enderecos_id')
            ->join('clientes', 'pedidos.clientes_id = clientes.clientes_id')
            ->join('enderecos', 'clientes.clientes_usuario_id = enderecos.enderecos_usuario_id')
            ->where('pedidos.pedidos_id', $pedidoId)
            ->where('enderecos.enderecos_status', 1)
            ->first();

        if ($endereco) {
            $this->entregas->insert([
                'pedido_id' => $pedidoId,
                'endereco_id' => $endereco->enderecos_id,
                'status_entrega' => 'em_transito',
                'data_saida' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Busca pedidos por status
     */
    private function getPedidosPorStatus($status)
    {
        $builder = $this->pedidos
            ->select('pedidos.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
            ->join('clientes', 'pedidos.clientes_id = clientes.clientes_id')
            ->join('usuarios', 'clientes.clientes_usuario_id = usuarios.usuarios_id');

        if ($status !== 'todos') {
            $builder->where('pedidos.status', $status);
        }

        return $builder->orderBy('pedidos.data_pedido', 'DESC')->findAll();
    }

    /**
     * Obtém contadores por status
     */
    private function getContadoresPorStatus()
    {
        $statuses = ['pendente', 'confirmado', 'preparando', 'saiu_entrega', 'entregue', 'cancelado'];
        $contadores = [];

        foreach ($statuses as $status) {
            $contadores[$status] = $this->pedidos->where('status', $status)->countAllResults();
        }

        $contadores['todos'] = $this->pedidos->countAll();

        return $contadores;
    }

    /**
     * Verifica se usuário está logado
     */
    private function verificarLogin()
    {
        return $this->session->has('login') && $this->session->get('login')->logged_in;
    }

    /**
     * Verifica permissão de funcionário ou superior
     */
    private function verificarPermissaoFuncionario()
    {
        return $this->verificarLogin() && $this->session->get('login')->usuarios_nivel >= 1;
    }
}
