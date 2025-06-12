<!-- Abre o menu de navegação -->
<nav class="navbar bg-dark navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo base_url('/') ?>">
            <!--Logo do Projeto-->
            <img src="<?php echo base_url('assets/images/sd_logo.png') ?>" alt="SysDelivery" width="180">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <!-- Link Home-->
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="<?php echo base_url('/') ?>">
                        <i class="bi bi-house-fill"></i>
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('pedidos') ?>">
                        <i class="bi bi-table"></i>
                        Pedidos
                    </a>
                </li>

            </ul>


            <div class="d-flex">
                <!-- Usuario Logado -->
                <span class="navbar-text text-white me-3">
                    <a class="nav-link text-white" href="<?php echo base_url('usuarios/perfil/' . session()->get('login')->usuarios_id) ?>">
                        <i class="bi bi-person-circle"></i>
                        <?php echo session()->get('login')->usuarios_nome ?>
                    </a>
                </span>
                <!-- Botão Sair -->
                <a class="btn btn-outline-primary me-2" href="<?php echo base_url('login/logout') ?>">
                    <i class="bi bi-unlock"></i>
                    sair
                </a>
            </div>
        </div>
    </div>
</nav>
<!-- Fecha o menu de navegação -->