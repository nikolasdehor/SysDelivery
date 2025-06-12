<?php

namespace App\Controllers;

use App\Models\Notificacoes as NotificacoesModel;

class NotificacoesController extends BaseController
{
    private $notificacoes;
    private $session;

    public function __construct()
    {
        $this->notificacoes = new NotificacoesModel();
        $this->session = \Config\Services::session();
        helper('functions');
    }

    /**
     * Lista notificações do usuário
     */
    public function index()
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login')->with('msg', msg('Faça login para ver suas notificações', 'warning'));
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        
        $data['title'] = 'Minhas Notificações';
        $data['notificacoes'] = $this->notificacoes->getNotificacoesUsuario($usuarioId, 50);
        $data['nao_lidas'] = $this->notificacoes->contarNaoLidas($usuarioId);

        return view('notificacoes/index', $data);
    }

    /**
     * Marca notificação como lida
     */
    public function marcarLida()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $notificacaoId = $this->request->getPost('notificacao_id');
        
        if (!$notificacaoId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID da notificação não informado'
            ]);
        }

        // Verifica se a notificação pertence ao usuário
        $notificacao = $this->notificacoes->find($notificacaoId);
        $usuarioId = $this->session->get('login')->usuarios_id;

        if (!$notificacao || $notificacao->notificacoes_usuario_id != $usuarioId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notificação não encontrada'
            ]);
        }

        $sucesso = $this->notificacoes->marcarComoLida($notificacaoId);

        if ($sucesso) {
            $naoLidas = $this->notificacoes->contarNaoLidas($usuarioId);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notificação marcada como lida',
                'nao_lidas' => $naoLidas
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao marcar notificação como lida'
            ]);
        }
    }

    /**
     * Marca todas as notificações como lidas
     */
    public function marcarTodasLidas()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        $sucesso = $this->notificacoes->marcarTodasComoLidas($usuarioId);

        if ($sucesso) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Todas as notificações foram marcadas como lidas',
                'nao_lidas' => 0
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao marcar notificações como lidas'
            ]);
        }
    }

    /**
     * Remove notificação
     */
    public function remover()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão expirada'
            ]);
        }

        $notificacaoId = $this->request->getPost('notificacao_id');
        
        if (!$notificacaoId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID da notificação não informado'
            ]);
        }

        // Verifica se a notificação pertence ao usuário
        $notificacao = $this->notificacoes->find($notificacaoId);
        $usuarioId = $this->session->get('login')->usuarios_id;

        if (!$notificacao || $notificacao->notificacoes_usuario_id != $usuarioId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notificação não encontrada'
            ]);
        }

        $sucesso = $this->notificacoes->removerNotificacao($notificacaoId);

        if ($sucesso) {
            $naoLidas = $this->notificacoes->contarNaoLidas($usuarioId);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notificação removida',
                'nao_lidas' => $naoLidas
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao remover notificação'
            ]);
        }
    }

    /**
     * Limpa notificações lidas
     */
    public function limparLidas()
    {
        if (!$this->verificarLogin()) {
            return redirect()->to('/login');
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        $sucesso = $this->notificacoes->limparNotificacoesLidas($usuarioId);

        if ($sucesso) {
            return redirect()->to('/notificacoes')->with('msg', msg('Notificações lidas removidas', 'success'));
        } else {
            return redirect()->to('/notificacoes')->with('msg', msg('Erro ao limpar notificações', 'danger'));
        }
    }

    /**
     * Conta notificações não lidas (para badge)
     */
    public function contarNaoLidas()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON(['nao_lidas' => 0]);
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        $naoLidas = $this->notificacoes->contarNaoLidas($usuarioId);

        return $this->response->setJSON(['nao_lidas' => $naoLidas]);
    }

    /**
     * Busca notificações não lidas (para dropdown)
     */
    public function naoLidas()
    {
        if (!$this->verificarLogin()) {
            return $this->response->setJSON(['notificacoes' => []]);
        }

        $usuarioId = $this->session->get('login')->usuarios_id;
        $notificacoes = $this->notificacoes->getNotificacoesUsuario($usuarioId, 10, true);

        return $this->response->setJSON(['notificacoes' => $notificacoes]);
    }

    /**
     * Envia notificação para todos os usuários (admin)
     */
    public function enviarParaTodos()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $titulo = $this->request->getPost('titulo');
        $mensagem = $this->request->getPost('mensagem');
        $tipo = $this->request->getPost('tipo') ?: 'info';

        if (!$titulo || !$mensagem) {
            return redirect()->back()->with('msg', msg('Título e mensagem são obrigatórios', 'danger'));
        }

        $enviadas = $this->notificacoes->notificarTodosUsuarios($titulo, $mensagem, $tipo);

        return redirect()->back()->with('msg', msg("Notificação enviada para {$enviadas} usuários", 'success'));
    }

    /**
     * Formulário para enviar notificação geral (admin)
     */
    public function formEnviarGeral()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $data['title'] = 'Enviar Notificação Geral';
        return view('notificacoes/form_geral', $data);
    }

    /**
     * Estatísticas de notificações (admin)
     */
    public function estatisticas()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $data['title'] = 'Estatísticas de Notificações';
        $data['estatisticas'] = $this->notificacoes->getEstatisticasNotificacoes();

        return view('notificacoes/estatisticas', $data);
    }

    /**
     * Limpa notificações antigas (admin)
     */
    public function limparAntigas()
    {
        if (!$this->verificarPermissaoAdmin()) {
            return redirect()->to('/')->with('msg', msg('Sem permissão', 'danger'));
        }

        $removidas = $this->notificacoes->limparNotificacoesAntigas();

        return redirect()->back()->with('msg', msg("{$removidas} notificações antigas foram removidas", 'success'));
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
}
