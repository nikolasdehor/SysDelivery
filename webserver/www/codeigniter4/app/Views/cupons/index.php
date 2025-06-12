<?php
helper('functions');
session();
$template = 'Templates_admin'; // Apenas admin pode gerenciar cupons
?>

<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="border-bottom border-2 border-primary mb-0">
            <i class="bi bi-ticket-perforated"></i> <?= $title ?>
        </h2>
        <a href="<?= base_url('/cupons/novo') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Novo Cupom
        </a>
    </div>

    <?php if (session()->getFlashdata('msg')): ?>
        <?= session()->getFlashdata('msg') ?>
    <?php endif; ?>

    <!-- Estatísticas -->
    <?php if (isset($estatisticas)): ?>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary"><?= $estatisticas['total_cupons'] ?></h3>
                        <p class="mb-0">Total de Cupons</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success"><?= $estatisticas['cupons_ativos'] ?></h3>
                        <p class="mb-0">Cupons Ativos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-danger"><?= $estatisticas['cupons_expirados'] ?></h3>
                        <p class="mb-0">Cupons Expirados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info"><?= $estatisticas['total_usos'] ?></h3>
                        <p class="mb-0">Total de Usos</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3" method="get">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="ativo">Ativos</option>
                        <option value="inativo">Inativos</option>
                        <option value="expirado">Expirados</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="tipo">
                        <option value="">Todos</option>
                        <option value="percentual">Percentual</option>
                        <option value="valor_fixo">Valor Fixo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Buscar</label>
                    <input type="text" class="form-control" name="busca" placeholder="Código ou descrição">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Cupons -->
    <div class="card">
        <div class="card-body">
            <?php if (isset($cupons) && count($cupons) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Período</th>
                                <th>Uso</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cupons as $cupom): ?>
                                <tr>
                                    <td>
                                        <code class="bg-light p-1 rounded"><?= esc($cupom->cupons_codigo) ?></code>
                                    </td>
                                    <td><?= esc($cupom->cupons_descricao) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $cupom->cupons_tipo === 'percentual' ? 'info' : 'warning' ?>">
                                            <?= $cupom->cupons_tipo === 'percentual' ? 'Percentual' : 'Valor Fixo' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($cupom->cupons_tipo === 'percentual'): ?>
                                            <?= number_format($cupom->cupons_valor, 1) ?>%
                                        <?php else: ?>
                                            R$ <?= number_format($cupom->cupons_valor, 2, ',', '.') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?= date('d/m/Y', strtotime($cupom->cupons_data_inicio)) ?><br>
                                            até <?= date('d/m/Y', strtotime($cupom->cupons_data_fim)) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?= $cupom->cupons_usado ?><?= $cupom->cupons_limite_uso ? '/' . $cupom->cupons_limite_uso : '' ?>
                                        <?php if ($cupom->cupons_limite_uso): ?>
                                            <div class="progress mt-1" style="height: 4px;">
                                                <?php $porcentagem = ($cupom->cupons_usado / $cupom->cupons_limite_uso) * 100; ?>
                                                <div class="progress-bar" style="width: <?= min(100, $porcentagem) ?>%"></div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $hoje = date('Y-m-d');
                                        $status = 'ativo';
                                        $statusClass = 'success';
                                        $statusText = 'Ativo';
                                        
                                        if (!$cupom->cupons_ativo) {
                                            $status = 'inativo';
                                            $statusClass = 'secondary';
                                            $statusText = 'Inativo';
                                        } elseif ($hoje > $cupom->cupons_data_fim) {
                                            $status = 'expirado';
                                            $statusClass = 'danger';
                                            $statusText = 'Expirado';
                                        } elseif ($cupom->cupons_limite_uso && $cupom->cupons_usado >= $cupom->cupons_limite_uso) {
                                            $status = 'esgotado';
                                            $statusClass = 'warning';
                                            $statusText = 'Esgotado';
                                        }
                                        ?>
                                        <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url('/cupons/editar/' . $cupom->cupons_id) ?>" 
                                               class="btn btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <button type="button" 
                                                    class="btn btn-outline-<?= $cupom->cupons_ativo ? 'warning' : 'success' ?>"
                                                    onclick="toggleStatus(<?= $cupom->cupons_id ?>, <?= $cupom->cupons_ativo ? 0 : 1 ?>)"
                                                    title="<?= $cupom->cupons_ativo ? 'Desativar' : 'Ativar' ?>">
                                                <i class="bi bi-<?= $cupom->cupons_ativo ? 'pause' : 'play' ?>"></i>
                                            </button>
                                            
                                            <a href="<?= base_url('/cupons/remover/' . $cupom->cupons_id) ?>" 
                                               class="btn btn-outline-danger" 
                                               title="Remover"
                                               onclick="return confirm('Tem certeza que deseja remover este cupom?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-ticket-perforated display-1 text-muted"></i>
                    <h4 class="mt-3">Nenhum cupom encontrado</h4>
                    <p class="text-muted">Crie seu primeiro cupom de desconto</p>
                    <a href="<?= base_url('/cupons/novo') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Criar Cupom
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleStatus(cupomId, novoStatus) {
    if (!confirm('Tem certeza que deseja alterar o status deste cupom?')) {
        return;
    }
    
    fetch('<?= base_url('/cupons/toggle-status') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cupom_id=${cupomId}&status=${novoStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao alterar status do cupom');
    });
}
</script>

<?= $this->endSection() ?>
