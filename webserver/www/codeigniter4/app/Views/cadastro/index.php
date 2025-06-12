<div class="container p-5">
    <div class="row mt-5">
        <div class="mx-auto border border-1 border-secondary rounded p-5 col-lg-5">
            <div class="text-center mt-3 mb-3">
                <?php if(isset($msg)){echo $msg;} ?>
                <img src="<?php echo base_url('assets/images/sd_logo.png') ?>" alt="SysDelivery" width="180">
                <h2 class="p-3">Cadastrar uma Conta</h2>
            </div>

            <form action="<?php echo base_url('cadastro/salvar') ?>" method="post">
                <!-- input nome -->
                <div class="mb-3">
                    <label for="nome" class="form-label">
                        <i class="bi bi-person"></i>
                        Nome
                    </label>
                    <input type="text" name="nome" placeholder="Informe o seu nome" class="form-control" id="nome">
                </div>

                <!-- input sobrenome -->
                <div class="mb-3">
                    <label for="sobrenome" class="form-label">
                        <i class="bi bi-person"></i>
                        Sobrenome
                    </label>
                    <input type="text" name="sobrenome" placeholder="Informe o seu sobrenome" class="form-control" id="sobrenome">
                </div>

                <!-- input email -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope"></i>
                        Email
                    </label>
                    <input type="email" name="email" placeholder="Informe o seu email" class="form-control" id="email">
                </div>

                <!-- input cpf -->
                <div class="mb-3">
                    <label for="cpf" class="form-label">
                        <i class="bi bi-file-earmark-text"></i>
                        CPF
                    </label>
                    <input type="text" name="cpf" placeholder="Informe o seu CPF" class="form-control" id="cpf">
                </div>
                
                <!-- input telefone -->
                <div class="mb-3">
                    <label for="telefone" class="form-label">
                        <i class="bi bi-telephone"></i>
                        Telefone
                    </label>
                    <input type="text" name="telefone" placeholder="Informe o seu telefone" class="form-control" id="telefone">
                </div>

                <!-- input senha -->
                <div class="mb-3">
                    <label for="senha" class="form-label">
                        <i class="bi bi-lock"></i>
                        Senha
                    </label>
                    <input type="password" placeholder="Informe a senha" name="senha" class="form-control" id="senha">
                </div>

                <!-- input confirmar senha -->
                <div class="mb-3">
                    <label for="confirmar_senha" class="form-label">
                        <i class="bi bi-lock"></i>
                        Confirmar Senha
                    </label>
                    <input type="password" placeholder="Confirme a senha" name="confirmar_senha" class="form-control" id="confirmar_senha">
                </div>

                <!-- input data de nascimento -->
                <div class="mb-3">
                    <label for="data_nasc" class="form-label">
                        <i class="bi bi-calendar"></i>
                        Data de Nascimento
                    </label>
                    <input type="date" name="data_nasc" class="form-control" id="data_nasc">
                </div>

                <!-- input observações -->
                <div class="mb-3">
                    <label for="observacoes" class="form-label">
                        <i class="bi bi-chat-dots"></i>
                        Observações (opcional)
                    </label>
                    <textarea name="observacoes" placeholder="Observações adicionais" class="form-control" id="observacoes"></textarea>
                </div>

                <!-- botão Cadastrar -->
                <p class="text-center">
                    <button type="submit" class="btn btn-lg btn-primary">
                        Cadastrar 
                        <i class="bi bi-person-plus"></i>
                    </button>
                </p>

                <!-- link para login -->
                <div class="text-center mt-3 mb-3">
                    <p>Já tem uma conta? <a class="link-offset-2 link-underline link-underline-opacity-0" href="<?= base_url('login'); ?>">Faça login</a></p>
                </div>

            </form>

        </div>
    </div>
</div>