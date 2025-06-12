<?php

//define o nome do projeto
$projeto = 'projeto';

//chama o Helpes para usar o metodo base_url
require($projeto.'/Config/Helpers.php');

//redireciona a página para home do projeto
header('Location: '.base_url($projeto));

?>
