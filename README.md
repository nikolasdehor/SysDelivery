# 🍕 SysDelivery

<div align="center">

![SysDelivery Logo](https://img.shields.io/badge/SysDelivery-Sistema%20de%20Delivery-orange?style=for-the-badge&logo=food&logoColor=white)

**Sistema completo de gerenciamento de delivery desenvolvido como projeto acadêmico**

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-EF4223?style=flat-square&logo=codeigniter&logoColor=white)](https://codeigniter.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com)

</div>

---

## 🚀 Sobre o Projeto

O **SysDelivery** é um sistema completo de gerenciamento de delivery desenvolvido como projeto acadêmico, demonstrando a implementação de funcionalidades modernas de e-commerce e delivery. O sistema oferece uma experiência completa desde o pedido até a entrega, com foco em segurança, usabilidade e performance.

### ✨ Principais Funcionalidades

#### 🛒 **E-commerce Completo**
- **Carrinho de Compras Inteligente**: Persistente, com cálculos automáticos e aplicação de cupons
- **Sistema de Avaliações**: Reviews com estrelas e comentários dos clientes
- **Cupons de Desconto**: Sistema flexível com validações automáticas
- **Catálogo de Produtos**: Organizado por categorias com busca avançada

#### 🔐 **Segurança Avançada**
- **Hash Seguro de Senhas**: Migração automática de MD5 para Argon2ID
- **Validação de Dados**: Sanitização automática e validação de CPF/telefone
- **Rate Limiting**: Proteção contra ataques de força bruta
- **Logs de Auditoria**: Rastreamento completo de ações do sistema

#### 📱 **Interface Moderna**
- **Design Responsivo**: Adaptável a qualquer dispositivo
- **Notificações em Tempo Real**: Sistema completo com badges dinâmicos
- **AJAX Integrado**: Atualizações sem reload da página
- **UX Otimizada**: Feedback visual e interações fluidas

#### 📊 **Gestão e Relatórios**
- **Dashboard Inteligente**: Métricas em tempo real para diferentes níveis de usuário
- **Rastreamento de Pedidos**: Timeline visual com atualizações automáticas
- **API REST**: Endpoints completos para integrações
- **Sistema de Pagamentos**: Gateway simulado com múltiplas formas

#### 👥 **Níveis de Acesso**
- **Cliente**: Pedidos, avaliações, rastreamento
- **Funcionário**: Gestão de pedidos e entregas
- **Administrador**: Controle total do sistema

---

## 🛠️ Tecnologias Utilizadas

<div align="center">

| Frontend | Backend | Banco de Dados | Ferramentas |
|----------|---------|----------------|-------------|
| ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white) | ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white) | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white) | ![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat-square&logo=docker&logoColor=white) |
| ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white) | ![CodeIgniter](https://img.shields.io/badge/CodeIgniter-EF4223?style=flat-square&logo=codeigniter&logoColor=white) | ![phpMyAdmin](https://img.shields.io/badge/phpMyAdmin-6C78AF?style=flat-square&logo=phpmyadmin&logoColor=white) | ![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=git&logoColor=white) |
| ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black) | ![REST API](https://img.shields.io/badge/REST-02569B?style=flat-square&logo=rest&logoColor=white) | | ![VS Code](https://img.shields.io/badge/VS%20Code-007ACC?style=flat-square&logo=visualstudiocode&logoColor=white) |
| ![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=flat-square&logo=bootstrap&logoColor=white) | ![Argon2](https://img.shields.io/badge/Argon2-Security-green?style=flat-square) | | |

</div>

---

## 🚀 Instalação e Configuração

### 📋 Pré-requisitos

- **PHP 8.0+** com extensões: `mysqli`, `json`, `mbstring`, `openssl`
- **MySQL 5.7+** ou **MariaDB 10.3+**
- **Servidor Web** (Apache/Nginx) ou PHP built-in server
- **Composer** (opcional, para dependências futuras)

### ⚡ Instalação Rápida

```bash
# 1. Clone o repositório
git clone https://github.com/nikolasdehor/SysDelivery.git
cd SysDelivery

# 2. Configure o banco de dados
mysql -u root -p -e "CREATE DATABASE sysdelivery CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p sysdelivery < webserver/projeto.sql

# 3. Configure a aplicação
cd webserver/www/codeigniter4
cp env .env

# 4. Edite o arquivo .env com suas configurações
nano .env

# 5. Inicie o servidor
php spark serve --host=0.0.0.0 --port=8080
```

### 🔧 Configuração do Banco

Edite o arquivo `.env` com suas credenciais:

```env
# Database
database.default.hostname = localhost
database.default.database = sysdelivery
database.default.username = seu_usuario
database.default.password = sua_senha
database.default.DBDriver = MySQLi
database.default.port = 3306

# Security
encryption.key = sua_chave_de_32_caracteres_aqui
```

### 🐳 Usando Docker (Alternativo)

```bash
# Inicie os containers
cd webserver
docker compose up -d

# Acesse o sistema
# http://localhost:8080
```

---

## 📖 Como Usar

### 🎯 Acesso ao Sistema

| Tipo de Usuário | URL de Acesso | Credenciais Padrão |
|------------------|---------------|-------------------|
| **Cliente** | `http://localhost:8080` | Cadastre-se no sistema |
| **Funcionário** | `http://localhost:8080/login` | Conforme dados no SQL |
| **Admin** | `http://localhost:8080/login` | Conforme dados no SQL |

### 🛍️ Fluxo do Cliente

1. **Cadastro/Login** → Acesso seguro ao sistema
2. **Navegação** → Explore produtos por categoria
3. **Carrinho** → Adicione produtos e aplique cupons
4. **Avaliação** → Deixe sua opinião sobre produtos
5. **Pedido** → Finalize com forma de pagamento
6. **Rastreamento** → Acompanhe seu pedido em tempo real

### 👨‍💼 Painel Administrativo

- **Dashboard**: Métricas e relatórios em tempo real
- **Produtos**: Gestão completa do catálogo
- **Pedidos**: Controle de status e entregas
- **Cupons**: Criação e gestão de promoções
- **Usuários**: Administração de acessos
- **Relatórios**: Análises de vendas e performance

---

## 🎨 Capturas de Tela

<div align="center">

### 🏠 Página Inicial
*Interface moderna e responsiva com catálogo de produtos*

### 🛒 Carrinho de Compras
*Sistema inteligente com aplicação de cupons e cálculos automáticos*

### ⭐ Sistema de Avaliações
*Reviews com estrelas e comentários dos clientes*

### 📊 Dashboard Administrativo
*Métricas em tempo real e relatórios detalhados*

</div>

---

## 🔌 API REST

O sistema inclui uma API REST completa para integrações:

```bash
# Listar produtos
GET /api/produtos

# Buscar produto específico
GET /api/produtos/{id}

# Validar cupom
POST /api/cupons/validar

# Gerenciar carrinho
POST /api/carrinho/adicionar
PUT /api/carrinho/{id}
DELETE /api/carrinho/{id}
```

**Autenticação**: Bearer Token
**Formato**: JSON
**Rate Limiting**: 60 req/min

---

## 🏗️ Arquitetura do Sistema

```
SysDelivery/
├── 📁 webserver/
│   ├── 📄 projeto.sql              # Schema do banco
│   ├── 📁 www/codeigniter4/
│   │   ├── 📁 app/
│   │   │   ├── 📁 Controllers/     # Lógica de negócio
│   │   │   │   ├── 📁 Api/         # Controllers da API
│   │   │   │   ├── 🛒 CarrinhoController.php
│   │   │   │   ├── ⭐ AvaliacoesController.php
│   │   │   │   ├── 🎫 CuponsController.php
│   │   │   │   └── 📊 DashboardController.php
│   │   │   ├── 📁 Models/          # Modelos de dados
│   │   │   │   ├── 🛒 Carrinho.php
│   │   │   │   ├── ⭐ Avaliacoes.php
│   │   │   │   ├── 🎫 Cupons.php
│   │   │   │   └── 🔔 Notificacoes.php
│   │   │   ├── 📁 Views/           # Interface do usuário
│   │   │   ├── 📁 Helpers/         # Funções auxiliares
│   │   │   │   └── 🔐 security_helper.php
│   │   │   └── 📁 Libraries/       # Bibliotecas customizadas
│   │   │       └── 💳 PagamentoGateway.php
│   │   └── 📁 public/              # Arquivos públicos
└── 📄 README.md                    # Este arquivo
```

---

## 🔒 Recursos de Segurança

- ✅ **Hash Seguro**: Senhas protegidas com Argon2ID
- ✅ **Validação Robusta**: Sanitização automática de dados
- ✅ **Rate Limiting**: Proteção contra ataques
- ✅ **CSRF Protection**: Tokens de segurança
- ✅ **SQL Injection**: Prevenção automática
- ✅ **Logs de Auditoria**: Rastreamento completo
- ✅ **Validação CPF/Telefone**: Algoritmos brasileiros

---

## 📈 Funcionalidades Avançadas

### 🛒 **Sistema de Carrinho**
- Persistência no banco de dados
- Cálculos automáticos de totais
- Aplicação de cupons em tempo real
- Validação de estoque

### ⭐ **Avaliações e Reviews**
- Sistema de estrelas (1-5)
- Comentários opcionais
- Cálculo automático de médias
- Moderação de conteúdo

### 🎫 **Cupons Inteligentes**
- Descontos percentuais e fixos
- Validação de datas e limites
- Valor mínimo de pedido
- Controle de uso

### 🔔 **Notificações em Tempo Real**
- Badges dinâmicos no menu
- Diferentes tipos (info, success, warning, danger)
- Notificações automáticas de status
- Histórico completo

### 📊 **Dashboard Analítico**
- Métricas em tempo real
- Gráficos interativos
- Relatórios de vendas
- Análise de performance

### 📦 **Rastreamento Avançado**
- Timeline visual do pedido
- Atualizações automáticas
- Notificações por status
- Página pública de consulta

---

## 🎓 Contexto Acadêmico

Este projeto foi desenvolvido como trabalho acadêmico, demonstrando:

- **Arquitetura MVC**: Separação clara de responsabilidades
- **Boas Práticas**: Código limpo e documentado
- **Segurança**: Implementação de medidas modernas
- **UX/UI**: Interface intuitiva e responsiva
- **API Design**: RESTful e bem estruturada
- **Banco de Dados**: Modelagem normalizada
- **Versionamento**: Controle com Git

---

## 🤝 Contribuição

Este é um projeto acadêmico, mas contribuições são bem-vindas:

1. **Fork** o projeto
2. **Crie** uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. **Commit** suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. **Push** para a branch (`git push origin feature/AmazingFeature`)
5. **Abra** um Pull Request

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

---

## 🙏 Agradecimentos

- **CodeIgniter Team** - Framework PHP excepcional
- **Bootstrap Team** - Framework CSS moderno
- **Comunidade PHP** - Recursos e documentação
- **Professores e Colegas** - Orientação e feedback

---

<div align="center">

**Desenvolvido com ❤️ para fins acadêmicos**

[![GitHub](https://img.shields.io/badge/GitHub-SysDelivery-181717?style=flat-square&logo=github)](https://github.com/nikolasdehor/SysDelivery)

*Demonstrando excelência em desenvolvimento web moderno*

</div>
