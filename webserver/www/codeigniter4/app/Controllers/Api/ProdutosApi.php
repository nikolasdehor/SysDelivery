<?php

namespace App\Controllers\Api;

use App\Models\Produtos as ProdutosModel;
use App\Models\Categorias as CategoriasModel;
use App\Models\Imgprodutos as ImgprodutosModel;
use App\Models\Avaliacoes as AvaliacoesModel;

class ProdutosApi extends BaseApiController
{
    protected $produtos;
    protected $categorias;
    protected $imagens;
    protected $avaliacoes;

    public function __construct()
    {
        parent::__construct();
        $this->produtos = new ProdutosModel();
        $this->categorias = new CategoriasModel();
        $this->imagens = new ImgprodutosModel();
        $this->avaliacoes = new AvaliacoesModel();
    }

    /**
     * Lista todos os produtos
     * GET /api/produtos
     */
    public function index()
    {
        // Aplica CORS
        $corsCheck = $this->handleCors();
        if ($corsCheck !== true) return $corsCheck;

        // Aplica rate limiting
        $rateLimitCheck = $this->applyRateLimit();
        if ($rateLimitCheck !== true) return $rateLimitCheck;

        try {
            $page = $this->request->getGet('page') ?? 1;
            $perPage = $this->request->getGet('per_page') ?? 20;
            $categoria = $this->request->getGet('categoria');
            $search = $this->request->getGet('search');

            $builder = $this->produtos
                ->select('produtos.*, categorias.categorias_nome')
                ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id');

            // Filtro por categoria
            if ($categoria) {
                $builder->where('produtos.produtos_categorias_id', $categoria);
            }

            // Busca por nome ou descrição
            if ($search) {
                $builder->groupStart()
                    ->like('produtos.produtos_nome', $search)
                    ->orLike('produtos.produtos_descricao', $search)
                    ->groupEnd();
            }

            $total = $builder->countAllResults(false);
            $produtos = $builder->paginate($perPage, 'default', $page);

            // Adiciona imagens e avaliações para cada produto
            foreach ($produtos as &$produto) {
                $produto = $this->formatForApi($produto);
                
                // Busca imagens
                $imagens = $this->imagens->where('imgprodutos_produtos_id', $produto['produtos_id'])->findAll();
                $produto['imagens'] = array_map([$this, 'formatForApi'], $imagens);

                // Busca avaliações
                $produto['avaliacao_media'] = $this->avaliacoes->getMediaAvaliacoes($produto['produtos_id']);
                $produto['total_avaliacoes'] = $this->avaliacoes->contarAvaliacoes($produto['produtos_id']);
            }

            $this->logApiActivity('LIST_PRODUTOS', ['total' => $total, 'page' => $page]);

            return $this->respondPaginated($produtos, $total, $page, $perPage);

        } catch (\Exception $e) {
            return $this->respondError('Erro ao buscar produtos: ' . $e->getMessage());
        }
    }

    /**
     * Busca produto específico
     * GET /api/produtos/{id}
     */
    public function show($id = null)
    {
        // Aplica CORS
        $corsCheck = $this->handleCors();
        if ($corsCheck !== true) return $corsCheck;

        // Aplica rate limiting
        $rateLimitCheck = $this->applyRateLimit();
        if ($rateLimitCheck !== true) return $rateLimitCheck;

        try {
            if (!$id) {
                return $this->failValidationErrors('ID do produto é obrigatório');
            }

            $produto = $this->produtos
                ->select('produtos.*, categorias.categorias_nome')
                ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
                ->find($id);

            if (!$produto) {
                return $this->failNotFound('Produto não encontrado');
            }

            $produto = $this->formatForApi($produto);

            // Busca imagens
            $imagens = $this->imagens->where('imgprodutos_produtos_id', $id)->findAll();
            $produto['imagens'] = array_map([$this, 'formatForApi'], $imagens);

            // Busca avaliações
            $produto['avaliacao_media'] = $this->avaliacoes->getMediaAvaliacoes($id);
            $produto['total_avaliacoes'] = $this->avaliacoes->contarAvaliacoes($id);
            $produto['avaliacoes'] = array_map([$this, 'formatForApi'], 
                $this->avaliacoes->getAvaliacoesProduto($id));

            $this->logApiActivity('VIEW_PRODUTO', ['produto_id' => $id]);

            return $this->respondSuccess($produto);

        } catch (\Exception $e) {
            return $this->respondError('Erro ao buscar produto: ' . $e->getMessage());
        }
    }

