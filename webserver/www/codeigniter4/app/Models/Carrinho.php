<?php

namespace App\Models;

use CodeIgniter\Model;

class Carrinho extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'carrinho';
    protected $primaryKey       = 'carrinho_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'carrinho_usuario_id',
        'carrinho_produto_id', 
        'carrinho_quantidade',
        'carrinho_preco_unitario',
        'carrinho_data_adicao'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'carrinho_usuario_id' => 'required|integer',
        'carrinho_produto_id' => 'required|integer',
        'carrinho_quantidade' => 'required|integer|greater_than[0]',
        'carrinho_preco_unitario' => 'required|decimal'
    ];

    protected $validationMessages = [
        'carrinho_usuario_id' => [
            'required' => 'O usuário é obrigatório',
            'integer' => 'ID do usuário deve ser um número'
        ],
        'carrinho_produto_id' => [
            'required' => 'O produto é obrigatório',
            'integer' => 'ID do produto deve ser um número'
        ],
        'carrinho_quantidade' => [
            'required' => 'A quantidade é obrigatória',
            'integer' => 'A quantidade deve ser um número',
            'greater_than' => 'A quantidade deve ser maior que zero'
        ],
        'carrinho_preco_unitario' => [
            'required' => 'O preço é obrigatório',
            'decimal' => 'O preço deve ser um valor decimal válido'
        ]
    ];

    protected $skipValidation = false;

    /**
     * Busca itens do carrinho com informações do produto
     */
    public function getCarrinhoComProdutos($usuarioId)
    {
        return $this->select('carrinho.*, produtos.produtos_nome, produtos.produtos_descricao, imgprodutos.imgprodutos_link')
            ->join('produtos', 'produtos.produtos_id = carrinho.carrinho_produto_id')
            ->join('imgprodutos', 'imgprodutos.imgprodutos_produtos_id = produtos.produtos_id', 'left')
            ->where('carrinho_usuario_id', $usuarioId)
            ->findAll();
    }

    /**
     * Adiciona item ao carrinho ou atualiza quantidade se já existe
     */
    public function adicionarItem($usuarioId, $produtoId, $quantidade, $precoUnitario)
    {
        // Verifica se o item já existe no carrinho
        $itemExistente = $this->where([
            'carrinho_usuario_id' => $usuarioId,
            'carrinho_produto_id' => $produtoId
        ])->first();

        if ($itemExistente) {
            // Atualiza a quantidade
            $novaQuantidade = $itemExistente->carrinho_quantidade + $quantidade;
            return $this->update($itemExistente->carrinho_id, [
                'carrinho_quantidade' => $novaQuantidade
            ]);
        } else {
            // Adiciona novo item
            return $this->insert([
                'carrinho_usuario_id' => $usuarioId,
                'carrinho_produto_id' => $produtoId,
                'carrinho_quantidade' => $quantidade,
                'carrinho_preco_unitario' => $precoUnitario
            ]);
        }
    }

    /**
     * Atualiza quantidade de um item específico
     */
    public function atualizarQuantidade($carrinhoId, $quantidade)
    {
        return $this->update($carrinhoId, ['carrinho_quantidade' => $quantidade]);
    }

    /**
     * Remove item do carrinho
     */
    public function removerItem($carrinhoId)
    {
        return $this->delete($carrinhoId);
    }

    /**
     * Limpa todo o carrinho do usuário
     */
    public function limparCarrinho($usuarioId)
    {
        return $this->where('carrinho_usuario_id', $usuarioId)->delete();
    }

    /**
     * Calcula o total do carrinho
     */
    public function calcularTotal($usuarioId)
    {
        $result = $this->selectSum('carrinho_quantidade * carrinho_preco_unitario', 'total')
            ->where('carrinho_usuario_id', $usuarioId)
            ->first();
        
        return $result ? $result->total : 0;
    }

    /**
     * Conta total de itens no carrinho
     */
    public function contarItens($usuarioId)
    {
        $result = $this->selectSum('carrinho_quantidade', 'total_itens')
            ->where('carrinho_usuario_id', $usuarioId)
            ->first();
        
        return $result ? $result->total_itens : 0;
    }
}
