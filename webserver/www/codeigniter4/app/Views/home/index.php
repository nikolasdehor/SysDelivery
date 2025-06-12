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
    } else {
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
        return;
    }
}
?>

<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<!--Abre Produtos-->
<div id="produtos" class="container">
    <?php if (session()->getFlashdata('msg')): ?>
        <?= session()->getFlashdata('msg') ?>
    <?php endif; ?>

    <h2 class="border-bottom mt-3 border-2 border-primary">Produtos</h2>

    <div class="col mt-3 mb-3">
        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Pesquisar" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="row">
        <?php if (isset($imgprodutos) && count($imgprodutos) > 0): ?>
        <?php foreach ($imgprodutos as $produto): ?>
        <div class="col-sm-3 mb-3 pb-4 mb-sm-0">
            <div class="card">
                <img src="<?= base_url('assets/' . $produto->imgprodutos_link) ?>" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title"><?= esc($produto->produtos_nome) ?></h5>
                    <h5 class="card-title">
                        <b class="text-danger"> R$ <?= number_format($produto->produtos_preco_custo, 2, ',', '.') ?>
                        </b>
                    </h5>
                    <p class="card-text"><?= esc($produto->produtos_descricao) ?></p>
                    <p class="text-center">
                        <a href="<?= base_url('/pedidos/produto/' . $produto->produtos_id) ?>" class="btn btn-primary">
                            Comprar <i class="bi bi-basket2-fill"></i>
                        </a>
                    </p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Nenhum produto encontrado.
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection() ?>