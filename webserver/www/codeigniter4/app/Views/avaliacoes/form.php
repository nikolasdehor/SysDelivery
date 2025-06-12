<?php
helper('functions');
session();
$template = 'Templates';

if (isset($_SESSION['login'])) {
    $login = $_SESSION['login'];
    if ($login->usuarios_nivel == 0) {
        $template = 'Templates_user';
    } elseif ($login->usuarios_nivel == 1) {
        $template = 'Templates_funcionario';
    } elseif ($login->usuarios_nivel == 2) {
        $template = 'Templates_admin';
    }
}
?>

<?= $this->extend($template) ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('/avaliacoes/produto/' . $produto->produtos_id) ?>">Avaliações</a></li>
            <li class="breadcrumb-item active"><?= $title ?></li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-star"></i> <?= $title ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('msg')): ?>
                        <?= session()->getFlashdata('msg') ?>
                    <?php endif; ?>

                    <!-- Informações do Produto -->
                    <div class="alert alert-light mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-1"><?= esc($produto->produtos_nome) ?></h5>
                                <p class="mb-0 text-muted"><?= esc($produto->produtos_descricao) ?></p>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="h5 text-success">
                                    R$ <?= number_format($produto->produtos_preco_venda, 2, ',', '.') ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário de Avaliação -->
                    <form action="<?= $action === 'adicionar' ? base_url('/avaliacoes/salvar') : base_url('/avaliacoes/atualizar') ?>" 
                          method="post" id="form-avaliacao">
                        
                        <?php if ($action === 'editar'): ?>
                            <input type="hidden" name="avaliacao_id" value="<?= $avaliacao->avaliacoes_id ?>">
                        <?php endif; ?>
                        
                        <input type="hidden" name="produto_id" value="<?= $produto->produtos_id ?>">

                        <!-- Nota -->
                        <div class="mb-4">
                            <label class="form-label">
                                <strong>Sua Nota *</strong>
                            </label>
                            <div class="rating-input">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" 
                                           id="star<?= $i ?>" 
                                           name="nota" 
                                           value="<?= $i ?>" 
                                           <?= (isset($avaliacao) && $avaliacao->avaliacoes_nota == $i) ? 'checked' : '' ?>
                                           required>
                                    <label for="star<?= $i ?>" class="star-label">
                                        <i class="bi bi-star-fill"></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                            <small class="form-text text-muted">Clique nas estrelas para dar sua nota</small>
                        </div>

                        <!-- Comentário -->
                        <div class="mb-4">
                            <label for="comentario" class="form-label">
                                <strong>Seu Comentário</strong> (opcional)
                            </label>
                            <textarea class="form-control" 
                                      id="comentario" 
                                      name="comentario" 
                                      rows="4" 
                                      maxlength="1000"
                                      placeholder="Conte sua experiência com este produto..."><?= isset($avaliacao) ? esc($avaliacao->avaliacoes_comentario) : '' ?></textarea>
                            <div class="form-text">
                                <span id="char-count">0</span>/1000 caracteres
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('/avaliacoes/produto/' . $produto->produtos_id) ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> 
                                <?= $action === 'adicionar' ? 'Enviar Avaliação' : 'Atualizar Avaliação' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dicas para uma boa avaliação -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightbulb"></i> Dicas para uma boa avaliação</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Seja honesto e imparcial em sua avaliação</li>
                        <li>Descreva sua experiência com o produto</li>
                        <li>Mencione pontos positivos e negativos</li>
                        <li>Evite linguagem ofensiva ou inadequada</li>
                        <li>Seja específico sobre qualidade, sabor, entrega, etc.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}

.rating-input input[type="radio"] {
    display: none;
}

.star-label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.star-label:hover,
.star-label:hover ~ .star-label {
    color: #ffc107;
}

.rating-input input[type="radio"]:checked ~ .star-label {
    color: #ffc107;
}

.rating-input input[type="radio"]:checked + .star-label {
    color: #ffc107;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres
    const comentario = document.getElementById('comentario');
    const charCount = document.getElementById('char-count');
    
    function updateCharCount() {
        charCount.textContent = comentario.value.length;
    }
    
    comentario.addEventListener('input', updateCharCount);
    updateCharCount(); // Inicializa o contador

    // Validação do formulário
    document.getElementById('form-avaliacao').addEventListener('submit', function(e) {
        const nota = document.querySelector('input[name="nota"]:checked');
        
        if (!nota) {
            e.preventDefault();
            alert('Por favor, selecione uma nota de 1 a 5 estrelas.');
            return false;
        }
    });

    // Efeito hover nas estrelas
    const stars = document.querySelectorAll('.star-label');
    const radioInputs = document.querySelectorAll('.rating-input input[type="radio"]');
    
    stars.forEach((star, index) => {
        star.addEventListener('mouseenter', function() {
            // Destaca esta estrela e todas as anteriores
            for (let i = stars.length - 1; i >= stars.length - 1 - index; i--) {
                stars[i].style.color = '#ffc107';
            }
        });
        
        star.addEventListener('mouseleave', function() {
            // Restaura o estado baseado na seleção atual
            const checkedInput = document.querySelector('.rating-input input[type="radio"]:checked');
            stars.forEach((s, i) => {
                if (checkedInput) {
                    const checkedValue = parseInt(checkedInput.value);
                    s.style.color = (stars.length - i <= checkedValue) ? '#ffc107' : '#ddd';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    // Atualiza as cores quando uma estrela é selecionada
    radioInputs.forEach(input => {
        input.addEventListener('change', function() {
            const value = parseInt(this.value);
            stars.forEach((star, index) => {
                star.style.color = (stars.length - index <= value) ? '#ffc107' : '#ddd';
            });
        });
    });
    
    // Inicializa as cores se já há uma seleção
    const checkedInput = document.querySelector('.rating-input input[type="radio"]:checked');
    if (checkedInput) {
        const value = parseInt(checkedInput.value);
        stars.forEach((star, index) => {
            star.style.color = (stars.length - index <= value) ? '#ffc107' : '#ddd';
        });
    }
});
</script>

<?= $this->endSection() ?>
