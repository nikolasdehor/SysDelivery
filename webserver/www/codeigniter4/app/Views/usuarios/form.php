<?php
helper('functions');
session();

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    // Bloco do Admin (nível 2) - Permanece inalterado
    if ($login->usuarios_nivel == 2) {
        ?>
<?= $this->extend('Templates_admin') ?>
<?= $this->section('content') ?>

<div class="container pt-4 pb-5 bg-light">
    <h2 class="border-bottom border-2 border-primary">
        <?= ucfirst($form) . ' ' . $title ?>
    </h2>

    <form action="<?= base_url('usuarios/' . $op); ?>" method="post">
        <div class="mb-3">
            <label for="usuarios_nome" class="form-label"> Nome </label>
            <input type="text" class="form-control" name="usuarios_nome" value="<?= $usuarios->usuarios_nome; ?>"
                id="usuarios_nome">
        </div>

        <div class="mb-3">
            <label for="usuarios_sobrenome" class="form-label"> Sobrenome </label>
            <input type="text" class="form-control" name="usuarios_sobrenome"
                value="<?= $usuarios->usuarios_sobrenome; ?>" id="usuarios_sobrenome">
        </div>

        <div class="mb-3">
            <label for="usuarios_cpf" class="form-label"> CPF </label>
            <input type="text" class="form-control" name="usuarios_cpf" value="<?= $usuarios->usuarios_cpf; ?>"
                id="usuarios_cpf">
        </div>

        <div class="mb-3">
            <label for="usuarios_email" class="form-label"> E-mail </label>
            <input type="email" class="form-control" name="usuarios_email" value="<?= $usuarios->usuarios_email; ?>"
                id="usuarios_email">
        </div>

        <?php if ($op == 'create'): ?>
        <!-- Campos de senha apenas para criação de novo usuário -->
        <div class="mb-3">
            <label for="usuarios_senha" class="form-label">
                <i class="bi bi-lock"></i>
                Senha
            </label>
            <input type="password" class="form-control" name="usuarios_senha" id="usuarios_senha" required>
            <!-- O indicador de qualidade será inserido aqui pelo JavaScript -->
        </div>

        <div class="mb-3">
            <label for="usuarios_confirmar_senha" class="form-label">
                <i class="bi bi-lock"></i>
                Confirmar Senha
            </label>
            <input type="password" class="form-control" name="usuarios_confirmar_senha" id="usuarios_confirmar_senha" required>
            <!-- O indicador de confirmação será inserido aqui pelo JavaScript -->
        </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="usuarios_fone" class="form-label"> Fone </label>
            <input type="tel" class="form-control" name="usuarios_fone" value="<?= $usuarios->usuarios_fone; ?>"
                id="usuarios_fone">
        </div>

        <div class="mb-3">
            <label for="usuarios_data_nasc" class="form-label"> Data Nasc. </label>
            <input type="date" class="form-control" name="usuarios_data_nasc"
                value="<?= $usuarios->usuarios_data_nasc; ?>" id="usuarios_data_nasc">
        </div>

        <input type="hidden" name="usuarios_id" value="<?= $usuarios->usuarios_id; ?>">

        <div class="mb-3">
            <?php if ($op == 'create'): ?>
            <button class="btn btn-secondary" type="submit" id="btn-cadastrar-admin" disabled title="Complete todos os requisitos de senha para continuar">
                <?= ucfirst($form) ?> <i class="bi bi-floppy"></i>
            </button>
            <?php else: ?>
            <button class="btn btn-success" type="submit">
                <?= ucfirst($form) ?> <i class="bi bi-floppy"></i>
            </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?php if ($op == 'create'): ?>
<!-- CSS para validação de senha -->
<link rel="stylesheet" href="<?= base_url('assets/css/password-validator.css') ?>">

<!-- JavaScript para validação de senha -->
<script src="<?= base_url('assets/js/password-validator.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar validador de senha para tela administrativa (sem requisitos)
    const passwordValidator = new PasswordValidator('usuarios_senha', 'usuarios_confirmar_senha', 'btn-cadastrar-admin', {
        showRequirements: false,
        showStrengthBar: true,
        publicForm: false
    });

    // Validação adicional do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        if (!passwordValidator.isValid()) {
            e.preventDefault();
            alert('Por favor, complete todos os requisitos de senha antes de continuar.');
            return false;
        }

        // Validar outros campos obrigatórios
        const requiredFields = ['usuarios_nome', 'usuarios_sobrenome', 'usuarios_email', 'usuarios_cpf', 'usuarios_fone', 'usuarios_data_nasc'];
        for (let field of requiredFields) {
            const input = document.getElementById(field);
            if (!input.value.trim()) {
                e.preventDefault();
                alert(`Por favor, preencha o campo ${input.previousElementSibling.textContent.replace(/[^a-zA-Z\s]/g, '').trim()}.`);
                input.focus();
                return false;
            }
        }
    });
});
</script>
<?php endif; ?>

