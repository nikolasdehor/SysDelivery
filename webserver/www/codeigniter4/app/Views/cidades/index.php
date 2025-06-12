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

    <h2 class="border-bottom border-2 border-primary mt-3 mb-4"> <?= $title ?> </h2>

    <?php if(isset($msg)){echo $msg;} ?>

    <form action="<?= base_url('cidades/search'); ?>" class="d-flex" role="search" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>


    <br>
    <a href="<?= base_url('relatorios/4') ?>" target="_blank" class="btn btn-primary mb-3">
        <i class="fas fa-file-pdf"></i> Relatório de Cidades
    </a>


    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Cidade</th>
                <th scope="col">Estado</th>
                <th scope="col">
                    <a class="btn btn-success" href="<?= base_url('cidades/new'); ?>">
                        Novo
                        <i class="bi bi-plus-circle"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody class="table-group-divider">

            <!-- Aqui vai o laço de repetição -->
            <?php for($i=0; $i < count($cidades); $i++){ ?>
            <tr>
                <th scope="row"><?= $cidades[$i]->cidades_id; ?></th>
                <td><?= $cidades[$i]->cidades_nome; ?></td>
                <td><?= $cidades[$i]->cidades_uf; ?></td>
                <td>
                    <a class="btn btn-primary" href="<?= base_url('cidades/edit/'.$cidades[$i]->cidades_id); ?>">
                        Editar
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a class="btn btn-danger" href="<?= base_url('cidades/delete/'.$cidades[$i]->cidades_id); ?>">
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