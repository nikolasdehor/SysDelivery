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

    <?php
    // Exibe erros de validação se existirem
    if (isset($errors) && !empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
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

    <!-- Opções de imagem -->
    <div class="mb-3">
        <label class="form-label">Tipo de Imagem</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_imagem" id="tipo_upload" value="upload" checked>
            <label class="form-check-label" for="tipo_upload">
                Upload de arquivo
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tipo_imagem" id="tipo_link" value="link">
            <label class="form-check-label" for="tipo_link">
                Link da imagem
            </label>
        </div>
    </div>

    <!-- Upload de arquivo -->
    <div class="mb-3" id="div_upload">
        <label for="imgprodutos_file" class="form-label">Upload de Arquivo <span class="text-muted">(JPG, PNG, GIF, WEBP - Máx: 10MB)</span></label>
        <input type="file" class="form-control" name="imgprodutos_file" id="imgprodutos_file"
               accept="image/jpg,image/jpeg,image/png,image/gif,image/webp">
    </div>

    <!-- Link da imagem -->
    <div class="mb-3" id="div_link" style="display: none;">
        <label for="imgprodutos_url" class="form-label">URL da Imagem <span class="text-danger">*</span></label>
        <input type="url" class="form-control" name="imgprodutos_url" id="imgprodutos_url"
               placeholder="https://exemplo.com/imagem.jpg"
               value="<?= isset($imgprodutos->imgprodutos_link) && !str_starts_with($imgprodutos->imgprodutos_link, 'uploads/') ? $imgprodutos->imgprodutos_link : ''; ?>">
        <small class="form-text text-muted">Cole aqui o link direto da imagem (deve terminar com .jpg, .png, .gif, etc.)</small>
    </div>

    <input type="hidden" name="imgprodutos_id" value="<?= $imgprodutos->imgprodutos_id; ?>">

    <div class="mb-3">
        <button class="btn btn-success" type="submit"> <?= ucfirst($form) ?> <i class="bi bi-floppy"></i></button>
    </div>

    </form>

</div>

<script>
// JavaScript para alternar entre upload e link
document.addEventListener('DOMContentLoaded', function() {
    const tipoUpload = document.getElementById('tipo_upload');
    const tipoLink = document.getElementById('tipo_link');
    const divUpload = document.getElementById('div_upload');
    const divLink = document.getElementById('div_link');
    const inputFile = document.getElementById('imgprodutos_file');
    const inputUrl = document.getElementById('imgprodutos_url');

    // Função para alternar entre os tipos
    function alternarTipo() {
        if (tipoUpload.checked) {
            divUpload.style.display = 'block';
            divLink.style.display = 'none';
            inputFile.required = true;
            inputUrl.required = false;
            inputUrl.value = '';
        } else {
            divUpload.style.display = 'none';
            divLink.style.display = 'block';
            inputFile.required = false;
            inputUrl.required = true;
            inputFile.value = '';
        }
    }

    // Verificar se é edição com link existente
    const linkExistente = '<?= isset($imgprodutos->imgprodutos_link) ? $imgprodutos->imgprodutos_link : ""; ?>';
    if (linkExistente && !linkExistente.startsWith('uploads/')) {
        tipoLink.checked = true;
        alternarTipo();
    }

    // Event listeners
    tipoUpload.addEventListener('change', alternarTipo);
    tipoLink.addEventListener('change', alternarTipo);

    // Inicializar
    alternarTipo();
});
</script>

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