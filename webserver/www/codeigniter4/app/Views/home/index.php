<?php
helper('functions');
session();
$template = 'Templates';

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    if ($login->usuarios_nivel == 0) {
        $template = 'Templates_user';
    } elseif ($login->usuarios_nivel == 1) {
        $template = 'Templates_funcionario';
    } elseif ($login->usuarios_nivel == 2) {
        $template = 'Templates_admin';
    } else {
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
        return;
    }
}
?>

<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<!--Abre Produtos-->
<div id="produtos" class="container">
    <?php if (session()->getFlashdata('msg')): ?>
        <?= session()->getFlashdata('msg') ?>
    <?php endif; ?>

    <h2 class="border-bottom mt-3 border-2 border-primary">Produtos</h2>

    <div class="col mt-3 mb-3">
        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Pesquisar" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="row">
        <?php
        helper('image'); // Carrega o helper de imagens
        if (isset($imgprodutos) && count($imgprodutos) > 0): ?>
        <?php foreach ($imgprodutos as $produto): ?>
        <div class="col-sm-3 mb-3 pb-4 mb-sm-0">
            <div class="card">
                <?= getImageTag(
                    $produto->imgprodutos_link,
                    esc($produto->produtos_nome),
                    'card-img-top',
                    'height: 200px; object-fit: cover;'
                ) ?>
                <div class="card-body">
                    <h5 class="card-title"><?= esc($produto->produtos_nome) ?></h5>
                    <h5 class="card-title">
                        <b class="text-danger"> R$ <?= number_format($produto->produtos_preco_venda, 2, ',', '.') ?>
                        </b>
                    </h5>
                    <p class="card-text"><?= esc($produto->produtos_descricao) ?></p>
                    <div class="text-center">
                        <?php if (isset($_SESSION['login'])): ?>
                            <button class="btn btn-primary me-2" onclick="adicionarAoCarrinho(<?= $produto->produtos_id ?>)">
                                <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                            </button>
                            <a href="<?= base_url('/pedidos/produto/' . $produto->produtos_id) ?>" class="btn btn-outline-primary">
                                <i class="bi bi-basket2-fill"></i> Comprar Agora
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('/login') ?>" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Faça Login para Comprar
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info" role="alert">
                Nenhum produto encontrado.
            </div>
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
function adicionarAoCarrinho(produtoId) {
    fetch('<?= base_url('carrinho/adicionar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            produto_id: produtoId,
            quantidade: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar mensagem de sucesso
            showToast('Produto adicionado ao carrinho!', 'success');
            // Atualizar badge do carrinho se existir
            if (typeof atualizarBadgeCarrinho === 'function') {
                atualizarBadgeCarrinho();
            }
        } else {
            showToast(data.message || 'Erro ao adicionar produto', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('Erro ao adicionar produto ao carrinho', 'error');
    });
}

function showToast(message, type = 'info') {
    // Criar toast simples
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

    document.body.appendChild(toast);

    // Remover automaticamente após 3 segundos
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 3000);
}
</script>

<?= $this->endSection() ?>