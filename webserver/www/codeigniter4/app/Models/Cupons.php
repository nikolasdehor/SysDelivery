<?php

namespace App\Models;

use CodeIgniter\Model;

class Cupons extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cupons';
    protected $primaryKey       = 'cupons_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cupons_codigo',
        'cupons_descricao',
        'cupons_tipo',
        'cupons_valor',
        'cupons_valor_minimo',
        'cupons_data_inicio',
        'cupons_data_fim',
        'cupons_limite_uso',
        'cupons_usado',
        'cupons_ativo'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'cupons_codigo' => 'required|max_length[50]|is_unique[cupons.cupons_codigo]',
        'cupons_descricao' => 'required|max_length[255]',
        'cupons_tipo' => 'required|in_list[percentual,valor_fixo]',
        'cupons_valor' => 'required|decimal|greater_than[0]',
        'cupons_valor_minimo' => 'decimal',
        'cupons_data_inicio' => 'required|valid_date',
        'cupons_data_fim' => 'required|valid_date',
        'cupons_limite_uso' => 'integer'
    ];

    protected $validationMessages = [
        'cupons_codigo' => [
            'required' => 'O código do cupom é obrigatório',
            'max_length' => 'O código deve ter no máximo 50 caracteres',
            'is_unique' => 'Este código já existe'
        ],
        'cupons_descricao' => [
            'required' => 'A descrição é obrigatória',
            'max_length' => 'A descrição deve ter no máximo 255 caracteres'
        ],
        'cupons_tipo' => [
            'required' => 'O tipo do cupom é obrigatório',
            'in_list' => 'Tipo deve ser percentual ou valor_fixo'
        ],
        'cupons_valor' => [
            'required' => 'O valor do desconto é obrigatório',
            'decimal' => 'O valor deve ser um número decimal',
            'greater_than' => 'O valor deve ser maior que zero'
        ],
        'cupons_data_inicio' => [
            'required' => 'A data de início é obrigatória',
            'valid_date' => 'Data de início inválida'
        ],
        'cupons_data_fim' => [
            'required' => 'A data de fim é obrigatória',
            'valid_date' => 'Data de fim inválida'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Valida se um cupom pode ser usado
     */
    public function validarCupom($codigo, $valorPedido = 0)
    {
        $cupom = $this->where('cupons_codigo', $codigo)
            ->where('cupons_ativo', 1)
            ->first();

        if (!$cupom) {
            return ['valido' => false, 'erro' => 'Cupom não encontrado ou inativo'];
        }

        // Verifica datas
        $hoje = date('Y-m-d');
        if ($hoje < $cupom->cupons_data_inicio) {
            return ['valido' => false, 'erro' => 'Cupom ainda não está válido'];
        }

        if ($hoje > $cupom->cupons_data_fim) {
            return ['valido' => false, 'erro' => 'Cupom expirado'];
        }

        // Verifica valor mínimo
        if ($valorPedido < $cupom->cupons_valor_minimo) {
            return ['valido' => false, 'erro' => 'Valor mínimo do pedido não atingido'];
        }

        // Verifica limite de uso
        if ($cupom->cupons_limite_uso && $cupom->cupons_usado >= $cupom->cupons_limite_uso) {
            return ['valido' => false, 'erro' => 'Cupom esgotado'];
        }

        return ['valido' => true, 'cupom' => $cupom];
    }

    /**
     * Calcula o desconto do cupom
     */
    public function calcularDesconto($cupom, $valorPedido)
    {
        if ($cupom->cupons_tipo === 'percentual') {
            return ($valorPedido * $cupom->cupons_valor) / 100;
        } else {
            return min($cupom->cupons_valor, $valorPedido);
        }
    }

    /**
     * Aplica o cupom (incrementa contador de uso)
     */
    public function aplicarCupom($cupomId)
    {
        $cupom = $this->find($cupomId);
        if ($cupom) {
            return $this->update($cupomId, [
                'cupons_usado' => $cupom->cupons_usado + 1
            ]);
        }
        return false;
    }

    /**
     * Busca cupons ativos
     */
    public function getCuponsAtivos()
    {
        $hoje = date('Y-m-d');
        return $this->where('cupons_ativo', 1)
            ->where('cupons_data_inicio <=', $hoje)
            ->where('cupons_data_fim >=', $hoje)
            ->findAll();
    }

    /**
     * Busca cupons disponíveis (que ainda podem ser usados)
     */
    public function getCuponsDisponiveis()
    {
        $hoje = date('Y-m-d');
        return $this->where('cupons_ativo', 1)
            ->where('cupons_data_inicio <=', $hoje)
            ->where('cupons_data_fim >=', $hoje)
            ->groupStart()
                ->where('cupons_limite_uso IS NULL')
                ->orWhere('cupons_usado < cupons_limite_uso')
            ->groupEnd()
            ->findAll();
    }

    /**
     * Gera código único para cupom
     */
    public function gerarCodigoUnico($prefixo = 'CUP')
    {
        do {
            $codigo = $prefixo . strtoupper(substr(md5(uniqid()), 0, 8));
            $existe = $this->where('cupons_codigo', $codigo)->first();
        } while ($existe);

        return $codigo;
    }

    /**
     * Busca estatísticas de uso de cupons
     */
    public function getEstatisticasCupons()
    {
        return [
            'total_cupons' => $this->countAll(),
            'cupons_ativos' => $this->where('cupons_ativo', 1)->countAllResults(),
            'cupons_expirados' => $this->where('cupons_data_fim <', date('Y-m-d'))->countAllResults(),
            'total_usos' => $this->selectSum('cupons_usado')->first()->cupons_usado ?? 0
        ];
    }

    /**
     * Desativa cupom
     */
    public function desativarCupom($cupomId)
    {
        return $this->update($cupomId, ['cupons_ativo' => 0]);
    }

    /**
     * Ativa cupom
     */
    public function ativarCupom($cupomId)
    {
        return $this->update($cupomId, ['cupons_ativo' => 1]);
    }
}
