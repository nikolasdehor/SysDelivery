<?php
helper('functions');
session();

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    if ($login->usuarios_nivel == 2) {
        echo $this->extend('Templates_admin');
    } elseif ($login->usuarios_nivel == 1) {
        echo $this->extend('Templates_funcionario');
    } else {
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
        return;
    }
    ?>

<?= $this->section('content') ?>

<div class="container">

    <h2 class="border-bottom border-2 border-primary mt-3 mb-4"> <?= $title ?> </h2>

    <?php if (isset($msg))
            echo $msg; ?>

    <form action="<?= base_url('categorias/search'); ?>" class="d-flex" role="search" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <br>
    <a href="<?= base_url('relatorios/5') ?>" target="_blank" class="btn btn-primary mb-3">
        <i class="fas fa-file-pdf"></i> Relatório de Categorias
    </a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Categoria</th>
                <th>
                    <a class="btn btn-success" href="<?= base_url('categorias/new'); ?>">
                        Novo <i class="bi bi-plus-circle"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php foreach ($categorias as $categoria): ?>
            <tr>
                <th scope="row"><?= $categoria->categorias_id; ?></th>
                <td><?= esc($categoria->categorias_nome); ?></td>
                <td>
                    <a class="btn btn-primary" href="<?= base_url('categorias/edit/' . $categoria->categorias_id); ?>">
                        Editar <i class="bi bi-pencil-square"></i>
                    </a>
                    <a class="btn btn-danger" href="<?= base_url('categorias/delete/' . $categoria->categorias_id); ?>">
                        Excluir <i class="bi bi-x-circle"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?= $this->endSection() ?>

<?php
} else {
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}
?>