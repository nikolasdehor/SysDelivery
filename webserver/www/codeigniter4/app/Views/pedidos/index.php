<?php
helper('functions');
session();

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    // Se for admin (2) ou funcionário (1), mostra a tela de gerenciamento
    if ($login->usuarios_nivel == 2 || $login->usuarios_nivel == 1) {

        // Carrega o template correto para cada nível
        if ($login->usuarios_nivel == 2) {
            echo $this->extend('Templates_admin');
        } else {
            echo $this->extend('Templates_funcionario');
        }
        ?>

<?= $this->section('content') ?>

<div class="container">
    <h2 class="border-bottom border-2 border-primary mt-5 pt-3 mb-4"> <?= $title ?> </h2>

    <?php if (isset($msg))
                echo $msg; ?>

    <form action="<?= base_url('pedidos/search'); ?>" class="d-flex mb-3" role="search" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <br>

    <a href="<?= base_url('relatorios/6') ?>" target="_blank" class="btn btn-primary mb-3">
        <i class="fas fa-file-pdf"></i> Relatório de Pedidos
    </a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Data do Pedido</th>
                <th>Status</th>
                <th>Total</th>
                <th>
                    <a class="btn btn-success" href="<?= base_url('pedidos/new'); ?>">
                        Novo <i class="bi bi-plus-circle"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody class="table-group-divider">
            <?php foreach ($pedidos as $pedido): ?>
            <tr>
                <td><strong><?= $pedido->pedidos_id ?></strong></td>
                <td><?= $pedido->usuarios_nome . ' ' . $pedido->usuarios_sobrenome ?></td>
                <td><?= date('d/m/Y H:i', strtotime($pedido->data_pedido)) ?></td>
                <td><?= ucfirst($pedido->status) ?></td>
                <td>R$ <?= number_format($pedido->total_pedido, 2, ',', '.') ?></td>
                <td>
                    <a class="btn btn-primary" href="<?= base_url('pedidos/edit/' . $pedido->pedidos_id); ?>">
                        Editar <i class="bi bi-pencil-square"></i>
                    </a>
                    <a class="btn btn-danger" href="<?= base_url('pedidos/delete/' . $pedido->pedidos_id); ?>">
                        Excluir <i class="bi bi-x-circle"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

<?php
        // Se for cliente (0), mostra a visão do cliente
    } elseif ($login->usuarios_nivel == 0) {
        ?>

<?= $this->extend('Templates_user') ?>
<?= $this->section('content') ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center border-bottom border-2 border-primary pb-2 mb-4">
        <h2 class="mb-0"><?= esc($title) ?></h2>
        <a class="btn btn-success" href="<?= base_url('pedidos/new'); ?>">
            <i class="bi bi-plus-circle"></i> Novo Pedido
        </a>
    </div>

    <?php if (isset($msg)): ?>
    <?= $msg; ?>
    <?php endif; ?>

    <form action="<?= base_url('pedidos/search'); ?>" class="d-flex mb-4" role="search" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar em itens de pedidos..."
            aria-label="Search">
        <button class="btn btn-outline-primary" type="submit">
            <i class="bi bi-search"></i> Pesquisar
        </button>
    </form>

    <?php
            $current_pedido_id = null;
            $total_geral_pedidos = 0;
            $itensNaoConcluidos = array_filter($itensPedido, function ($item) {
                return strtolower($item->status) !== 'concluido';
            });

            if (empty($itensNaoConcluidos)): ?>
    <div class="alert alert-info text-center" role="alert">
        Nenhum pedido encontrado.
    </div>
    <?php else:
                $current_pedido_id = null;
                $total_geral_pedidos = 0;
                foreach ($itensPedido as $index => $item):

                    if (strtolower($item->status) === 'concluido') {
                        if ($item->pedidos_id !== $current_pedido_id && $current_pedido_id !== null): ?>
    </tbody>
    </table>
</div>
<div class="card-footer bg-light d-flex justify-content-between align-items-center">
    <strong>Total do Pedido: R$ <?= number_format($total_do_pedido_atual, 2, ',', '.') ?></strong>
</div>
</div>
<?php
                        endif;
                        continue;
                    }

                    if ($item->pedidos_id !== $current_pedido_id):
                        if ($current_pedido_id !== null): ?>

</tbody>
</table>
</div>
<div class="card-footer bg-light d-flex justify-content-between align-items-center">
    <strong>Total do Pedido: R$ <?= number_format($total_do_pedido_atual, 2, ',', '.') ?></strong>
</div>
</div>
<?php
                        endif;

                        $current_pedido_id = $item->pedidos_id;
                        $total_do_pedido_atual = 0;
                        ?>
<div class="card mb-4 shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Pedido <?= esc($item->pedidos_id) ?></h4>
        <h4 class="mb-0">Status: <?= ucfirst($item->status) ?></h4>
        <h4 class="mb-0">Data: <?= date('d/m/Y H:i', strtotime($item->data_pedido)) ?></h4>
        <div>
            <a href="<?= base_url('pedidos/edit/' . $item->pedidos_id); ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-pencil-square"></i>
            </a>
            <a href="<?= base_url('pedidos/delete/' . $item->pedidos_id); ?>" class="btn btn-danger btn-sm">
                <i class="bi bi-x-circle"></i>
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Produto</th>
                    <th class="text-center">Qtd.</th>
                    <th class="text-end">Preço Unit.</th>
                    <th class="text-end">Subtotal</th>
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
                        <?= esc($item->produtos_nome ?? 'Produto não encontrado') ?>
                    </td>
                    <td class="text-center align-middle"><?= esc($item->quantidade) ?></td>
                    <td class="text-end align-middle">R$ <?= number_format($item->produtos_preco_venda, 2, ',', '.') ?>
                    </td>
                    <td class="text-end align-middle">R$ <?= number_format($subtotal_item, 2, ',', '.') ?></td>
                </tr>
                <?php
                                if ($index === array_key_last($itensPedido)): ?>
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
        // Se não for nenhum dos níveis permitidos
    } else {
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
    }
} else {
    // Se não estiver logado
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}
?>