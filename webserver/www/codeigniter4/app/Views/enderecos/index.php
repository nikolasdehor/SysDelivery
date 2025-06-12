<?php
helper('functions');
session();
 if(isset($_SESSION['login'])){
     $login = $_SESSION['login'];
     if($login->usuarios_nivel == 2){

?>

<?= $this->extend('Templates_admin') ?>
<?= $this->section('content') ?>

<div class="container">

    <h2 class="border-bottom border-2 border-primary mt-3 mb-4"> <?= esc($title) ?> </h2>

    <?php if (isset($msg))
        echo $msg; ?>

    <form action="<?= base_url('enderecos/search'); ?>" class="d-flex mb-3" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <br>

    <a href="<?= base_url('relatorios/3') ?>" target="_blank" class="btn btn-primary mb-3">
        <i class="fas fa-file-pdf"></i> Relatório de Endereços
    </a>


    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Rua</th>
                <th>Número</th>
                <th>Complemento</th>
                <th>Status</th>
                <th>Cidade</th>
                <th>Usuário</th>
                <th>
                    <a class="btn btn-success" href="<?= base_url('enderecos/new'); ?>">
                        Novo
                        <i class="bi bi-plus-circle"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php foreach ($enderecos as $e): ?>
            <tr>
                <td><strong><?= esc($e->enderecos_id) ?></strong></td>
                <td><?= esc($e->enderecos_rua) ?></td>
                <td><?= esc($e->enderecos_numero) ?></td>
                <td><?= esc($e->enderecos_complemento) ?></td>
                <td><?= $e->enderecos_status ? 'Ativo' : 'Inativo' ?></td>
                <td><?= esc($e->cidades_nome ?? 'N/A') ?></td>
                <td><?= esc($e->usuarios_nome ?? 'N/A') ?></td>
                <td>
                    <a class="btn btn-primary" href="<?= base_url('enderecos/edit/' . $e->enderecos_id); ?>">
                        Editar
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a class="btn btn-danger" href="<?= base_url('enderecos/delete/' . $e->enderecos_id); ?>">
                        Excluir
                        <i class="bi bi-x-circle"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>

</div>

<?= $this->endSection() ?>

<?php
     }else{

         $data['msg'] = msg("Sem permissão de acesso!","danger");
         echo view('login',$data);
     }
 }else{

     $data['msg'] = msg("O usuário não está logado!","danger");
     echo view('login',$data);
}

?>