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
        <?= ucfirst($form) . ' Entrega' ?>
    </h2>

    <form action="<?= base_url('entregas/' . $op); ?>" method="post">

        <div class="mb-3">
            <label for="pedido_id" class="form-label">Pedido</label>
            <select class="form-select" name="pedido_id" id="pedido_id" required>
                <option value="">Selecione um pedido</option>
                <?php foreach ($pedidos as $pedido): ?>
                <option value="<?= $pedido->pedidos_id ?>"
                    <?= isset($entrega->pedido_id) && $entrega->pedido_id == $pedido->pedidos_id ? 'selected' : '' ?>>
                    Pedido #<?= esc($pedido->pedidos_id) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="funcionario_id" class="form-label">Funcionário</label>
            <select class="form-select" name="funcionario_id" id="funcionario_id" required>
                <option value="">Selecione um funcionário</option>
                <?php foreach ($funcionarios as $func): ?>
                <option value="<?= $func->funcionarios_id ?>"
                    <?= isset($entrega->funcionario_id) && $entrega->funcionario_id == $func->funcionarios_id ? 'selected' : '' ?>>
                    <?= esc($func->usuarios_nome ?? 'Sem nome') ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="endereco_texto" class="form-label">Endereço</label>
            <input type="text" class="form-control" id="endereco_texto" value="-- Selecione um pedido --"
                value="<?= isset($entrega->enderecos_rua) ? esc($entrega->enderecos_rua) : '' ?>" readonly>

            <input type="hidden" name="endereco_id" id="endereco_id"
                value="<?= isset($entrega->endereco_id) ? esc($entrega->endereco_id) : '' ?>">
        </div>

        <div class="mb-3">
            <label for="status_entrega" class="form-label">Status da Entrega</label>
            <select class="form-select" name="status_entrega" id="status_entrega" required>
                <option value="">Selecione o status</option>
                <option value="A CAMINHO"
                    <?= isset($entrega->status_entrega) && $entrega->status_entrega === 'A CAMINHO' ? 'selected' : '' ?>>
                    A CAMINHO</option>
                <option value="ENTREGUE"
                    <?= isset($entrega->status_entrega) && $entrega->status_entrega === 'ENTREGUE' ? 'selected' : '' ?>>
                    ENTREGUE</option>
                <option value="CANCELADO"
                    <?= isset($entrega->status_entrega) && $entrega->status_entrega === 'CANCELADO' ? 'selected' : '' ?>>
                    CANCELADO</option>
            </select>
        </div>

        <input type="hidden" name="entregas_id"
            value="<?= isset($entrega->entregas_id) ? esc($entrega->entregas_id) : '' ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit">
                <?= ucfirst($form) ?> <i class="bi bi-floppy"></i>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pedidoSelect = document.getElementById('pedido_id');

    function buscarEndereco(pedidoId) {
        if (!pedidoId) {
            document.getElementById('endereco_id').value = '';
            document.getElementById('endereco_texto').value = '-- Selecione um pedido --';
            return;
        }

        fetch(`<?= base_url('entregas/getEnderecoPorPedido/') ?>${pedidoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.endereco) {
                    document.getElementById('endereco_id').value = data.endereco.enderecos_id;
                    document.getElementById('endereco_texto').value =
                        data.endereco.enderecos_rua + ', ' +
                        data.endereco.enderecos_numero + ' ' +
                        (data.endereco.enderecos_complemento || '');
                } else {
                    document.getElementById('endereco_id').value = '';
                    document.getElementById('endereco_texto').value = 'Endereço não encontrado';
                }
            }).catch(err => {
                console.error('Erro ao buscar endereço:', err);
                document.getElementById('endereco_id').value = '';
                document.getElementById('endereco_texto').value = 'Erro ao buscar endereço';
            });
    }

    pedidoSelect.addEventListener('change', function() {
        buscarEndereco(this.value);
    });
    if (pedidoSelect.value) {
        buscarEndereco(pedidoSelect.value);
    }
});
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