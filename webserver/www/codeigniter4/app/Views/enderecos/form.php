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

    <form action="<?= base_url('enderecos/' . $op); ?>" method="post">
        <div class="mb-3">
            <label for="enderecos_rua" class="form-label">Rua</label>
            <input type="text" class="form-control" name="enderecos_rua" id="enderecos_rua"
                value="<?= esc($enderecos->enderecos_rua ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="enderecos_numero" class="form-label">Número</label>
            <input type="text" class="form-control" name="enderecos_numero" id="enderecos_numero"
                value="<?= esc($enderecos->enderecos_numero ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="enderecos_complemento" class="form-label">Complemento</label>
            <input type="text" class="form-control" name="enderecos_complemento" id="enderecos_complemento"
                value="<?= esc($enderecos->enderecos_complemento ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="enderecos_status" class="form-label">Status</label>
            <select name="enderecos_status" id="enderecos_status" class="form-select">
                <option value="1" <?= isset($enderecos) && $enderecos->enderecos_status == 1 ? 'selected' : '' ?>>Ativo
                </option>
                <option value="0" <?= isset($enderecos) && $enderecos->enderecos_status == 0 ? 'selected' : '' ?>>
                    Inativo
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="enderecos_cidade_id" class="form-label">Cidade</label>
            <select name="enderecos_cidade_id" id="enderecos_cidade_id" class="form-select" required>
                <option value="">-- Selecione uma cidade --</option>
                <?php if (isset($cidades) && is_array($cidades)): ?>
                <?php foreach ($cidades as $cidade): ?>
                <option value="<?= $cidade->cidades_id ?>"
                    <?= isset($enderecos) && $enderecos->enderecos_cidade_id == $cidade->cidades_id ? 'selected' : '' ?>>
                    <?= esc($cidade->cidades_nome) ?> (<?= esc($cidade->cidades_uf) ?>)
                </option>
                <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="enderecos_usuario_id" class="form-label">Usuário</label>
            <select name="enderecos_usuario_id" id="enderecos_usuario_id" class="form-select" required>
                <option value="">-- Selecione um usuário --</option>
                <?php if (isset($usuarios) && is_array($usuarios)): ?>
                <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario->usuarios_id ?>"
                    <?= isset($enderecos) && $enderecos->enderecos_usuario_id == $usuario->usuarios_id ? 'selected' : '' ?>>
                    <?= esc($usuario->usuarios_nome) ?>
                </option>
                <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <input type="hidden" name="enderecos_id" value="<?= esc($enderecos->enderecos_id ?? '') ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit"> <?= ucfirst($form) ?> <i class="bi bi-floppy"></i></button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?php
    }elseif($login->usuarios_nivel == 0){

        ?>

<?= $this->extend('Templates_user') ?>
<?= $this->section('content') ?>

<div class="container pt-4 pb-5 bg-light">
    <h2 class="border-bottom border-2 border-primary">
        <?= ucfirst($form) . ' ' . $title ?>
    </h2>

    <form action="<?= base_url('enderecos/' . $op); ?>" method="post">
        <div class="mb-3">
            <label for="enderecos_rua" class="form-label">Rua</label>
            <input type="text" class="form-control" name="enderecos_rua" id="enderecos_rua"
                value="<?= esc($enderecos->enderecos_rua ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="enderecos_numero" class="form-label">Número</label>
            <input type="text" class="form-control" name="enderecos_numero" id="enderecos_numero"
                value="<?= esc($enderecos->enderecos_numero ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="enderecos_complemento" class="form-label">Complemento</label>
            <input type="text" class="form-control" name="enderecos_complemento" id="enderecos_complemento"
                value="<?= esc($enderecos->enderecos_complemento ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="enderecos_cidade_nome" class="form-label">Cidade</label>
            <input type="text" class="form-control" name="enderecos_cidade_nome" id="enderecos_cidade_nome"
                value="<?= esc($enderecos->cidades_nome ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="enderecos_cidade_uf" class="form-label">Estado</label>
            <input type="text" class="form-control" name="enderecos_cidade_uf" id="enderecos_cidade_uf"
                value="<?= esc($enderecos->cidades_uf ?? '') ?>">
        </div>

        <input type="hidden" name="enderecos_usuario_id" value="<?= esc($enderecos->enderecos_usuario_id ?? '') ?>">
        <input type="hidden" name="enderecos_cidades_id" value="<?= esc($enderecos->enderecos_cidade_id ?? '') ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit"> <?= ucfirst($form) ?> <i class="bi bi-floppy"></i></button>
            <a class="btn btn-danger" href="<?= base_url('usuarios/perfil/' . $usuarios->usuarios_id) ?>">Cancelar</a>
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