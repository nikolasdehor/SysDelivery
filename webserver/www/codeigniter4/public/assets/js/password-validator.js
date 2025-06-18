/**
 * Sistema de Validação de Qualidade de Senha
 * SysDelivery - Password Strength Validator
 */

class PasswordValidator {
    constructor(passwordFieldId, confirmPasswordFieldId = null, submitButtonId = null, options = {}) {
        this.passwordField = document.getElementById(passwordFieldId);
        this.confirmPasswordField = confirmPasswordFieldId ? document.getElementById(confirmPasswordFieldId) : null;
        this.submitButton = submitButtonId ? document.getElementById(submitButtonId) : null;
        this.options = {
            showRequirements: true,
            showStrengthBar: true,
            publicForm: false,
            ...options
        };
        
        this.requirements = {
            length: { min: 8, regex: /.{8,}/, message: 'Mínimo 8 caracteres' },
            uppercase: { regex: /[A-Z]/, message: 'Uma letra maiúscula' },
            lowercase: { regex: /[a-z]/, message: 'Uma letra minúscula' },
            number: { regex: /[0-9]/, message: 'Um número' },
            special: { regex: /[^A-Za-z0-9]/, message: 'Um caractere especial (!@#$%^&*)' }
        };
        
        this.init();
    }
    
    init() {
        if (!this.passwordField) {
            console.error('Campo de senha não encontrado');
            return;
        }
        
        this.createPasswordStrengthIndicator();
        this.attachEventListeners();
        this.updateSubmitButtonState();
    }
    
    createPasswordStrengthIndicator() {
        const container = document.createElement('div');
        container.className = 'password-strength-container';

        let html = '';

        // Barra de força (sempre mostrar)
        if (this.options.showStrengthBar) {
            html += `
                <div class="password-strength-bar">
                    <div class="strength-bar-fill" id="strength-bar-${this.passwordField.id}"></div>
                </div>
            `;
        }

        // Texto de força (sempre mostrar)
        html += `
            <div class="strength-text">
                <span id="strength-text-${this.passwordField.id}">Digite uma senha</span>
            </div>
        `;

        // Lista de requisitos (apenas se habilitado e SEM containers)
        if (this.options.showRequirements) {
            html += `
                <div class="requirements-list">
                    ${Object.entries(this.requirements).map(([key, req]) => `
                        <div class="requirement-item" data-requirement="${key}">
                            <span class="requirement-text text-danger">${req.message}</span>
                        </div>
                    `).join('')}
                </div>
            `;
        }

        container.innerHTML = html;

        this.passwordField.parentNode.insertBefore(container, this.passwordField.nextSibling);
        this.strengthContainer = container;
    }
    
    attachEventListeners() {
        this.passwordField.addEventListener('input', () => {
            this.validatePassword();
            this.updateSubmitButtonState();
        });
        
        this.passwordField.addEventListener('focus', () => {
            this.strengthContainer.style.display = 'block';
        });
        
        if (this.confirmPasswordField) {
            this.confirmPasswordField.addEventListener('input', () => {
                this.validatePasswordMatch();
                this.updateSubmitButtonState();
            });
        }
    }
    
    validatePassword() {
        const password = this.passwordField.value;
        const results = {};
        let score = 0;
        
        // Validar cada requisito
        Object.entries(this.requirements).forEach(([key, req]) => {
            const isValid = req.regex.test(password);
            results[key] = isValid;
            if (isValid) score++;

            // Atualizar indicador visual do requisito (apenas se mostrar requisitos)
            if (this.options.showRequirements) {
                const requirementItem = this.strengthContainer.querySelector(`[data-requirement="${key}"]`);
                if (requirementItem) {
                    const text = requirementItem.querySelector('.requirement-text');

                    if (isValid) {
                        text.classList.add('text-success');
                        text.classList.remove('text-danger');
                    } else {
                        text.classList.add('text-danger');
                        text.classList.remove('text-success');
                    }
                }
            }
        });
        
        // Atualizar barra de força
        this.updateStrengthBar(score, password.length);
        
        return results;
    }
    