<?php
        // CORREÇÃO: Bloco combinado para Comum (0) e Funcionário (1)
    } elseif ($login->usuarios_nivel == 0 || $login->usuarios_nivel == 1) {

        // Carrega o template correto para cada nível
        if ($login->usuarios_nivel == 0) {
            echo $this->extend('Templates_user');
        } else {
            echo $this->extend('Templates_funcionario');
        }
        ?>
<?= $this->section('content') ?>

<div class="container pt-4 pb-5 bg-light">
    <?php if (isset($msg)) {
                echo $msg;
            } ?>
    <h2 class="border-bottom border-2 border-primary">
        <?= ucfirst($form) . ' ' . $title ?>
    </h2>

    <form action="<?= base_url('usuarios/' . $op); ?>" method="post">
        <div class="mb-3">
            <label for="usuarios_nome" class="form-label"> Nome </label>
            <input type="text" class="form-control" name="usuarios_nome" value="<?= $usuarios->usuarios_nome; ?>"
                id="usuarios_nome">
        </div>

        <div class="mb-3">
            <label for="usuarios_sobrenome" class="form-label"> Sobrenome </label>
            <input type="text" class="form-control" name="usuarios_sobrenome"
                value="<?= $usuarios->usuarios_sobrenome; ?>" id="usuarios_sobrenome">
        </div>

        <div class="mb-3">
            <label for="usuarios_cpf" class="form-label"> CPF </label>
            <input type="text" class="form-control" name="usuarios_cpf" value="<?= $usuarios->usuarios_cpf; ?>"
                id="usuarios_cpf">
        </div>

        <div class="mb-3">
            <label for="usuarios_email" class="form-label"> E-mail </label>
            <input type="email" class="form-control" name="usuarios_email" value="<?= $usuarios->usuarios_email; ?>"
                id="usuarios_email">
        </div>

        <div class="mb-3">
            <label for="usuarios_fone" class="form-label"> Telefone </label>
            <input type="tel" class="form-control" name="usuarios_fone" value="<?= $usuarios->usuarios_fone; ?>"
                id="usuarios_fone">
        </div>

        <div class="mb-3">
            <label for="usuarios_data_nasc" class="form-label"> Data Nasc. </label>
            <input type="date" class="form-control" name="usuarios_data_nasc"
                value="<?= $usuarios->usuarios_data_nasc; ?>" id="usuarios_data_nasc">
        </div>

        <input type="hidden" name="usuarios_id" value="<?= $usuarios->usuarios_id; ?>">
        <input type="hidden" name="usuarios_senha" value="<?= $usuarios->usuarios_senha; ?>">
        <input type="hidden" name="usuarios_nivel" value="<?= $usuarios->usuarios_nivel; ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit"> <?= ucfirst($form) ?> <i class="bi bi-floppy"></i></button>
            <a class="btn btn-danger" href="<?= base_url('usuarios/perfil/' . $usuarios->usuarios_id) ?>">Cancelar</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?php
    } else {
        // Se não for nenhum dos níveis permitidos
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
    }
} else {
    // Se não estiver logado
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}
?>