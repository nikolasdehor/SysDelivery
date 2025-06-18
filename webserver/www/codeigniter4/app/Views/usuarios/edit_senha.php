<?php
helper('functions');
session();
if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    if ($login->usuarios_nivel == 2) {

        ?>
<?= $this->extend('Templates_admin') ?>
<?= $this->section('content') ?>


<div class="container mt-5 pt-4 pb-5 bg-light">

    <?php if(isset($msg)){echo $msg;} ?>

    <h2 class="border-bottom border-2 border-primary">
        Alterar Senha
    </h2>

    <form action="<?= base_url('usuarios/salvar_senha'); ?>" method="post">

        <div class="mb-3">
            <label for="usuarios_senha_atual" class="form-label"> Senha Atual </label>
            <input type="password" class="form-control" name="usuarios_senha_atual" id="usuarios_senha_atual">
        </div>

        <div class="mb-3">
            <label for="usuarios_nova_senha" class="form-label"> Nova Senha </label>
            <input type="password" class="form-control" name="usuarios_nova_senha"
                value="<?= $usuarios->usuarios_nova_senha ?? '' ?>" id="usuarios_nova_senha" required>
            <!-- O indicador de qualidade será inserido aqui pelo JavaScript -->
        </div>

        <div class="mb-3">
            <label for="usuarios_confirmar_senha" class="form-label"> Confirma nova senha </label>
            <input type="password" class="form-control" name="usuarios_confirmar_senha"
                value="<?= $usuarios->usuarios_confirmar_senha ?? '' ?>" id="usuarios_confirmar_senha" required>
            <!-- O indicador de confirmação será inserido aqui pelo JavaScript -->
        </div>

        <input type="hidden" name="usuarios_id" value="<?= $login->usuarios_id;?>">

        <div class="mb-3">
            <button class="btn btn-secondary" type="submit" id="btn-alterar-senha" disabled title="Complete todos os requisitos de senha para continuar">
                Alterar senha <i class="bi bi-floppy"></i>
            </button>
            <a class="btn btn-danger" href="<?= base_url('usuarios/acess') ?>">Cancelar</a>
        </div>

    </form>

</div>

<?= $this->endSection() ?>

<?php 
        }elseif($login->usuarios_nivel == 0){

            ?>
<?= $this->extend('Templates_user') ?>
<?= $this->section('content') ?>


<div class="container mt-5 pt-4 pb-5 bg-light">

    <?php if(isset($msg)){echo $msg;} ?>

    <h2 class="border-bottom border-2 border-primary">
        Alterar Senha
    </h2>

    <form action="<?= base_url('usuarios/salvar_senha') ?>" method="post">

        <div class="mb-3">
            <label for="usuarios_senha_atual" class="form-label"> Senha Atual </label>
            <input type="password" class="form-control" name="usuarios_senha_atual" id="usuarios_senha_atual"
                value="<?= $forms->usuarios_senha_atual; ?>">
        </div>

        <div class="mb-3">
            <label for="usuarios_nova_senha" class="form-label"> Nova Senha </label>
            <input type="password" class="form-control" name="usuarios_nova_senha"
                value="<?= $forms->usuarios_nova_senha; ?>" id="usuarios_nova_senha_user" required>
            <!-- O indicador de qualidade será inserido aqui pelo JavaScript -->
        </div>

        <div class="mb-3">
            <label for="usuarios_confirmar_senha" class="form-label"> Confirma nova senha </label>
            <input type="password" class="form-control" name="usuarios_confirmar_senha"
                value="<?= $forms->usuarios_confirmar_senha; ?>" id="usuarios_confirmar_senha_user" required>
            <!-- O indicador de confirmação será inserido aqui pelo JavaScript -->
        </div>

        <input type="hidden" name="usuarios_id" value="<?= $forms->usuarios_id ?>">

        <div class="mb-3">
            <button class="btn btn-secondary" type="submit" id="btn-alterar-senha-user" disabled title="Complete todos os requisitos de senha para continuar">
                Alterar senha <i class="bi bi-floppy"></i>
            </button>
            <a class="btn btn-danger" href="<?= base_url('usuarios/perfil/' . $forms->usuarios_id) ?>">Cancelar</a>
        </div>

    </form>

</div>

<?= $this->endSection() ?>

<!-- CSS para validação de senha -->
<link rel="stylesheet" href="<?= base_url('assets/css/password-validator.css') ?>">

<!-- JavaScript para validação de senha -->
<script src="<?= base_url('assets/js/password-validator.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar qual versão da tela está sendo exibida
    const adminPasswordField = document.getElementById('usuarios_nova_senha');
    const userPasswordField = document.getElementById('usuarios_nova_senha_user');

    if (adminPasswordField) {
        // Versão admin (sem mostrar requisitos)
        const passwordValidator = new PasswordValidator('usuarios_nova_senha', 'usuarios_confirmar_senha', 'btn-alterar-senha', {
            showRequirements: false,
            showStrengthBar: true,
            publicForm: false
        });
    } else if (userPasswordField) {
        // Versão usuário (sem mostrar requisitos)
        const passwordValidator = new PasswordValidator('usuarios_nova_senha_user', 'usuarios_confirmar_senha_user', 'btn-alterar-senha-user', {
            showRequirements: false,
            showStrengthBar: true,
            publicForm: false
        });
    }

    // Validação adicional do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const currentPasswordField = document.getElementById('usuarios_senha_atual');
        if (!currentPasswordField.value.trim()) {
            e.preventDefault();
            alert('Por favor, informe a senha atual.');
            currentPasswordField.focus();
            return false;
        }
    });
});
</script>

<?php
    } else {

        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
    }
} else {

    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}

?>