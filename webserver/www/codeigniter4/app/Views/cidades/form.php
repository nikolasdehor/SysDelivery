<?php
    helper('functions');
    session();
     if(isset($_SESSION['login'])){
         $login = $_SESSION['login'];
         if($login->usuarios_nivel == 2){
    
?>
<?= $this->extend('Templates_admin') ?>
<?= $this->section('content') ?>


<div class="container pt-4 pb-5 bg-light">
    <h2 class="border-bottom border-2 border-primary">
        <?= ucfirst($form).' '.$title ?>
    </h2>

    <form action="<?= base_url('cidades/' . $op); ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="cidades_nome" class="form-label"> Cidade </label>
            <input type="text" class="form-control" name="cidades_nome" value="<?= $cidades->cidades_nome; ?>"
                id="cidades_nome">
        </div>

        <div class="mb-3">
            <label for="cidades_uf" class="form-label"> Estado </label>
            <input type="text" class="form-control" name="cidades_uf" value="<?= $cidades->cidades_uf; ?>"
                id="cidades_uf">
        </div>


        <input type="hidden" name="cidades_id" value="<?= $cidades->cidades_id; ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit"> <?= ucfirst($form)  ?> <i class="bi bi-floppy"></i></button>
        </div>

    </form>

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