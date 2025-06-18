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

<div class="container">

    <h2 class="border-bottom border-2 border-primary mt-3 mb-4"> <?= $title ?> </h2>

    <?php
    // Exibe mensagens da sessão (flash messages)
    if (session()->getFlashdata('msg')) {
        echo session()->getFlashdata('msg');
    }
    // Exibe mensagens passadas diretamente
    if (isset($msg)) {
        echo $msg;
    }
    ?>

    <form action="<?= base_url('imgprodutos/search'); ?>" class="d-flex" role="search" method="post">
        <input class="form-control me-2" name="pesquisar" type="search" placeholder="Pesquisar" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">
            <i class="bi bi-search"></i>
        </button>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Img</th>
                <th scope="col">Link</th>
                <th scope="col">
                    <a class="btn btn-success" href="<?= base_url('imgprodutos/new'); ?>">
                        Novo
                        <i class="bi bi-plus-circle"></i>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody class="table-group-divider">

            <?php
            helper('image'); // Carrega o helper de imagens
            for ($i = 0; $i < count($imgprodutos); $i++) {
                $isExternalLink = isExternalImage($imgprodutos[$i]->imgprodutos_link);
                $linkDisplay = $isExternalLink ? $imgprodutos[$i]->imgprodutos_link : 'assets/' . $imgprodutos[$i]->imgprodutos_link;
            ?>
            <tr>
                <th scope="row"><?= $imgprodutos[$i]->imgprodutos_id; ?></th>
                <td>
                    <?= getImageTag(
                        $imgprodutos[$i]->imgprodutos_link,
                        $imgprodutos[$i]->imgprodutos_descricao,
                        '',
                        'width: 50px; object-fit: cover; border-radius: 4px;'
                    ) ?>
                </td>
                <td>
                    <small><?= $linkDisplay; ?></small>
                    <?php if ($isExternalLink): ?>
                        <br><span class="badge bg-info">Link Externo</span>
                    <?php else: ?>
                        <br><span class="badge bg-success">Upload Local</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a class="btn btn-primary"
                        href="<?= base_url('imgprodutos/edit/' . $imgprodutos[$i]->imgprodutos_id); ?>">
                        Editar
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a class="btn btn-danger"
                        href="<?= base_url('imgprodutos/delete/' . $imgprodutos[$i]->imgprodutos_id); ?>">
                        Excluir
                        <i class="bi bi-x-circle"></i>
                    </a>
                </td>
            </tr>
            <?php } ?>

        </tbody>
    </table>

</div>
<?= $this->endSection() ?>

<?php
} else {
    $data['msg'] = msg("O usuário não está logado!", "danger");
    echo view('login', $data);
}
?>