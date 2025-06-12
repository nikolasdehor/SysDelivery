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
        <?= ucfirst($form) . ' ' . $title ?>
    </h2>

    <form action="<?= base_url('clientes/' . $op); ?>" method="post">
        <!-- SELECT DE USUÁRIOS -->
        <div class="mb-3">
            <label for="clientes_usuario_id" class="form-label">Usuário</label>
            <select class="form-select" name="clientes_usuario_id" id="clientes_usuario_id" required>
                <option value="">Selecione um usuário</option>
                <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario->usuarios_id ?>"
                    <?= isset($clientes->clientes_usuario_id) && $clientes->clientes_usuario_id == $usuario->usuarios_id ? 'selected' : '' ?>>
                    <?= esc($usuario->usuarios_nome . ' ' . $usuario->usuarios_sobrenome) ?> -
                    <?= esc($usuario->usuarios_cpf) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="clientes_observacoes" class="form-label">Observações (opcional)</label>
            <textarea class="form-control" name="clientes_observacoes" id="clientes_observacoes"
                rows="4"><?= $clientes->clientes_observacoes ?? ''; ?></textarea>
        </div>

        <input type="hidden" name="clientes_id" value="<?= $clientes->clientes_id ?? ''; ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit">
                <?= ucfirst($form) ?> <i class="bi bi-floppy"></i>
            </button>
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