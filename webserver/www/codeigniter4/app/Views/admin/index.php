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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Admin</a></li>
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <br>
        </ol>
        <span class="breadcrumb-text"> Seja bem vindo <?= $login->usuarios_nome ?></span>
    </nav>
    <h2 class="border-bottom border-2 border-primary">
        Administrador
    </h2>
    <p></p>

    <!-- Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Usuários</h5>
                            <h3 class="mb-0">
                                <?php
                                try {
                                    $db = \Config\Database::connect();
                                    $query = $db->query("SELECT COUNT(*) as total FROM usuarios");
                                    $result = $query->getRow();
                                    echo $result->total;
                                } catch (Exception $e) {
                                    echo "0";
                                }
                                ?>
                            </h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Produtos</h5>
                            <h3 class="mb-0">
                                <?php
                                try {
                                    $query = $db->query("SELECT COUNT(*) as total FROM produtos");
                                    $result = $query->getRow();
                                    echo $result->total;
                                } catch (Exception $e) {
                                    echo "0";
                                }
                                ?>
                            </h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-box fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Pedidos</h5>
                            <h3 class="mb-0">
                                <?php
                                try {
                                    $query = $db->query("SELECT COUNT(*) as total FROM pedidos");
                                    $result = $query->getRow();
                                    echo $result->total;
                                } catch (Exception $e) {
                                    echo "0";
                                }
                                ?>
                            </h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-cart fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Categorias</h5>
                            <h3 class="mb-0">
                                <?php
                                try {
                                    $query = $db->query("SELECT COUNT(*) as total FROM categorias");
                                    $result = $query->getRow();
                                    echo $result->total;
                                } catch (Exception $e) {
                                    echo "0";
                                }
                                ?>
                            </h3>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-tags fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ações Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people"></i><br>
                                Gerenciar Usuários
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?= base_url('produtos') ?>" class="btn btn-outline-success w-100">
                                <i class="bi bi-box"></i><br>
                                Gerenciar Produtos
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?= base_url('categorias') ?>" class="btn btn-outline-info w-100">
                                <i class="bi bi-tags"></i><br>
                                Gerenciar Categorias
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?= base_url('pedidos') ?>" class="btn btn-outline-warning w-100">
                                <i class="bi bi-cart"></i><br>
                                Gerenciar Pedidos
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?= base_url('cupons') ?>" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-percent"></i><br>
                                Gerenciar Cupons
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?= base_url('relatorios') ?>" class="btn btn-outline-dark w-100">
                                <i class="bi bi-graph-up"></i><br>
                                Relatórios
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Sistema -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Status do Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Banco de Dados</h6>
                            <p class="text-success">
                                <i class="bi bi-check-circle"></i> Conectado
                            </p>
                        </div>
                        <div class="col-md-4">
                            <h6>Versão do Sistema</h6>
                            <p>SysDelivery v1.0</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Último Login</h6>
                            <p><?= date('d/m/Y H:i') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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