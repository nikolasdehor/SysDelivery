<?php
helper('functions');
session();
$template = 'Templates';

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    if ($login->usuarios_nivel == 0) {
        $template = 'Templates_user';
    } elseif ($login->usuarios_nivel == 1) {
        $template = 'Templates_funcionario';
    } elseif ($login->usuarios_nivel == 2) {
        $template = 'Templates_admin';
    }
}
?>

<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
            <li class="breadcrumb-item active"><?= esc($produto->produtos_nome) ?></li>
            <li class="breadcrumb-item active">Avaliações</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <h2 class="border-bottom border-2 border-primary mb-4">
                <i class="bi bi-star"></i> Avaliações - <?= esc($produto->produtos_nome) ?>
            </h2>

            <?php if (session()->getFlashdata('msg')): ?>
                <?= session()->getFlashdata('msg') ?>
            <?php endif; ?>

            <!-- Resumo das Avaliações -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <h1 class="display-4 text-warning"><?= number_format($media, 1) ?></h1>
                            <div class="mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star<?= $i <= $media ? '-fill' : '' ?> text-warning"></i>
                                <?php endfor; ?>
                            </div>
                            <small class="text-muted"><?= $total_avaliacoes ?> avaliações</small>
                        </div>
                        <div class="col-md-8">
                            <?php if (isset($estatisticas)): ?>
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="me-2"><?= $i ?> <i class="bi bi-star-fill text-warning"></i></span>
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <?php 
                                            $porcentagem = $total_avaliacoes > 0 ? ($estatisticas[$i] / $total_avaliacoes) * 100 : 0;
                                            ?>
                                            <div class="progress-bar bg-warning" style="width: <?= $porcentagem ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?= $estatisticas[$i] ?></small>
                                    </div>
                                <?php endfor; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botão para Avaliar -->
            <?php if (isset($_SESSION['login']) && !$ja_avaliou): ?>
                <div class="mb-4">
                    <a href="<?= base_url('/avaliacoes/adicionar/' . $produto->produtos_id) ?>" 
                       class="btn btn-primary">
                        <i class="bi bi-star"></i> Avaliar este produto
                    </a>
                </div>
            <?php elseif (!isset($_SESSION['login'])): ?>
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle"></i> 
                    <a href="<?= base_url('/login') ?>">Faça login</a> para avaliar este produto.
                </div>
            <?php elseif ($ja_avaliou): ?>
                <div class="alert alert-success mb-4">
                    <i class="bi bi-check-circle"></i> Você já avaliou este produto.
                </div>
            <?php endif; ?>

            <!-- Lista de Avaliações -->
            <?php if (isset($avaliacoes) && count($avaliacoes) > 0): ?>
                <div class="avaliacoes-lista">
                    <?php foreach ($avaliacoes as $avaliacao): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1"><?= esc($avaliacao->usuarios_nome . ' ' . $avaliacao->usuarios_sobrenome) ?></h6>
                                        <div class="mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?= $i <= $avaliacao->avaliacoes_nota ? '-fill' : '' ?> text-warning"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($avaliacao->avaliacoes_data)) ?></small>
                                </div>
                                
                                <?php if (!empty($avaliacao->avaliacoes_comentario)): ?>
                                    <p class="mb-0"><?= esc($avaliacao->avaliacoes_comentario) ?></p>
                                <?php endif; ?>

                                <?php if (isset($_SESSION['login']) && 
                                         ($_SESSION['login']->usuarios_id == $avaliacao->avaliacoes_usuario_id || 
                                          $_SESSION['login']->usuarios_nivel == 2)): ?>
                                    <div class="mt-2">
                                        <?php if ($_SESSION['login']->usuarios_id == $avaliacao->avaliacoes_usuario_id): ?>
                                            <a href="<?= base_url('/avaliacoes/editar/' . $avaliacao->avaliacoes_id) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="<?= base_url('/avaliacoes/remover/' . $avaliacao->avaliacoes_id) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Tem certeza que deseja remover esta avaliação?')">
                                            <i class="bi bi-trash"></i> Remover
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-star display-1 text-muted"></i>
                    <h4 class="mt-3">Nenhuma avaliação ainda</h4>
                    <p class="text-muted">Seja o primeiro a avaliar este produto!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar com informações do produto -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?= esc($produto->produtos_nome) ?></h5>
                    <p class="card-text"><?= esc($produto->produtos_descricao) ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 text-success mb-0">
                            R$ <?= number_format($produto->produtos_preco_venda, 2, ',', '.') ?>
                        </span>
                        <a href="<?= base_url('/pedidos/produto/' . $produto->produtos_id) ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-cart-plus"></i> Comprar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Rápidas -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-graph-up"></i> Estatísticas</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Nota Média:</span>
                        <strong class="text-warning"><?= number_format($media, 1) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total de Avaliações:</span>
                        <strong><?= $total_avaliacoes ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Recomendação:</span>
                        <strong class="<?= $media >= 4 ? 'text-success' : ($media >= 3 ? 'text-warning' : 'text-danger') ?>">
                            <?php if ($media >= 4): ?>
                                <i class="bi bi-hand-thumbs-up"></i> Recomendado
                            <?php elseif ($media >= 3): ?>
                                <i class="bi bi-hand-thumbs-up"></i> Bom
                            <?php else: ?>
                                <i class="bi bi-hand-thumbs-down"></i> Regular
                            <?php endif; ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
