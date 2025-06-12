<?php

namespace App\Models;

use CodeIgniter\Model;

class Notificacoes extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'notificacoes';
    protected $primaryKey       = 'notificacoes_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'notificacoes_usuario_id',
        'notificacoes_titulo',
        'notificacoes_mensagem',
        'notificacoes_tipo',
        'notificacoes_lida',
        'notificacoes_data'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'notificacoes_usuario_id' => 'required|integer',
        'notificacoes_titulo' => 'required|max_length[255]',
        'notificacoes_mensagem' => 'required',
        'notificacoes_tipo' => 'in_list[info,success,warning,danger]'
    ];

    protected $validationMessages = [
        'notificacoes_usuario_id' => [
            'required' => 'O usuário é obrigatório',
            'integer' => 'ID do usuário deve ser um número'
        ],
        'notificacoes_titulo' => [
            'required' => 'O título é obrigatório',
            'max_length' => 'O título deve ter no máximo 255 caracteres'
        ],
        'notificacoes_mensagem' => [
            'required' => 'A mensagem é obrigatória'
        ],
        'notificacoes_tipo' => [
            'in_list' => 'Tipo deve ser: info, success, warning ou danger'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Cria nova notificação
     */
    public function criarNotificacao($usuarioId, $titulo, $mensagem, $tipo = 'info')
    {
        return $this->insert([
            'notificacoes_usuario_id' => $usuarioId,
            'notificacoes_titulo' => $titulo,
            'notificacoes_mensagem' => $mensagem,
            'notificacoes_tipo' => $tipo,
            'notificacoes_lida' => 0
        ]);
    }

    /**
     * Busca notificações do usuário
     */
    public function getNotificacoesUsuario($usuarioId, $limite = 20, $apenasNaoLidas = false)
    {
        $builder = $this->where('notificacoes_usuario_id', $usuarioId);
        
        if ($apenasNaoLidas) {
            $builder->where('notificacoes_lida', 0);
        }
        
        return $builder->orderBy('notificacoes_data', 'DESC')
            ->limit($limite)
            ->findAll();
    }

    /**
     * Marca notificação como lida
     */
    public function marcarComoLida($notificacaoId)
    {
        return $this->update($notificacaoId, ['notificacoes_lida' => 1]);
    }

    /**
     * Marca todas as notificações do usuário como lidas
     */
    public function marcarTodasComoLidas($usuarioId)
    {
        return $this->where('notificacoes_usuario_id', $usuarioId)
            ->set('notificacoes_lida', 1)
            ->update();
    }

    /**
     * Conta notificações não lidas do usuário
     */
    public function contarNaoLidas($usuarioId)
    {
        return $this->where('notificacoes_usuario_id', $usuarioId)
            ->where('notificacoes_lida', 0)
            ->countAllResults();
    }

    /**
     * Remove notificação
     */
    public function removerNotificacao($notificacaoId)
    {
        return $this->delete($notificacaoId);
    }

    /**
     * Remove todas as notificações lidas do usuário
     */
    public function limparNotificacoesLidas($usuarioId)
    {
        return $this->where('notificacoes_usuario_id', $usuarioId)
            ->where('notificacoes_lida', 1)
            ->delete();
    }

    /**
     * Notificação para novo pedido
     */
    public function notificarNovoPedido($usuarioId, $pedidoId)
    {
        return $this->criarNotificacao(
            $usuarioId,
            'Novo Pedido Realizado',
            "Seu pedido #{$pedidoId} foi realizado com sucesso!",
            'success'
        );
    }

    /**
     * Notificação para status do pedido
     */
    public function notificarStatusPedido($usuarioId, $pedidoId, $status)
    {
        $mensagens = [
            'confirmado' => 'Seu pedido foi confirmado e está sendo preparado.',
            'preparando' => 'Seu pedido está sendo preparado.',
            'saiu_entrega' => 'Seu pedido saiu para entrega!',
            'entregue' => 'Seu pedido foi entregue com sucesso!',
            'cancelado' => 'Seu pedido foi cancelado.'
        ];

        $tipos = [
            'confirmado' => 'info',
            'preparando' => 'info',
            'saiu_entrega' => 'warning',
            'entregue' => 'success',
            'cancelado' => 'danger'
        ];

        $mensagem = $mensagens[$status] ?? 'Status do pedido atualizado.';
        $tipo = $tipos[$status] ?? 'info';

        return $this->criarNotificacao(
            $usuarioId,
            "Pedido #{$pedidoId} - " . ucfirst(str_replace('_', ' ', $status)),
            $mensagem,
            $tipo
        );
    }

    /**
     * Notificação para promoção
     */
    public function notificarPromocao($usuarioId, $titulo, $mensagem)
    {
        return $this->criarNotificacao(
            $usuarioId,
            $titulo,
            $mensagem,
            'info'
        );
    }

    /**
     * Envia notificação para todos os usuários
     */
    public function notificarTodosUsuarios($titulo, $mensagem, $tipo = 'info')
    {
        $usuariosModel = new \App\Models\Usuarios();
        $usuarios = $usuariosModel->findAll();

        $sucesso = 0;
        foreach ($usuarios as $usuario) {
            if ($this->criarNotificacao($usuario->usuarios_id, $titulo, $mensagem, $tipo)) {
                $sucesso++;
            }
        }

        return $sucesso;
    }

    /**
     * Remove notificações antigas (mais de 30 dias)
     */
    public function limparNotificacoesAntigas()
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime('-30 days'));
        return $this->where('notificacoes_data <', $dataLimite)->delete();
    }

    /**
     * Busca estatísticas de notificações
     */
    public function getEstatisticasNotificacoes()
    {
        return [
            'total' => $this->countAll(),
            'nao_lidas' => $this->where('notificacoes_lida', 0)->countAllResults(),
            'por_tipo' => [
                'info' => $this->where('notificacoes_tipo', 'info')->countAllResults(),
                'success' => $this->where('notificacoes_tipo', 'success')->countAllResults(),
                'warning' => $this->where('notificacoes_tipo', 'warning')->countAllResults(),
                'danger' => $this->where('notificacoes_tipo', 'danger')->countAllResults()
            ]
        ];
    }
}
