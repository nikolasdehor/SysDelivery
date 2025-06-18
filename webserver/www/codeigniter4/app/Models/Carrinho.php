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

    public $usarSessao = false; // Usar banco de dados quando disponível

    /**
     * Verifica se deve usar sessão ou banco de dados
     */
    private function deveUsarSessao($usuarioId)
    {
        if ($this->usarSessao) {
            return true;
        }

        try {
            // Verificar se há dados no banco para este usuário
            $count = $this->where('carrinho_usuario_id', $usuarioId)->countAllResults();

            // Se não há dados no banco, verificar se há na sessão
            if ($count == 0) {
                $session = \Config\Services::session();
                $carrinhoSessao = $session->get('carrinho_' . $usuarioId) ?? [];

                // Se há dados na sessão, usar sessão
                if (!empty($carrinhoSessao)) {
                    log_message('info', "Usando sessão pois há dados na sessão mas não no banco");
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            log_message('error', "Erro ao verificar carrinho: " . $e->getMessage());
            return true; // Fallback para sessão
        }
    }

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
        // Verificar dinamicamente se deve usar sessão
        if ($this->deveUsarSessao($usuarioId)) {
            return $this->getCarrinhoSessao($usuarioId);
        }

        try {
            return $this->select('carrinho.*, produtos.produtos_nome, produtos.produtos_descricao, produtos.produtos_preco_venda, imgprodutos.imgprodutos_link')
                ->join('produtos', 'produtos.produtos_id = carrinho.carrinho_produto_id')
                ->join('imgprodutos', 'imgprodutos.imgprodutos_produtos_id = produtos.produtos_id', 'left')
                ->where('carrinho_usuario_id', $usuarioId)
                ->findAll();
        } catch (\Exception $e) {
            // Fallback para sessão se a tabela não existir
            return $this->getCarrinhoSessao($usuarioId);
        }
    }

    /**
     * Fallback: Busca carrinho da sessão
     */
    private function getCarrinhoSessao($usuarioId)
    {
        $session = \Config\Services::session();
        $carrinho = $session->get('carrinho_' . $usuarioId) ?? [];

        if (empty($carrinho)) {
            return [];
        }

        $produtosModel = new \App\Models\Produtos();
        $imagensModel = new \App\Models\Imgprodutos();
        $itens = [];

        foreach ($carrinho as $item) {
            $produto = $produtosModel->find($item['produto_id']);
            if ($produto) {
                $imagem = $imagensModel->where('imgprodutos_produtos_id', $produto->produtos_id)->first();

                $itens[] = (object) [
                    'carrinho_id' => $item['produto_id'], // Usar produto_id como ID temporário
                    'carrinho_usuario_id' => $usuarioId,
                    'carrinho_produto_id' => $item['produto_id'],
                    'carrinho_quantidade' => $item['quantidade'],
                    'carrinho_preco_unitario' => $item['preco_unitario'],
                    'produtos_nome' => $produto->produtos_nome,
                    'produtos_descricao' => $produto->produtos_descricao,
                    'produtos_preco_venda' => $produto->produtos_preco_venda,
                    'imgprodutos_link' => $imagem ? $imagem->imgprodutos_link : null
                ];
            }
        }

        return $itens;
    }

    /**
     * Adiciona item ao carrinho ou atualiza quantidade se já existe
     */
    public function adicionarItem($usuarioId, $produtoId, $quantidade, $precoUnitario)
    {
        // Verificar dinamicamente se deve usar sessão
        if ($this->deveUsarSessao($usuarioId)) {
            return $this->adicionarItemSessao($usuarioId, $produtoId, $quantidade, $precoUnitario);
        }

        try {
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
        } catch (\Exception $e) {
            // Fallback para sessão
            return $this->adicionarItemSessao($usuarioId, $produtoId, $quantidade, $precoUnitario);
        }
    }

    /**
     * Fallback: Adiciona item na sessão
     */
    private function adicionarItemSessao($usuarioId, $produtoId, $quantidade, $precoUnitario)
    {
        $session = \Config\Services::session();
        $carrinho = $session->get('carrinho_' . $usuarioId) ?? [];

        $encontrado = false;
        foreach ($carrinho as &$item) {
            if ($item['produto_id'] == $produtoId) {
                $item['quantidade'] += $quantidade;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $carrinho[] = [
                'produto_id' => $produtoId,
                'quantidade' => $quantidade,
                'preco_unitario' => $precoUnitario
            ];
        }

        $session->set('carrinho_' . $usuarioId, $carrinho);
        return true;
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
        try {
            return $this->delete($carrinhoId);
        } catch (\Exception $e) {
            // Para sessão, $carrinhoId é o produto_id
            return $this->removerItemSessao($carrinhoId);
        }
    }

    /**
     * Limpa todo o carrinho do usuário
     */
    public function limparCarrinho($usuarioId)
    {
        try {
            return $this->where('carrinho_usuario_id', $usuarioId)->delete();
        } catch (\Exception $e) {
            // Fallback para sessão
            $session = \Config\Services::session();
            $session->remove('carrinho_' . $usuarioId);
            return true;
        }
    }

    /**
     * Fallback: Remove item da sessão
     */
    private function removerItemSessao($produtoId)
    {
        $session = \Config\Services::session();
        $usuarioId = $session->get('login')->usuarios_id ?? 0;
        $carrinho = $session->get('carrinho_' . $usuarioId) ?? [];

        $carrinho = array_filter($carrinho, function($item) use ($produtoId) {
            return $item['produto_id'] != $produtoId;
        });

        $session->set('carrinho_' . $usuarioId, array_values($carrinho));
        return true;
    }

    /**
     * Calcula o total do carrinho
     */
    public function calcularTotal($usuarioId)
    {
        // Verificar dinamicamente se deve usar sessão
        if ($this->deveUsarSessao($usuarioId)) {
            $session = \Config\Services::session();
            $carrinho = $session->get('carrinho_' . $usuarioId) ?? [];

            $total = 0;
            foreach ($carrinho as $item) {
                $total += $item['quantidade'] * $item['preco_unitario'];
            }

            log_message('info', "Total calculado via sessão: {$total}");
            return $total;
        }

        try {
            // Debug: verificar se há itens no carrinho
            $itens = $this->where('carrinho_usuario_id', $usuarioId)->findAll();
            log_message('info', "Itens no carrinho para usuário {$usuarioId}: " . count($itens));

            if (!empty($itens)) {
                foreach ($itens as $item) {
                    log_message('info', "Item: produto_id={$item->carrinho_produto_id}, quantidade={$item->carrinho_quantidade}, preco={$item->carrinho_preco_unitario}");
                }
            }

            $result = $this->selectSum('carrinho_quantidade * carrinho_preco_unitario', 'total')
                ->where('carrinho_usuario_id', $usuarioId)
                ->first();

            $total = $result ? $result->total : 0;
            log_message('info', "Total calculado via banco: {$total}");

            return $total;
        } catch (\Exception $e) {
            log_message('error', "Erro ao calcular total: " . $e->getMessage());
            // Fallback para sessão
            $session = \Config\Services::session();
            $carrinho = $session->get('carrinho_' . $usuarioId) ?? [];

            $total = 0;
            foreach ($carrinho as $item) {
                $total += $item['quantidade'] * $item['preco_unitario'];
            }

            return $total;
        }
    }

    /**
     * Conta total de itens no carrinho
     */
    public function contarItens($usuarioId)
    {
        // Verificar dinamicamente se deve usar sessão
        if ($this->deveUsarSessao($usuarioId)) {
            $session = \Config\Services::session();
            $carrinho = $session->get('carrinho_' . $usuarioId) ?? [];

            $totalItens = 0;
            foreach ($carrinho as $item) {
                $totalItens += $item['quantidade'];
            }

            return $totalItens;
        }

        try {
            $result = $this->selectSum('carrinho_quantidade', 'total_itens')
                ->where('carrinho_usuario_id', $usuarioId)
                ->first();

            return $result ? $result->total_itens : 0;
        } catch (\Exception $e) {
            // Fallback para sessão
            $session = \Config\Services::session();
            $carrinho = $session->get('carrinho_' . $usuarioId) ?? [];

            $totalItens = 0;
            foreach ($carrinho as $item) {
                $totalItens += $item['quantidade'];
            }

            return $totalItens;
        }
    }
}
