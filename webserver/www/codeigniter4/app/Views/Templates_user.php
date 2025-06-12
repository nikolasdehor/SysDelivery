<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include('templates/header.php'); ?>
</head>

<body>
    <!-- Estrutura principal com flexbox para ocupar toda a altura da tela -->
    <div class="d-flex flex-column min-vh-100">

        <!-- Navegação do usuário -->
        <?php include('templates/nav_user.php'); ?>

        <!-- Conteúdo principal -->
        <main class="flex-fill">
            <?= $this->renderSection('content'); ?>
        </main>

        <!-- Rodapé -->
        <?php include('templates/footer.php'); ?>
    </div>

    <?php include('templates/end.php'); ?>
</body>

</html>