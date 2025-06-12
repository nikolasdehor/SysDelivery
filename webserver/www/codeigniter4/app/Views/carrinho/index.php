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
    }
}
?>

<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h2 class="border-bottom border-2 border-primary mb-4">
        <i class="bi bi-cart3"></i> <?= $title ?>
    </h2>

    <?php if (session()->getFlashdata('msg')): ?>
        <?= session()->getFlashdata('msg') ?>
    <?php endif; ?>

    <?php if (isset($itens) && count($itens) > 0): ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-basket"></i> Itens do Carrinho</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($itens as $item): ?>
                            <div class="row align-items-center mb-3 pb-3 border-bottom" id="item-<?= $item->carrinho_id ?>">
                                <div class="col-md-2">
                                    <?php if (!empty($item->imgprodutos_link)): ?>
                                        <img src="<?= base_url('assets/' . $item->imgprodutos_link) ?>" 
                                             class="img-fluid rounded" alt="<?= esc($item->produtos_nome) ?>">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <h6><?= esc($item->produtos_nome) ?></h6>
                                    <small class="text-muted"><?= esc($item->produtos_descricao) ?></small>
                                </div>
                                <div class="col-md-2">
                                    <strong class="text-success">
                                        R$ <?= number_format($item->carrinho_preco_unitario, 2, ',', '.') ?>
                                    </strong>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-sm">
                                        <button class="btn btn-outline-secondary btn-sm" type="button" 
                                                onclick="alterarQuantidade(<?= $item->carrinho_id ?>, -1)">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number" class="form-control text-center" 
                                               id="qty-<?= $item->carrinho_id ?>" 
                                               value="<?= $item->carrinho_quantidade ?>" 
                                               min="1" readonly>
                                        <button class="btn btn-outline-secondary btn-sm" type="button" 
                                                onclick="alterarQuantidade(<?= $item->carrinho_id ?>, 1)">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button class="btn btn-outline-danger btn-sm" 
                                            onclick="removerItem(<?= $item->carrinho_id ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="<?= base_url('/') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Continuar Comprando
                    </a>
                    <a href="<?= base_url('/carrinho/limpar') ?>" class="btn btn-outline-danger" 
                       onclick="return confirm('Tem certeza que deseja limpar o carrinho?')">
                        <i class="bi bi-trash"></i> Limpar Carrinho
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-calculator"></i> Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">R$ <?= number_format($total, 2, ',', '.') ?></span>
                        </div>
                        
                        <!-- Cupom de Desconto -->
                        <div class="mb-3">
                            <label class="form-label">Cupom de Desconto:</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="codigo-cupom" 
                                       placeholder="Digite o código">
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="aplicarCupom()">
                                    Aplicar
                                </button>
                            </div>
                            <div id="cupom-resultado" class="mt-2"></div>
                        </div>

                        <div id="desconto-section" class="d-none">
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Desconto:</span>
                                <span id="valor-desconto">R$ 0,00</span>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="total-final" class="text-success">
                                R$ <?= number_format($total, 2, ',', '.') ?>
                            </strong>
                        </div>

                        <button class="btn btn-success w-100 mb-2">
                            <i class="bi bi-credit-card"></i> Finalizar Pedido
                        </button>

                        <?php if (isset($cupons_disponiveis) && count($cupons_disponiveis) > 0): ?>
                            <div class="mt-3">
                                <small class="text-muted">Cupons disponíveis:</small>
                                <?php foreach ($cupons_disponiveis as $cupom): ?>
                                    <div class="badge bg-light text-dark me-1 mb-1" 
                                         style="cursor: pointer;" 
                                         onclick="document.getElementById('codigo-cupom').value = '<?= $cupom->cupons_codigo ?>'">
                                        <?= $cupom->cupons_codigo ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-3">Seu carrinho está vazio</h4>
            <p class="text-muted">Adicione alguns produtos para continuar</p>
            <a href="<?= base_url('/') ?>" class="btn btn-primary">
                <i class="bi bi-shop"></i> Ver Produtos
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function alterarQuantidade(carrinhoId, delta) {
    const qtyInput = document.getElementById(`qty-${carrinhoId}`);
    const novaQuantidade = parseInt(qtyInput.value) + delta;
    
    if (novaQuantidade < 1) return;
    
    fetch('<?= base_url('/carrinho/atualizar') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `carrinho_id=${carrinhoId}&quantidade=${novaQuantidade}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            qtyInput.value = novaQuantidade;
            document.getElementById('subtotal').textContent = `R$ ${data.total}`;
            document.getElementById('total-final').textContent = `R$ ${data.total}`;
            atualizarBadgeCarrinho(data.total_itens);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar quantidade');
    });
}

function removerItem(carrinhoId) {
    if (!confirm('Tem certeza que deseja remover este item?')) return;
    
    fetch('<?= base_url('/carrinho/remover') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `carrinho_id=${carrinhoId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`item-${carrinhoId}`).remove();
            document.getElementById('subtotal').textContent = `R$ ${data.total}`;
            document.getElementById('total-final').textContent = `R$ ${data.total}`;
            atualizarBadgeCarrinho(data.total_itens);
            
            // Se não há mais itens, recarrega a página
            if (data.total_itens === 0) {
                location.reload();
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao remover item');
    });
}

function aplicarCupom() {
    const codigo = document.getElementById('codigo-cupom').value.trim();
    if (!codigo) {
        alert('Digite um código de cupom');
        return;
    }
    
    fetch('<?= base_url('/carrinho/aplicar-cupom') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `codigo_cupom=${codigo}`
    })
    .then(response => response.json())
    .then(data => {
        const resultado = document.getElementById('cupom-resultado');
        if (data.success) {
            resultado.innerHTML = `<small class="text-success">${data.message}</small>`;
            document.getElementById('desconto-section').classList.remove('d-none');
            document.getElementById('valor-desconto').textContent = `R$ ${data.desconto}`;
            document.getElementById('total-final').textContent = `R$ ${data.total_com_desconto}`;
        } else {
            resultado.innerHTML = `<small class="text-danger">${data.message}</small>`;
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao aplicar cupom');
    });
}

function atualizarBadgeCarrinho(totalItens) {
    const badge = document.querySelector('.badge-carrinho');
    if (badge) {
        badge.textContent = totalItens;
        if (totalItens === 0) {
            badge.style.display = 'none';
        }
    }
}
</script>

<?= $this->endSection() ?>
