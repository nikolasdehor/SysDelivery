<?php
helper('functions');
session();

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];

    // CORREÇÃO: Permite o acesso para nível 2 (admin) e 1 (funcionário)
    if ($login->usuarios_nivel == 2 || $login->usuarios_nivel == 1) {

        // Carrega o template correto de acordo com o nível
        if ($login->usuarios_nivel == 2) {
            $template = 'Templates_admin';
        } else {
            $template = 'Templates_funcionario';
        }
        ?>

<?= $this->extend($template) ?>
<?= $this->section('content') ?>


<div class="container pt-4 pb-5 bg-light">
    <h2 class="border-bottom border-2 border-primary">
        <?= ucfirst($form) . ' ' . $title ?>
    </h2>

    <?php
    // Exibe erros de validação se existirem
    if (session()->getFlashdata('errors')) {
        echo '<div class="alert alert-danger">';
        foreach (session()->getFlashdata('errors') as $error) {
            echo '<p>' . $error . '</p>';
        }
        echo '</div>';
    }

    // Exibe erros de validação do CodeIgniter
    if (isset($validation)) {
        echo '<div class="alert alert-danger">';
        echo $validation->listErrors();
        echo '</div>';
    }
    ?>

    <form action="<?= base_url('categorias/' . $op); ?>" method="post">
        <div class="mb-3">
            <label for="categorias_nome" class="form-label">Nome da Categoria <span class="text-danger">*</span></label>
            <input type="text"
                   class="form-control"
                   name="categorias_nome"
                   id="categorias_nome"
                   value="<?= isset($categorias->categorias_nome) ? $categorias->categorias_nome : ''; ?>"
                   placeholder="Digite o nome da categoria (ex: Bebidas, Lanches, Sobremesas)"
                   required
                   style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-height: 40px;">
            <small class="form-text text-muted">Mínimo 3 caracteres, máximo 255 caracteres</small>
        </div>


        <input type="hidden" name="categorias_id" value="<?= $categorias->categorias_id; ?>">

        <div class="mb-3">
            <button class="btn btn-success" type="submit"> <?= ucfirst($form) ?> <i class="bi bi-floppy"></i></button>
        </div>

    </form>

</div>

<?= $this->endSection() ?>

<?php
    } else {
        // Se não for nível 2 ou 1, o acesso é negado
        $data['msg'] = msg("Sem permissão de acesso!", "danger");
        echo view('login', $data);
    }
} else {
    // Se não estiver logado
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}
?>