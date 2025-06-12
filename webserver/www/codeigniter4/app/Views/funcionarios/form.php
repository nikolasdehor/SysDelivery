<?php
helper('functions');
session();
if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    if ($login->usuarios_nivel == 2) {

        ?>

<?= $this->extend('Templates_admin') ?>
<?= $this->section('content') ?>

<div class="container pt-4 pb-5 bg-light">
    <h2 class="border-bottom border-2 border-primary">
        <?= ucfirst($form) . ' ' . $title ?>
    </h2>

    <form action="<?= base_url('funcionarios/' . $op); ?>" method="post">
        <!-- SELECT DE USUÁRIOS -->
        <div class="mb-3">
            <label for="funcionarios_usuario_id" class="form-label">Usuário</label>
            <select class="form-select" name="funcionarios_usuario_id" id="funcionarios_usuario_id" required>
                <option value="">Selecione um usuário</option>
                <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario->usuarios_id ?>"
                    <?= isset($funcionarios->funcionarios_usuario_id) && $funcionarios->funcionarios_usuario_id == $usuario->usuarios_id ? 'selected' : '' ?>>
                    <?= esc($usuario->usuarios_nome . ' ' . $usuario->usuarios_sobrenome) ?> -
                    <?= esc($usuario->usuarios_cpf) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="funcionarios_cargo" class="form-label">Cargo</label>
            <input type="text" class="form-control" name="funcionarios_cargo"
                value="<?= $funcionarios->funcionarios_cargo ?? ''; ?>" id="funcionarios_cargo">
        </div>

        <div class="mb-3">
            <label for="funcionarios_salario" class="form-label">Salário</label>
            <input type="number" step="0.01" class="form-control" name="funcionarios_salario"
                value="<?= $funcionarios->funcionarios_salario ?? ''; ?>" id="funcionarios_salario">
        </div>

        <div class="mb-3">
            <label for="funcionarios_data_admissao" class="form-label">Data de Admissão</label>
            <input type="date" class="form-control" name="funcionarios_data_admissao"
                value="<?= $funcionarios->funcionarios_data_admissao ?? ''; ?>" id="funcionarios_data_admissao">
        </div>

        <div class="mb-3">
            <label for="funcionarios_observacoes" class="form-label">Observações</label>
            <textarea class="form-control" name="funcionarios_observacoes" id="funcionarios_observacoes"
                rows="4"><?= $funcionarios->funcionarios_observacoes ?? ''; ?></textarea>
        </div>

        <input type="hidden" name="funcionarios_id" value="<?= $funcionarios->funcionarios_id ?? ''; ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit">
                <?= ucfirst($form) ?> <i class="bi bi-floppy"></i>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?php
    }
    else{

        $data['msg'] = msg("Sem permissão de acesso!","danger");
        echo view('login',$data);
    }
}else{

    $data['msg'] = msg("O usuário não está logado!","danger");
    echo view('login',$data);
}

?>