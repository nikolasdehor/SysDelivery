<!--Abre Produtos-->
<div id="produtos" class="container">

<h1>Produtos</h1>

<div class="row">

<?php 
$produtos = ['hamburguer1.png','hamburguer2.png','hamburguer3.png','hamburguer4.png','hamburguer5.png','pizza1.png','pizza2.png','pizza3.png','pizza4.png'];
for($i=0; $i < count($produtos);$i++){

?>
    <!-- card 1 -->
    <div class="col-sm-3 mb-3 pb-4 mb-sm-0">
        <div class="card">
            <img src="assets/images/<?= $produtos[$i] ?>"
                class="card-img-top">
            <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a
                    natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
            </div>
        </div>
    </div>



<?php } ?>

</div>

</div>

<!--Fecha Produtos-->