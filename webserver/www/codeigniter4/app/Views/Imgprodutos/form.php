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

    <?= form_open_multipart('imgprodutos/' . $op) ?>
    <div class="mb-3">
        <label for="imgprodutos_descricao" class="form-label"> Descrição </label>
        <input type="text" class="form-control" name="imgprodutos_descricao"
            value="<?= $imgprodutos->imgprodutos_descricao; ?>" id="imgprodutos_descricao">
    </div>

    <div class="mb-3">
        <label for="imgprodutos_produtos_id" class="form-label"> Produto </label>
        <select class="form-control" name="imgprodutos_produtos_id" id="imgprodutos_produtos_id">
            <option value="">Selecione um produto</option>
            <?php
                    // Loop corrigido para usar a variável $produto
                    foreach ($produtos as $produto) {
                        $selected = '';
                        if (isset($imgprodutos->imgprodutos_produtos_id) && $imgprodutos->imgprodutos_produtos_id == $produto->produtos_id) {
                            $selected = 'selected';
                        }
                        ?>
            <option value="<?= $produto->produtos_id; ?>" <?= $selected; ?>>
                <?= $produto->produtos_nome; ?>
            </option>
            <?php
                    }
                    ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="imgprodutos_link" class="form-label"> Upload </label>
        <input type="file" class="form-control" name="imgprodutos_link" value="<?= $imgprodutos->imgprodutos_link; ?>"
            id="imgprodutos_link">
    </div>

    <input type="hidden" name="imgprodutos_id" value="<?= $imgprodutos->imgprodutos_id; ?>">

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