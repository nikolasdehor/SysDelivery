<?php
helper('functions');
session();
if(isset($_SESSION['login'])){
    $login = $_SESSION['login'];
    if($login->usuarios_nivel == 2){

?>
<?= $this->extend('Templates_admin') ?>
<?= $this->section('content') ?>

<div class="container mt-5 pt-4 pb-5 bg-light">

    <?php if(isset($msg)){echo $msg;} ?>

    <h2 class="border-bottom border-2 border-primary">
        Limpar Rate Limiting
    </h2>

    <div class="alert alert-info">
        <strong>Atenção:</strong> Esta funcionalidade remove bloqueios de tentativas de login.
        Use apenas quando usuários estiverem bloqueados indevidamente.
    </div>

    <!-- Limpar por IP específico -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Limpar por IP Específico</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('usuarios/limpar_rate_limiting'); ?>" method="post">
                <div class="mb-3">
                    <label for="ip" class="form-label">Endereço IP</label>
                    <input type="text" class="form-control" name="ip" id="ip" 
                           placeholder="Ex: 192.168.1.100" pattern="^(?:[0-9]{1,3}\.){3}[0-9]{1,3}$">
                    <div class="form-text">Digite o IP do usuário que está bloqueado</div>
                </div>
                <button class="btn btn-warning" type="submit">
                    <i class="bi bi-unlock"></i> Limpar Rate Limiting para este IP
                </button>
            </form>
        </div>
    </div>

    <!-- Limpar tudo -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Limpar Todos os Rate Limits</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <strong>Cuidado:</strong> Esta operação remove TODOS os bloqueios de rate limiting.
            </div>
            <form action="<?= base_url('usuarios/limpar_rate_limiting'); ?>" method="post">
                <button class="btn btn-danger" type="submit" 
                        onclick="return confirm('Tem certeza que deseja limpar TODOS os rate limits?')">
                    <i class="bi bi-trash"></i> Limpar Todos os Rate Limits
                </button>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <a class="btn btn-secondary" href="<?= base_url('usuarios') ?>">
            <i class="bi bi-arrow-left"></i> Voltar para Usuários
        </a>
    </div>

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
