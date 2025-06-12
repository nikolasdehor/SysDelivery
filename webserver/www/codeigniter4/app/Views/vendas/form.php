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

    <form action="<?= base_url('vendas/' . $op); ?>" method="post">

        <div class="mb-3">
            <label for="pedidos_id" class="form-label">Pedido</label>
            <select class="form-select" name="pedidos_id" id="pedidos_id" required
                onchange="atualizarValorTotal(this.value)">
                <option value="">Selecione um pedido</option>
                <?php foreach ($pedidos as $pedido): ?>
                <option value="<?= $pedido->pedidos_id ?>"
                    <?= isset($venda->pedidos_id) && $venda->pedidos_id == $pedido->pedidos_id ? 'selected' : '' ?>>
                    <?= esc('Pedido #' . $pedido->pedidos_id . ' - ' . $pedido->usuarios_nome . ' ' . $pedido->usuarios_sobrenome) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="data_venda" class="form-label">Data da Venda</label>
            <input type="datetime-local" class="form-control" name="data_venda"
                value="<?= isset($venda->data_venda) ? date('Y-m-d\TH:i', strtotime($venda->data_venda . ' -3 hours')) : ''; ?>"
                id="data_venda" required>
        </div>

        <div class="mb-3">
            <label for="forma_pagamento" class="form-label">Forma de Pagamento</label>
            <select class="form-select" name="forma_pagamento" id="forma_pagamento" required>
                <option value="">Selecione</option>
                <option value="dinheiro"
                    <?= isset($venda->forma_pagamento) && $venda->forma_pagamento == 'dinheiro' ? 'selected' : '' ?>>
                    Dinheiro</option>
                <option value="cartao_credito"
                    <?= isset($venda->forma_pagamento) && $venda->forma_pagamento == 'cartao_credito' ? 'selected' : '' ?>>
                    Cartão de Crédito</option>
                <option value="cartao_debito"
                    <?= isset($venda->forma_pagamento) && $venda->forma_pagamento == 'cartao_debito' ? 'selected' : '' ?>>
                    Cartão de Débito</option>
                <option value="pix"
                    <?= isset($venda->forma_pagamento) && $venda->forma_pagamento == 'pix' ? 'selected' : '' ?>>Pix
                </option>
                <option value="boleto"
                    <?= isset($venda->forma_pagamento) && $venda->forma_pagamento == 'boleto' ? 'selected' : '' ?>>
                    Boleto</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="valor_total" class="form-label">Valor Total (R$)</label>
            <input type="text" class="form-control" name="valor_total" id="valor_total"
                value="<?= isset($venda->valor_total) ? number_format($venda->valor_total, 2, ',', '') : ''; ?>"
                readonly required>
        </div>

        <div class="mb-3">
            <label for="observacoes" class="form-label">Observações</label>
            <textarea class="form-control" name="observacoes" id="observacoes"
                rows="4"><?= $venda->observacoes ?? ''; ?></textarea>
        </div>

        <input type="hidden" name="vendas_id" value="<?= $venda->vendas_id ?? ''; ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit">
                <?= ucfirst($form) ?> <i class="bi bi-floppy"></i>
            </button>
        </div>
    </form>
</div>

<script>
function atualizarValorTotal(pedidos_id) {
    if (!pedidos_id) {
        document.getElementById('valor_total').value = '';
        return;
    }

    fetch(`<?= base_url('vendas/getTotalPedido/') ?>${pedidos_id}`)
        .then(response => response.json())
        .then(data => {
            if (data.total_pedido !== undefined) {
                const valorFormatado = parseFloat(data.total_pedido).toFixed(2).replace('.', ',');
                document.getElementById('valor_total').value = valorFormatado;
            }
        })
        .catch(error => {
            console.error('Erro ao buscar o total do pedido:', error);
        });
}
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