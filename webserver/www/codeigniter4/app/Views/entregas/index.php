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
    <h2 class="border-bottom border-2 border-primary mt-5 pt-3 mb-4"> <?= $title ?> </h2>

    <?php if (isset($msg))
            echo $msg; ?>

    <form action="<?= base_url('entregas/search'); ?>" class="d-flex mb-3" role="search" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <br>

    <a href="<?= base_url('relatorios/11') ?>" target="_blank" class="btn btn-primary mb-3">
        <i class="fas fa-file-pdf"></i> Relatório de Entregas
    </a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Pedido</th>
                <th>Funcionário</th>
                <th>Endereço</th>
                <th>Status</th>
                <th>
                    <a class="btn btn-success" href="<?= base_url('entregas/new'); ?>">
                        Novo <i class="bi bi-plus-circle"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php foreach ($entregas as $entrega): ?>
            <tr>
                <td><strong><?= esc($entrega->entregas_id) ?></strong></td>
                <td><?= esc($entrega->pedido_id) ?></td>
                <td><?= esc($entrega->funcionario_nome ?? 'N/A') ?></td>
                <td><?= esc($entrega->enderecos_rua ?? 'N/A') ?></td>
                <td><?= esc($entrega->status_entrega) ?></td>
                <td>
                    <a class="btn btn-primary" href="<?= base_url('entregas/edit/' . $entrega->entregas_id); ?>">
                        Editar <i class="bi bi-pencil-square"></i>
                    </a>
                    <a class="btn btn-danger" href="<?= base_url('entregas/delete/' . $entrega->entregas_id); ?>">
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