    updateStrengthBar(score, passwordLength) {
        const strengthBar = document.getElementById(`strength-bar-${this.passwordField.id}`);
        const strengthText = document.getElementById(`strength-text-${this.passwordField.id}`);
        
        let strength = '';
        let color = '';
        let width = '0%';
        
        if (passwordLength === 0) {
            strength = 'Digite uma senha';
            color = '#6c757d';
            width = '0%';
        } else if (score < 2) {
            strength = 'Muito fraca';
            color = '#dc3545';
            width = '20%';
        } else if (score < 3) {
            strength = 'Fraca';
            color = '#fd7e14';
            width = '40%';
        } else if (score < 4) {
            strength = 'Média';
            color = '#ffc107';
            width = '60%';
        } else if (score < 5) {
            strength = 'Forte';
            color = '#20c997';
            width = '80%';
        } else {
            strength = 'Muito forte';
            color = '#198754';
            width = '100%';
        }
        
        strengthBar.style.width = width;
        strengthBar.style.backgroundColor = color;
        strengthText.textContent = `Força da senha: ${strength}`;
        strengthText.style.color = color;
        
        // Armazenar score para validação
        this.currentScore = score;
        this.isPasswordValid = score >= 4; // Requer pelo menos 4 dos 5 critérios
    }
    
    validatePasswordMatch() {
        if (!this.confirmPasswordField) return true;
        
        const password = this.passwordField.value;
        const confirmPassword = this.confirmPasswordField.value;
        const isMatch = password === confirmPassword && password.length > 0;
        
        // Remover indicadores anteriores
        const existingIndicator = this.confirmPasswordField.parentNode.querySelector('.password-match-indicator');
        if (existingIndicator) {
            existingIndicator.remove();
        }
        
        // Adicionar novo indicador se há conteúdo
        if (confirmPassword.length > 0) {
            const indicator = document.createElement('div');
            indicator.className = 'password-match-indicator mt-1';
            
            if (isMatch) {
                indicator.innerHTML = '<small class="text-success"><i class="bi bi-check-circle"></i> Senhas coincidem</small>';
            } else {
                indicator.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle"></i> Senhas não coincidem</small>';
            }
            
            this.confirmPasswordField.parentNode.insertBefore(indicator, this.confirmPasswordField.nextSibling);
        }
        
        this.isPasswordMatch = isMatch;
        return isMatch;
    }
    
    updateSubmitButtonState() {
        if (!this.submitButton) return;
        
        const isPasswordValid = this.isPasswordValid || false;
        const isPasswordMatch = this.confirmPasswordField ? (this.isPasswordMatch || false) : true;
        const canSubmit = isPasswordValid && isPasswordMatch;
        
        this.submitButton.disabled = !canSubmit;
        
        if (canSubmit) {
            this.submitButton.classList.remove('btn-secondary');
            this.submitButton.classList.add('btn-success');
            this.submitButton.title = '';
        } else {
            this.submitButton.classList.remove('btn-success');
            this.submitButton.classList.add('btn-secondary');
            this.submitButton.title = 'Complete todos os requisitos de senha para continuar';
        }
    }
    
    // Método público para verificar se a senha é válida
    isValid() {
        return this.isPasswordValid && (this.confirmPasswordField ? this.isPasswordMatch : true);
    }
    
    // Método público para obter a pontuação atual
    getScore() {
        return this.currentScore || 0;
    }
}

// Função de conveniência para inicializar rapidamente
function initPasswordValidator(passwordFieldId, confirmPasswordFieldId = null, submitButtonId = null, options = {}) {
    return new PasswordValidator(passwordFieldId, confirmPasswordFieldId, submitButtonId, options);
}

// Exportar para uso global
window.PasswordValidator = PasswordValidator;
window.initPasswordValidator = initPasswordValidator;
