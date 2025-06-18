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
                    <a class="nav-link" href="<?php echo base_url('carrinho') ?>">
                        <i class="bi bi-cart3"></i>
                        Carrinho
                        <span id="carrinho-badge" class="badge bg-danger ms-1">0</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('pedidos') ?>">
                        <i class="bi bi-table"></i>
                        Meus Pedidos
                    </a>
                </li>

            </ul>


            <div class="d-flex align-items-center">
                <!-- Dropdown do usuário -->
                <div class="dropdown me-3">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                        <?php echo session()->get('login')->usuarios_nome ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li>
                            <a class="dropdown-item" href="<?php echo base_url('usuarios/perfil/' . session()->get('login')->usuarios_id) ?>">
                                <i class="bi bi-person"></i> Meu Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo base_url('enderecos/new') ?>">
                                <i class="bi bi-geo-alt"></i> Adicionar Endereço
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?php echo base_url('login/logout') ?>">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<!-- Fecha o menu de navegação -->

<script>
// Atualizar badge do carrinho
function atualizarBadgeCarrinho() {
    fetch('<?= base_url('carrinho/contar-itens') ?>')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('carrinho-badge');
            if (badge) {
                badge.textContent = data.total_itens || 0;
                badge.style.display = data.total_itens > 0 ? 'inline' : 'none';
            }
        })
        .catch(error => console.log('Erro ao atualizar carrinho:', error));
}

// Atualizar ao carregar a página
document.addEventListener('DOMContentLoaded', atualizarBadgeCarrinho);

// Atualizar a cada 30 segundos
setInterval(atualizarBadgeCarrinho, 30000);
</script>