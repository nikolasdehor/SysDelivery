<?php
helper('functions');
session();

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    // CORREÇÃO: Permite o acesso para nível 0 (comum) e 1 (funcionário)
    if ($login->usuarios_nivel == 0 || $login->usuarios_nivel == 1) {

        // Carrega o template correto de acordo com o nível
        if ($login->usuarios_nivel == 0) {
            echo $this->extend('Templates_user');
        } else {
            echo $this->extend('Templates_funcionario');
        }
        ?>
<?= $this->section('content') ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center border-bottom border-2 border-primary pb-2 mb-4">
        <h2 class="mb-0"><?= esc($title) ?></h2>
        <div>
            <a class="btn btn-success" href="<?= base_url('usuarios/edit/' . $usuario->usuarios_id); ?>">
                <i class="bi bi-pencil-square"></i> Editar Perfil
            </a>
            <a class="btn btn-danger" href="<?= base_url('usuarios/delete/' . $usuario->usuarios_id); ?>">
                <i class="bi bi-trash"></i> Excluir Conta
            </a>
        </div>
    </div>

    <?php if (isset($msg)): ?>
    <?= $msg; ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Informações de Cadastro</h5>
            <div class="card-body">
                <p><strong>Nome:</strong> <?= $usuario->usuarios_nome ?> - <?= $usuario->usuarios_sobrenome ?></p>
                <p><strong>CPF:</strong> <?= $usuario->usuarios_cpf ?></p>
                <p><strong>Data de Nascimento:</strong> <?= date('d/m/Y', strtotime($usuario->usuarios_data_nasc)) ?>
                </p>
                <p><strong>Email:</strong> <?= $usuario->usuarios_email ?></p>
                <p><strong>Telefone:</strong> <?= $usuario->usuarios_fone ?></p>
                <p><strong>Data de Cadastro:</strong>
                    <?= date('d/m/Y H:i', strtotime($usuario->usuarios_data_cadastro)) ?></p>
            </div>
        </div>
        <div class="card-footer">
            <a class="btn btn-primary" href="<?= base_url('usuarios/edit_senha/' . $usuario->usuarios_id); ?>">
                <i class="bi bi-key"></i> Alterar Senha
            </a>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Meus Endereços</h5>
                <a class="btn btn-success" href="<?= base_url('enderecos/new/') ?>">
                    <i class="bi bi-plus-circle"></i> Adicionar Novo Endereço
                </a>
            </div>
            <?php if (!empty($enderecos)): ?>
            <ul class="list-group">
                <?php foreach ($enderecos as $endereco): ?>
                <li class="list-group-item">
                    <?= $endereco->cidades_nome ?> - <?= $endereco->cidades_uf ?><br>
                    <?= $endereco->enderecos_rua ?>, <?= $endereco->enderecos_numero ?>,
                    <?= $endereco->enderecos_complemento ?><br>
                    <a class="btn btn-info btn-sm mt-2"
                        href="<?= base_url('enderecos/edit/' . $endereco->enderecos_id); ?>">
                        <i class="bi bi-pencil-square"></i> Editar
                    </a>
                    <a class="btn btn-danger btn-sm mt-2"
                        href="<?= base_url('enderecos/delete/' . $endereco->enderecos_id); ?>">
                        <i class="bi bi-trash"></i> Excluir
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
            <p class="text-muted">Nenhum endereço adicionado.</p>
            <?php endif; ?>
        </div>
    </div>
    <?= $this->endSection() ?>

    <?php
    } else {
        // Se não for nível 0 ou 1, o acesso é negado
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
    }
} else {
    // Se não estiver logado
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}
?>