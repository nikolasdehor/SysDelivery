<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
// Função para atualizar badges do carrinho e notificações
function atualizarBadges() {
    // Atualiza badge do carrinho
    fetch('<?= base_url('/carrinho/contar-itens') ?>')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('carrinho-badge');
            if (badge && data.total_itens > 0) {
                badge.textContent = data.total_itens;
                badge.style.display = 'inline';
            } else if (badge) {
                badge.style.display = 'none';
            }
        })
        .catch(error => console.log('Erro ao buscar itens do carrinho:', error));

    // Atualiza badge de notificações
    fetch('<?= base_url('/notificacoes/contar-nao-lidas') ?>')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificacoes-badge');
            if (badge && data.nao_lidas > 0) {
                badge.textContent = data.nao_lidas;
                badge.style.display = 'inline';
            } else if (badge) {
                badge.style.display = 'none';
            }
        })
        .catch(error => console.log('Erro ao buscar notificações:', error));
}

// Função para carregar notificações no dropdown
function carregarNotificacoes() {
    fetch('<?= base_url('/notificacoes/nao-lidas') ?>')
        .then(response => response.json())
        .then(data => {
            const dropdown = document.getElementById('notificacoes-dropdown');
            if (!dropdown) return;

            let html = '<li><h6 class="dropdown-header">Notificações</h6></li><li><hr class="dropdown-divider"></li>';

            if (data.notificacoes && data.notificacoes.length > 0) {
                data.notificacoes.forEach(notif => {
                    const tipoClass = {
                        'info': 'text-info',
                        'success': 'text-success',
                        'warning': 'text-warning',
                        'danger': 'text-danger'
                    }[notif.notificacoes_tipo] || 'text-info';

                    html += `
                        <li class="dropdown-item-text p-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 ${tipoClass}">${notif.notificacoes_titulo}</h6>
                                    <p class="mb-1 small">${notif.notificacoes_mensagem}</p>
                                    <small class="text-muted">${new Date(notif.notificacoes_data).toLocaleString()}</small>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary ms-2"
                                        onclick="marcarComoLida(${notif.notificacoes_id})">
                                    <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </li>
                    `;
                });

                html += `
                    <li><hr class="dropdown-divider"></li>
                    <li class="text-center p-2">
                        <a href="<?= base_url('/notificacoes') ?>" class="btn btn-sm btn-primary">Ver todas</a>
                        <button class="btn btn-sm btn-outline-secondary ms-1" onclick="marcarTodasLidas()">
                            Marcar todas como lidas
                        </button>
                    </li>
                `;
            } else {
                html += '<li class="text-center p-3 text-muted">Nenhuma notificação</li>';
            }

            dropdown.innerHTML = html;
        })
        .catch(error => console.log('Erro ao carregar notificações:', error));
}

// Função para marcar notificação como lida
function marcarComoLida(notificacaoId) {
    fetch('<?= base_url('/notificacoes/marcar-lida') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `notificacao_id=${notificacaoId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarBadges();
            carregarNotificacoes();
        }
    })
    .catch(error => console.log('Erro ao marcar notificação:', error));
}

// Função para marcar todas como lidas
function marcarTodasLidas() {
    fetch('<?= base_url('/notificacoes/marcar-todas-lidas') ?>', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarBadges();
            carregarNotificacoes();
        }
    })
    .catch(error => console.log('Erro ao marcar notificações:', error));
}

// Inicializa quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    atualizarBadges();

    // Carrega notificações quando o dropdown é aberto
    const notifDropdown = document.querySelector('[data-bs-toggle="dropdown"]');
    if (notifDropdown) {
        notifDropdown.addEventListener('show.bs.dropdown', carregarNotificacoes);
    }

    // Atualiza badges a cada 30 segundos
    setInterval(atualizarBadges, 30000);
});
</script>
</body>
</html>