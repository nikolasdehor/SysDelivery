<?php
    include('templates/header.php');
    include('templates/nav.php');
    echo $this->renderSection('content');
    include('templates/sobre.php');
    include('templates/contato.php');
    include('templates/footer.php'); 
    include('templates/end.php');
?>