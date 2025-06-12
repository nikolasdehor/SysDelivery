<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include('templates/header.php'); ?>
</head>

<body>
    <!-- Container flexível que ocupa toda a altura da viewport -->
    <div class="d-flex flex-column min-vh-100">

        <!-- Navegação -->
        <?php include('templates/nav_funcionario.php'); ?>

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