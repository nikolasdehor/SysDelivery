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

    <h2 class="border-bottom border-2 border-primary mt-5 pt-3 mb-4"> <?= $title ?> </h2>

    <?php if(isset($msg)){echo $msg;} ?>

    <form action="<?= base_url('clientes/search'); ?>" class="d-flex" role="search" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <br>

    <a href="<?= base_url('relatorios/10') ?>" target="_blank" class="btn btn-primary mb-3">
        <i class="fas fa-file-pdf"></i> Relatório de Clientes
    </a>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nome</th>
                <th scope="col">CPF</th>
                <th scope="col">
                    <a class="btn btn-success" href="<?= base_url('clientes/new'); ?>">
                        Novo
                        <i class="bi bi-plus-circle"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody class="table-group-divider">

            <!-- Aqui vai o laço de repetição -->
            <?php for($i=0; $i < count($clientes); $i++){ ?>
            <tr>
                <th scope="row"><?= $clientes[$i]->clientes_id; ?></th>
                <td><?= $clientes[$i]->usuarios_nome . ' ' . $clientes[$i]->usuarios_sobrenome; ?></td>
                <td><?= $clientes[$i]->usuarios_cpf; ?></td>
                <td>
                    <a class="btn btn-primary" href="<?= base_url('clientes/edit/'.$clientes[$i]->clientes_id); ?>">
                        Editar
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a class="btn btn-danger" href="<?= base_url('clientes/delete/'.$clientes[$i]->clientes_id); ?>">
                        Excluir
                        <i class="bi bi-x-circle"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>

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