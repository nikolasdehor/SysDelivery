<?php

namespace App\Models;

use CodeIgniter\Model;

class Avaliacoes extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'avaliacoes';
    protected $primaryKey       = 'avaliacoes_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'avaliacoes_produto_id',
        'avaliacoes_usuario_id',
        'avaliacoes_nota',
        'avaliacoes_comentario',
        'avaliacoes_data',
        'avaliacoes_status'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'avaliacoes_produto_id' => 'required|integer',
        'avaliacoes_usuario_id' => 'required|integer',
        'avaliacoes_nota' => 'required|integer|greater_than[0]|less_than[6]',
        'avaliacoes_comentario' => 'max_length[1000]'
    ];

    protected $validationMessages = [
        'avaliacoes_produto_id' => [
            'required' => 'O produto é obrigatório',
            'integer' => 'ID do produto deve ser um número'
        ],
        'avaliacoes_usuario_id' => [
            'required' => 'O usuário é obrigatório',
            'integer' => 'ID do usuário deve ser um número'
        ],
        'avaliacoes_nota' => [
            'required' => 'A nota é obrigatória',
            'integer' => 'A nota deve ser um número',
            'greater_than' => 'A nota deve ser entre 1 e 5',
            'less_than' => 'A nota deve ser entre 1 e 5'
        ],
        'avaliacoes_comentario' => [
            'max_length' => 'O comentário deve ter no máximo 1000 caracteres'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Busca avaliações de um produto com informações do usuário
     */
    public function getAvaliacoesProduto($produtoId, $ativas = true)
    {
        $builder = $this->select('avaliacoes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
            ->join('usuarios', 'usuarios.usuarios_id = avaliacoes.avaliacoes_usuario_id')
            ->where('avaliacoes_produto_id', $produtoId)
            ->orderBy('avaliacoes_data', 'DESC');

        if ($ativas) {
            $builder->where('avaliacoes_status', 1);
        }

        return $builder->findAll();
    }

    /**
     * Calcula a média de avaliações de um produto
     */
    public function getMediaAvaliacoes($produtoId)
    {
        $result = $this->selectAvg('avaliacoes_nota', 'media')
            ->where('avaliacoes_produto_id', $produtoId)
            ->where('avaliacoes_status', 1)
            ->first();
        
        return $result ? round($result->media, 1) : 0;
    }

    /**
     * Conta total de avaliações de um produto
     */
    public function contarAvaliacoes($produtoId)
    {
        return $this->where('avaliacoes_produto_id', $produtoId)
            ->where('avaliacoes_status', 1)
            ->countAllResults();
    }

    /**
     * Verifica se usuário já avaliou o produto
     */
    public function usuarioJaAvaliou($produtoId, $usuarioId)
    {
        return $this->where([
            'avaliacoes_produto_id' => $produtoId,
            'avaliacoes_usuario_id' => $usuarioId
        ])->first() !== null;
    }

    /**
     * Adiciona nova avaliação
     */
    public function adicionarAvaliacao($produtoId, $usuarioId, $nota, $comentario = '')
    {
        // Verifica se já existe avaliação
        if ($this->usuarioJaAvaliou($produtoId, $usuarioId)) {
            return false;
        }

        return $this->insert([
            'avaliacoes_produto_id' => $produtoId,
            'avaliacoes_usuario_id' => $usuarioId,
            'avaliacoes_nota' => $nota,
            'avaliacoes_comentario' => $comentario,
            'avaliacoes_status' => 1
        ]);
    }

    /**
     * Atualiza avaliação existente
     */
    public function atualizarAvaliacao($avaliacaoId, $nota, $comentario = '')
    {
        return $this->update($avaliacaoId, [
            'avaliacoes_nota' => $nota,
            'avaliacoes_comentario' => $comentario
        ]);
    }

    /**
     * Busca estatísticas de avaliações por nota
     */
    public function getEstatisticasAvaliacoes($produtoId)
    {
        $stats = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = $this->where('avaliacoes_produto_id', $produtoId)
                ->where('avaliacoes_nota', $i)
                ->where('avaliacoes_status', 1)
                ->countAllResults();
            $stats[$i] = $count;
        }
        return $stats;
    }

    /**
     * Busca avaliações recentes
     */
    public function getAvaliacoesRecentes($limite = 10)
    {
        return $this->select('avaliacoes.*, usuarios.usuarios_nome, produtos.produtos_nome')
            ->join('usuarios', 'usuarios.usuarios_id = avaliacoes.avaliacoes_usuario_id')
            ->join('produtos', 'produtos.produtos_id = avaliacoes.avaliacoes_produto_id')
            ->where('avaliacoes_status', 1)
            ->orderBy('avaliacoes_data', 'DESC')
            ->limit($limite)
            ->findAll();
    }

    /**
     * Modera avaliação (ativa/desativa)
     */
    public function moderarAvaliacao($avaliacaoId, $status)
    {
        return $this->update($avaliacaoId, ['avaliacoes_status' => $status]);
    }
}
