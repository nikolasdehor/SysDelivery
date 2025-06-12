<?php
helper('functions');
session();

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    // Se for admin (2) ou funcionário (1), mostra o formulário de gerenciamento
    if ($login->usuarios_nivel == 2 || $login->usuarios_nivel == 1) {

        // Carrega o template correto para cada nível
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

    <form action="<?= base_url('pedidos/' . $op); ?>" method="post">

        <div class="mb-3">
            <label for="clientes_id" class="form-label">Cliente</label>
            <select class="form-select" name="clientes_id" id="clientes_id" required>
                <option value="">Selecione um cliente</option>
                <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente->clientes_id ?>"
                    <?= isset($pedidos->clientes_id) && $pedidos->clientes_id == $cliente->clientes_id ? 'selected' : '' ?>>
                    <?= esc($cliente->usuarios_nome . ' ' . $cliente->usuarios_sobrenome) ?> -
                    <?= esc($cliente->usuarios_cpf) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="data_pedido" class="form-label">Data do Pedido</label>
            <input type="datetime-local" class="form-control" name="data_pedido"
                value="<?= isset($pedidos->data_pedido) ? date('Y-m-d\TH:i', strtotime($pedidos->data_pedido . ' -3 hours')) : ''; ?>"
                id="data_pedido" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" name="status" id="status" required>
                <option value="">Selecione o status</option>
                <option value="aguardando"
                    <?= isset($pedidos->status) && $pedidos->status == 'aguardando' ? 'selected' : '' ?>>Aguardando
                </option>
                <option value="em andamento"
                    <?= isset($pedidos->status) && $pedidos->status == 'em andamento' ? 'selected' : '' ?>>Em andamento
                </option>
                <option value="em rota de entrega"
                    <?= isset($pedidos->status) && $pedidos->status == 'em rota de entrega' ? 'selected' : '' ?>>Em rota
                    de entrega
                </option>
                <option value="concluido"
                    <?= isset($pedidos->status) && $pedidos->status == 'concluido' ? 'selected' : '' ?>>Concluído
                </option>
                <option value="cancelado"
                    <?= isset($pedidos->status) && $pedidos->status == 'cancelado' ? 'selected' : '' ?>>Cancelado
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label for="total_pedido" class="form-label">Total do Pedido (R$)</label>
            <input type="number" step="0.01" class="form-control" name="total_pedido"
                value="<?= $pedidos->total_pedido ?? ''; ?>" id="total_pedido" required>
        </div>

        <div class="mb-3">
            <label for="observacoes" class="form-label">Observações</label>
            <textarea class="form-control" name="observacoes" id="observacoes"
                rows="4"><?= $pedidos->observacoes ?? ''; ?></textarea>
        </div>

        <input type="hidden" name="pedidos_id" value="<?= $pedidos->pedidos_id ?? ''; ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit">
                <?= ucfirst($form) ?> <i class="bi bi-floppy"></i>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
<?php
    } elseif ($login->usuarios_nivel == 0) { // Se for cliente (0), mostra o formulário de criação de pedido
        ?>

<?= $this->extend('Templates_user') ?>
<?= $this->section('content') ?>

<div class="container pt-4 pb-5 bg-light">
    <?php if(isset($msg)){echo $msg;} ?>
    <h2 class="border-bottom border-2 border-primary">
        <?= ucfirst($form) . ' ' . $title ?>
    </h2>

    <form action="<?= base_url('pedidos/' . $op); ?>" method="post">

        <input type="hidden" name="status" id="status" value="aguardando">
        <input type="hidden" name="pedidos_id" id="pedidos_id" value="<?= $pedidos->pedidos_id ?? '' ?>">

        <div class="mb-3">
            <label for="produtos" class="form-label">Produtos</label>
            <div id="produtos-container">

                <?php if (empty($itensPedido)): ?>
                <div class="row mb-2 produto-item">
                    <div class="col-md-4">
                        <select name="produtos[]" class="form-select produto-select" required>
                            <option value="">Selecione um produto</option>
                            <?php if(!empty($selectProduto)): ?>
                                <?php foreach ($produtos as $produto): ?>
                                    <?php foreach ($selectProduto as $select): ?>
                                        <?php if ($produto->produtos_id == $select->produtos_id): ?>
                                            <option value="<?= $produto->produtos_id ?>"
                                                    data-preco="<?= $produto->produtos_preco_venda ?>" selected>
                                                    <?= esc($produto->produtos_nome) ?> - R$
                                                    <?= number_format($produto->produtos_preco_venda, 2, ',', '.') ?>
                                                </option>
                                        <?php else: ?>
                                            <option value="<?= $produto->produtos_id ?>"
                                                data-preco="<?= $produto->produtos_preco_venda ?>">
                                                <?= esc($produto->produtos_nome) ?> - R$
                                                <?= number_format($produto->produtos_preco_venda, 2, ',', '.') ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php foreach ($produtos as $produto): ?>
                                    <option value="<?= $produto->produtos_id ?>"
                                        data-preco="<?= $produto->produtos_preco_venda ?>">
                                        <?= esc($produto->produtos_nome) ?> - R$
                                        <?= number_format($produto->produtos_preco_venda, 2, ',', '.') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary btn-minus">-</button>
                            <input type="number" name="quantidades[]" class="form-control text-center quantidade"
                                value="1" min="1" required>
                            <button type="button" class="btn btn-outline-secondary btn-plus">+</button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <input type="text" class="form-control preco-final" value="R$ 0,00" readonly>
                            <button type="button" class="btn btn-danger btn-remove">Remover</button>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <?php foreach ($itensPedido as $item): ?>
                <div class="row mb-2 produto-item">
                    <div class="col-md-4">
                        <select name="produtos[]" class="form-select produto-select" required>
                            <?php foreach ($produtos as $produto): ?>
                            <option value="<?= $produto->produtos_id ?>"
                                data-preco="<?= $produto->produtos_preco_venda ?>"
                                <?= $produto->produtos_id == $item->produtos_id ? 'selected' : '' ?>>
                                <?= esc($produto->produtos_nome) ?> - R$
                                <?= number_format($produto->produtos_preco_venda, 2, ',', '.') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary btn-minus">-</button>
                            <input type="number" name="quantidades[]" class="form-control text-center quantidade"
                                value="<?= $item->quantidade ?>" min="1" required>
                            <button type="button" class="btn btn-outline-secondary btn-plus">+</button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <input type="text" class="form-control preco-final"
                                value="R$ <?= number_format($item->produtos_preco_venda * $item->quantidade, 2, ',', '.') ?>"
                                readonly>
                            <button type="button" class="btn btn-danger btn-remove">Remover</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-secondary mt-2" id="add-produto">+ Produto</button>
        </div>

        <div class="mb-3">
            <label for="total_pedido" class="form-label">Total do Pedido (R$)</label>
            <input type="text" class="form-control" name="total_pedido" id="total_pedido" readonly required>
        </div>

        <div class="mb-3">
            <label for="observacoes" class="form-label">Observações (opcional)</label>
            <textarea class="form-control" name="observacoes" id="observacoes" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="enderecos_id" class="form-label">Endereço de Entrega</label>
            <select class="form-select" name="enderecos_id" id="enderecos_id" required>
                <option value="">Selecione um endereço</option>
                <?php foreach ($enderecos as $endereco): ?>
                <option value="<?= $endereco->enderecos_id ?>">
                    <?= esc($endereco->enderecos_rua . ', ' . $endereco->enderecos_status) ?> -
                    <?= esc($endereco->cidades_nome . ' - ' . $endereco->cidades_uf) ?> -
                    <?= esc($endereco->usuarios_nome . ' ' . $endereco->usuarios_sobrenome) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <button class="btn btn-success" type="submit">
                Finalizar Pedido <i class="bi bi-cart-check"></i>
            </button>
        </div>
    </form>

    <script>
    function atualizarPrecoFinal(item) {
        const produtoSelect = item.querySelector('.produto-select');
        const preco = parseFloat(produtoSelect.selectedOptions[0]?.getAttribute('data-preco') || 0);
        const quantidade = parseInt(item.querySelector('.quantidade').value) || 1;
        const precoFinal = preco * quantidade;
        item.querySelector('.preco-final').value = `R$ ${precoFinal.toFixed(2).replace('.', ',')}`;
        atualizarTotalPedido();
    }

    function atualizarTotalPedido() {
        let total = 0;
        document.querySelectorAll('.produto-item').forEach(item => {
            const produtoSelect = item.querySelector('.produto-select');
            const preco = parseFloat(produtoSelect.selectedOptions[0]?.getAttribute('data-preco') || 0);
            const quantidade = parseInt(item.querySelector('.quantidade').value) || 1;
            total += preco * quantidade;
        });
        document.getElementById('total_pedido').value = `R$ ${total.toFixed(2).replace('.', ',')}`;
    }

    document.getElementById('add-produto').addEventListener('click', () => {
        const container = document.getElementById('produtos-container');
        const baseItem = container.querySelector('.produto-item');
        const newItem = baseItem.cloneNode(true);

        newItem.querySelector('.produto-select').value = '';
        newItem.querySelector('.quantidade').value = '1';
        newItem.querySelector('.preco-final').value = 'R$ 0,00';

        container.appendChild(newItem);
    });

    document.addEventListener('click', e => {
        if (e.target.classList.contains('btn-remove')) {
            const items = document.querySelectorAll('.produto-item');
            if (items.length > 1) {
                e.target.closest('.produto-item').remove();
                atualizarTotalPedido();
            }
        }

        if (e.target.classList.contains('btn-plus') || e.target.classList.contains('btn-minus')) {
            const item = e.target.closest('.produto-item');
            const input = item.querySelector('.quantidade');
            let val = parseInt(input.value) || 1;

            if (e.target.classList.contains('btn-plus')) input.value = val + 1;
            else if (val > 1) input.value = val - 1;

            atualizarPrecoFinal(item);
        }
    });

    document.addEventListener('input', e => {
        if (e.target.classList.contains('quantidade')) {
            atualizarPrecoFinal(e.target.closest('.produto-item'));
        }
    });

    document.addEventListener('change', e => {
        if (e.target.classList.contains('produto-select')) {
            atualizarPrecoFinal(e.target.closest('.produto-item'));
        }
    });

    window.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.produto-item').forEach(atualizarPrecoFinal);
    });
    </script>

    <?= $this->endSection() ?>

    <?php
    } else {
        // Se não for nenhum dos níveis permitidos
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
    }
} else {
    // Se não estiver logado
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}
?>