    /**
     * Cria novo produto
     * POST /api/produtos
     */
    public function create()
    {
        // Aplica CORS
        $corsCheck = $this->handleCors();
        if ($corsCheck !== true) return $corsCheck;

        // Verifica autenticação
        $authCheck = $this->authenticateApi();
        if ($authCheck !== true) return $authCheck;

        // Verifica permissão (admin ou funcionário)
        $permissionCheck = $this->checkPermission(1);
        if ($permissionCheck !== true) return $permissionCheck;

        try {
            $rules = [
                'produtos_nome' => 'required|max_length[255]',
                'produtos_descricao' => 'required',
                'produtos_preco_custo' => 'required|decimal',
                'produtos_preco_venda' => 'required|decimal',
                'produtos_categorias_id' => 'required|integer'
            ];

            $data = $this->validateInput($rules);
            if (!is_array($data)) return $data;

            // Sanitiza dados
            $data = $this->sanitizeInput($data);

            // Verifica se categoria existe
            $categoria = $this->categorias->find($data['produtos_categorias_id']);
            if (!$categoria) {
                return $this->failValidationErrors('Categoria não encontrada');
            }

            $produtoId = $this->produtos->insert($data);

            if (!$produtoId) {
                return $this->respondError('Erro ao criar produto', 500, $this->produtos->errors());
            }

            $produto = $this->produtos->find($produtoId);
            $produto = $this->formatForApi($produto);

            $this->logApiActivity('CREATE_PRODUTO', ['produto_id' => $produtoId]);

            return $this->respondSuccess($produto, 'Produto criado com sucesso', 201);

        } catch (\Exception $e) {
            return $this->respondError('Erro ao criar produto: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza produto
     * PUT /api/produtos/{id}
     */
    public function update($id = null)
    {
        // Aplica CORS
        $corsCheck = $this->handleCors();
        if ($corsCheck !== true) return $corsCheck;

        // Verifica autenticação
        $authCheck = $this->authenticateApi();
        if ($authCheck !== true) return $authCheck;

        // Verifica permissão (admin ou funcionário)
        $permissionCheck = $this->checkPermission(1);
        if ($permissionCheck !== true) return $permissionCheck;

        try {
            if (!$id) {
                return $this->failValidationErrors('ID do produto é obrigatório');
            }

            $produto = $this->produtos->find($id);
            if (!$produto) {
                return $this->failNotFound('Produto não encontrado');
            }

            $rules = [
                'produtos_nome' => 'max_length[255]',
                'produtos_preco_custo' => 'decimal',
                'produtos_preco_venda' => 'decimal',
                'produtos_categorias_id' => 'integer'
            ];

            $data = $this->validateInput($rules);
            if (!is_array($data)) return $data;

            // Remove campos vazios
            $data = array_filter($data, function($value) {
                return $value !== null && $value !== '';
            });

            // Sanitiza dados
            $data = $this->sanitizeInput($data);

            // Verifica categoria se fornecida
            if (isset($data['produtos_categorias_id'])) {
                $categoria = $this->categorias->find($data['produtos_categorias_id']);
                if (!$categoria) {
                    return $this->failValidationErrors('Categoria não encontrada');
                }
            }

            $success = $this->produtos->update($id, $data);

            if (!$success) {
                return $this->respondError('Erro ao atualizar produto', 500, $this->produtos->errors());
            }

            $produto = $this->produtos->find($id);
            $produto = $this->formatForApi($produto);

            $this->logApiActivity('UPDATE_PRODUTO', ['produto_id' => $id]);

            return $this->respondSuccess($produto, 'Produto atualizado com sucesso');

        } catch (\Exception $e) {
            return $this->respondError('Erro ao atualizar produto: ' . $e->getMessage());
        }
    }

    /**
     * Remove produto
     * DELETE /api/produtos/{id}
     */
    public function delete($id = null)
    {
        // Aplica CORS
        $corsCheck = $this->handleCors();
        if ($corsCheck !== true) return $corsCheck;

        // Verifica autenticação
        $authCheck = $this->authenticateApi();
        if ($authCheck !== true) return $authCheck;

        // Verifica permissão (apenas admin)
        $permissionCheck = $this->checkPermission(2);
        if ($permissionCheck !== true) return $permissionCheck;

        try {
            if (!$id) {
                return $this->failValidationErrors('ID do produto é obrigatório');
            }

            $produto = $this->produtos->find($id);
            if (!$produto) {
                return $this->failNotFound('Produto não encontrado');
            }

            $success = $this->produtos->delete($id);

            if (!$success) {
                return $this->respondError('Erro ao remover produto');
            }

            $this->logApiActivity('DELETE_PRODUTO', ['produto_id' => $id]);

            return $this->respondSuccess(null, 'Produto removido com sucesso');

        } catch (\Exception $e) {
            return $this->respondError('Erro ao remover produto: ' . $e->getMessage());
        }
    }

    /**
     * Busca produtos por categoria
     * GET /api/produtos/categoria/{categoriaId}
     */
    public function porCategoria($categoriaId = null)
    {
        // Aplica CORS
        $corsCheck = $this->handleCors();
        if ($corsCheck !== true) return $corsCheck;

        try {
            if (!$categoriaId) {
                return $this->failValidationErrors('ID da categoria é obrigatório');
            }

            $categoria = $this->categorias->find($categoriaId);
            if (!$categoria) {
                return $this->failNotFound('Categoria não encontrada');
            }

            $produtos = $this->produtos
                ->select('produtos.*, categorias.categorias_nome')
                ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
                ->where('produtos.produtos_categorias_id', $categoriaId)
                ->findAll();

            $produtos = array_map([$this, 'formatForApi'], $produtos);

            return $this->respondSuccess([
                'categoria' => $this->formatForApi($categoria),
                'produtos' => $produtos
            ]);

        } catch (\Exception $e) {
            return $this->respondError('Erro ao buscar produtos da categoria: ' . $e->getMessage());
        }
    }
}
