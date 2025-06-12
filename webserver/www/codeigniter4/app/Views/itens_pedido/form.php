<?php
helper('functions');
session();

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    // CORREÇÃO: Permite o acesso para nível 2 (admin) e 1 (funcionário)
    if ($login->usuarios_nivel == 2 || $login->usuarios_nivel == 1) {

        // Carrega o template correto de acordo com o nível
        if ($login->usuarios_nivel == 2) {
            echo $this->extend('Templates_admin');
        } else {
            echo $this->extend('Templates_funcionario');
        }
        ?>

<?= $this->section('content') ?>

<div class="container pt-4 pb-5 bg-light">
    <h2 class="border-bottom border-2 border-primary">
        <?= ucfirst($form) . ' ' . $title ?>
    </h2>

    <form action="<?= base_url('itens_pedido/' . $op); ?>" method="post">

        <div class="mb-3">
            <label for="pedidos_id" class="form-label">Pedido</label>
            <select class="form-select" name="pedidos_id" id="pedidos_id" required>
                <option value="">Selecione um pedido</option>
                <?php foreach ($pedidos as $pedido): ?>
                <option value="<?= $pedido->pedidos_id ?>"
                    <?= isset($itens_pedido->pedidos_id) && $itens_pedido->pedidos_id == $pedido->pedidos_id ? 'selected' : '' ?>>
                    Pedido #<?= $pedido->pedidos_id ?> - <?= date('d/m/Y H:i', strtotime($pedido->data_pedido)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="produtos_id" class="form-label">Produto</label>
            <select class="form-select" name="produtos_id" id="produtos_id" required>
                <option value="">Selecione um produto</option>
                <?php foreach ($produtos as $produto): ?>
                <option value="<?= $produto->produtos_id ?>" data-preco="<?= $produto->produtos_preco_venda ?>"
                    <?= isset($itens_pedido->produtos_id) && $itens_pedido->produtos_id == $produto->produtos_id ? 'selected' : '' ?>>
                    <?= esc($produto->produtos_nome) ?> - R$
                    <?= number_format($produto->produtos_preco_venda, 2, ',', '.') ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <div class="input-group" style="max-width: 200px;">
                <button type="button" class="btn btn-outline-secondary" onclick="alterarQuantidade(-1)">-</button>
                <input type="number" class="form-control text-center" name="quantidade" id="quantidade" min="1"
                    value="<?= $itens_pedido->quantidade ?? 1; ?>" required>
                <button type="button" class="btn btn-outline-secondary" onclick="alterarQuantidade(1)">+</button>
            </div>
        </div>

        <div class="mb-3">
            <label for="preco_unitario" class="form-label">Preço Final (R$)</label>
            <input type="number" step="0.01" class="form-control" name="preco_unitario" id="preco_unitario"
                value="<?= $itens_pedido->preco_unitario ?? '0.00'; ?>" readonly required>
        </div>

        <input type="hidden" name="itens_pedido_id" value="<?= $itens_pedido->itens_pedido_id ?? ''; ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit">
                <?= ucfirst($form) ?> <i class="bi bi-floppy"></i>
            </button>
        </div>
    </form>
</div>

<script>
function alterarQuantidade(delta) {
    const quantidadeInput = document.getElementById('quantidade');
    let quantidade = parseInt(quantidadeInput.value) || 1;
    quantidade = Math.max(1, quantidade + delta);
    quantidadeInput.value = quantidade;
    atualizarPreco();
}

function atualizarPreco() {
    const selectProduto = document.getElementById('produtos_id');
    const preco = parseFloat(selectProduto.options[selectProduto.selectedIndex]?.getAttribute('data-preco') || 0);
    const quantidade = parseInt(document.getElementById('quantidade').value) || 1;
    const precoFinal = (preco * quantidade).toFixed(2);
    document.getElementById('preco_unitario').value = precoFinal;
}

document.getElementById('produtos_id').addEventListener('change', atualizarPreco);
document.getElementById('quantidade').addEventListener('input', atualizarPreco);

// Inicializa o preço ao carregar
window.addEventListener('DOMContentLoaded', atualizarPreco);
</script>

<?= $this->endSection() ?>

<?php
    } else {
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
    }
} else {
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}
?>