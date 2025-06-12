<?php
helper('functions');
session();

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    if ($login->usuarios_nivel == 2) {
        echo $this->extend('Templates_admin');
    } elseif ($login->usuarios_nivel == 1) {
        echo $this->extend('Templates_funcionario');
    } else {
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
        return;
    }
    ?>

<?= $this->section('content') ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center border-bottom border-2 border-primary pb-2 mb-4">
        <h2 class="mb-0"><?= esc($title) ?></h2>
        <a class="btn btn-success" href="<?= base_url('itens_pedido/new'); ?>">
            <i class="bi bi-plus-circle"></i> Novo Item de Pedido
        </a>
    </div>

    <?php if (isset($msg)): ?>
    <?= $msg; ?>
    <?php endif; ?>

    <form action="<?= base_url('itens_pedido/search'); ?>" class="d-flex mb-4" role="search" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar em itens de pedidos..."
            aria-label="Search">
        <button class="btn btn-outline-primary" type="submit">
            <i class="bi bi-search"></i> Pesquisar
        </button>
    </form>

    <br>

    <a href="<?= base_url('relatorios/8') ?>" target="_blank" class="btn btn-primary mb-3">
        <i class="fas fa-file-pdf"></i> Relatório de Itens de Pedido
    </a>

    <?php
        $current_pedido_id = null;
        $total_geral_pedidos = 0;
        if (empty($itens_pedido)): ?>
    <div class="alert alert-info text-center" role="alert">
        Nenhum item de pedido encontrado.
    </div>
    <?php else:
            foreach ($itens_pedido as $index => $item):
                if ($item->pedidos_id !== $current_pedido_id):
                    if ($current_pedido_id !== null): ?>
    </tbody>
    </table>
</div>
<div class="card-footer bg-light d-flex justify-content-between align-items-center">
    <strong>Total do Pedido: R$ <?= number_format($total_do_pedido_atual, 2, ',', '.') ?></strong>
</div>
</div>
<?php endif;
                    $current_pedido_id = $item->pedidos_id;
                    $total_do_pedido_atual = 0;
                    ?>
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Pedido ID: <?= esc($item->pedidos_id) ?></h4>
        <a href="<?= base_url('itens_pedido/finalizar/' . $item->pedidos_id); ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-cart-check"></i> Fazer Pedido
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Produto</th>
                    <th class="text-center">Qtd.</th>
                    <th class="text-end">Preço Unit.</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php endif;

                $subtotal_item = $item->produtos_preco_venda * $item->quantidade;
                $total_do_pedido_atual += $subtotal_item;
                $total_geral_pedidos += $subtotal_item;
                ?>
                <tr>
                    <td>
                        <strong><?= esc($item->produto_nome ?? 'Produto não encontrado') ?></strong><br>
                        <small class="text-muted">ID Item: <?= esc($item->itens_pedido_id) ?></small>
                    </td>
                    <td class="text-center align-middle"><?= esc($item->quantidade) ?></td>
                    <td class="text-end align-middle">R$ <?= number_format($item->produtos_preco_venda, 2, ',', '.') ?>
                    </td>
                    <td class="text-end align-middle">R$ <?= number_format($subtotal_item, 2, ',', '.') ?></td>
                    <td class="text-center align-middle">
                        <a class="btn btn-sm btn-primary mb-1"
                            href="<?= base_url('itens_pedido/edit/' . $item->itens_pedido_id); ?>" title="Editar Item">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <a class="btn btn-sm btn-danger mb-1"
                            href="<?= base_url('itens_pedido/delete/' . $item->itens_pedido_id); ?>">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </td>
                </tr>
                <?php
                            if ($index === array_key_last($itens_pedido)): ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-light d-flex justify-content-between align-items-center">
        <strong>Total do Pedido: R$ <?= number_format($total_do_pedido_atual, 2, ',', '.') ?></strong>
    </div>
</div>
<?php endif;
            endforeach;
        endif;
        ?>
</div>

<?= $this->endSection() ?>

<?php
} else {
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}

?>