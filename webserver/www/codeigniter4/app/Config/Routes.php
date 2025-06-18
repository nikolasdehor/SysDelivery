<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/home', 'Home::index');

$routes->get('/cidades', 'Cidades::index');
$routes->get('/cidades/index', 'Cidades::index');
$routes->get('/cidades/new', 'Cidades::new');
$routes->post('/cidades/create', 'Cidades::create');
$routes->get('/cidades/edit/(:num)', 'Cidades::edit/$1');
$routes->post('/cidades/update', 'Cidades::update');
$routes->post('/cidades/search', 'Cidades::search');
$routes->get('/cidades/delete/(:num)', 'Cidades::delete/$1');

$routes->get('/categorias', 'Categorias::index');
$routes->get('/categorias/index', 'Categorias::index');
$routes->get('/categorias/new', 'Categorias::new');
$routes->post('/categorias/create', 'Categorias::create');
$routes->get('/categorias/edit/(:any)', 'Categorias::edit/$1');
$routes->post('/categorias/update', 'Categorias::update');
$routes->post('/categorias/search', 'Categorias::search');
$routes->get('/categorias/delete/(:any)', 'Categorias::delete/$1');

$routes->get('/produtos', 'Produtos::index');
$routes->get('/produtos/index', 'Produtos::index');
$routes->get('/produtos/new', 'Produtos::new');
$routes->post('/produtos/create', 'Produtos::create');
$routes->get('/produtos/edit/(:any)', 'Produtos::edit/$1');
$routes->post('/produtos/update', 'Produtos::update');
$routes->post('/produtos/search', 'Produtos::search');
$routes->get('/produtos/delete/(:any)', 'Produtos::delete/$1');

$routes->get('/usuarios', 'Usuarios::index');
$routes->get('/usuarios/index', 'Usuarios::index');
$routes->get('/usuarios/new', 'Usuarios::new');
$routes->post('/usuarios/create', 'Usuarios::create');
$routes->get('/usuarios/edit/(:any)', 'Usuarios::edit/$1');
$routes->get('/usuarios/delete/(:any)', 'Usuarios::delete/$1');
$routes->post('/usuarios/update', 'Usuarios::update');
$routes->post('/usuarios/search', 'Usuarios::search');
$routes->get('/usuarios/perfil/(:any)', 'Usuarios::perfil/$1');

$routes->get('/usuarios/edit_senha/(:any)', 'Usuarios::edit_senha/$1');
$routes->post('/usuarios/salvar_senha', 'Usuarios::salvar_senha');
$routes->get('/usuarios/acess', 'Usuarios::acess');

$routes->get('/usuarios/edit_nivel', 'Usuarios::edit_nivel');
$routes->post('/usuarios/salvar_nivel', 'Usuarios::salvar_nivel');

$routes->get('clientes', 'Clientes::index');
$routes->get('clientes/index', 'Clientes::index');
$routes->get('clientes/new', 'Clientes::new');
$routes->post('clientes/create', 'Clientes::create');
$routes->get('clientes/edit/(:num)', 'Clientes::edit/$1');
$routes->post('clientes/update', 'Clientes::update');
$routes->get('clientes/delete/(:num)', 'Clientes::delete/$1');
$routes->post('clientes/search', 'Clientes::search');

$routes->get('funcionarios', 'Funcionarios::index');
$routes->get('funcionarios/index', 'Funcionarios::index');
$routes->get('funcionarios/new', 'Funcionarios::new');
$routes->post('funcionarios/create', 'Funcionarios::create');
$routes->get('funcionarios/edit/(:num)', 'Funcionarios::edit/$1');
$routes->post('funcionarios/update', 'Funcionarios::update');
$routes->get('funcionarios/delete/(:num)', 'Funcionarios::delete/$1');
$routes->post('funcionarios/search', 'Funcionarios::search');

$routes->get('pedidos', 'Pedidos::index');
$routes->get('pedidos/index', 'Pedidos::index');
$routes->get('pedidos/new', 'Pedidos::new');
$routes->post('pedidos/create', 'Pedidos::create');
$routes->post('pedidos/createPedido', 'Pedidos::createPedido');
$routes->get('pedidos/show/(:num)', 'Pedidos::show/$1');
$routes->get('pedidos/edit/(:num)', 'Pedidos::edit/$1');
$routes->post('pedidos/update', 'Pedidos::update');
$routes->get('pedidos/delete/(:num)', 'Pedidos::delete/$1');
$routes->post('pedidos/search', 'Pedidos::search');
$routes->get('pedidos/produto/(:num)', 'Pedidos::selectProduto/$1');

$routes->get('vendas', 'Vendas::index');
$routes->get('vendas/index', 'Vendas::index');
$routes->get('vendas/new', 'Vendas::new');
$routes->post('vendas/create', 'Vendas::create');
$routes->get('vendas/edit/(:num)', 'Vendas::edit/$1');
$routes->post('vendas/update', 'Vendas::update');
$routes->get('vendas/delete/(:num)', 'Vendas::delete/$1');
$routes->post('vendas/search', 'Vendas::search');
$routes->get('vendas/getTotalPedido/(:num)', 'Vendas::getTotalPedido/$1');


$routes->get('/enderecos', 'Enderecos::index');
$routes->get('/enderecos/index', 'Enderecos::index');
$routes->get('/enderecos/new', 'Enderecos::new');
$routes->post('/enderecos/create', 'Enderecos::create');
$routes->get('/enderecos/edit/(:any)', 'Enderecos::edit/$1');
$routes->post('/enderecos/update', 'Enderecos::update');
$routes->post('/enderecos/search', 'Enderecos::search');
$routes->get('/enderecos/delete/(:any)', 'Enderecos::delete/$1');

$routes->get('/login', 'Login::index');
$routes->get('/login/index', 'Login::index');
$routes->post('/login/logar', 'Login::logar');
$routes->get('/login/logout', 'Login::logout');

$routes->get('/cadastro', 'Cadastro::index');
$routes->get('/cadastro/index', 'Cadastro::index');
$routes->post('/cadastro/salvar', 'Cadastro::salvar');

$routes->get('/admin', 'Admin::index');
$routes->get('/admin/index', 'Admin::index');

$routes->get('/funcionario', 'Admin::index');
$routes->get('/funcionario/index', 'Admin::index');

$routes->get('/user', 'User::index');
$routes->get('/user/index', 'User::index');

$routes->get('/imgprodutos', 'Imgprodutos::index');
$routes->get('/imgprodutos/index', 'Imgprodutos::index');
$routes->get('/imgprodutos/new', 'Imgprodutos::new');
$routes->post('/imgprodutos/create', 'Imgprodutos::create');
$routes->get('/imgprodutos/edit/(:any)', 'Imgprodutos::edit/$1');
$routes->post('/imgprodutos/update', 'Imgprodutos::update');
$routes->post('/imgprodutos/search', 'Imgprodutos::search');
$routes->get('/imgprodutos/delete/(:any)', 'Imgprodutos::delete/$1');

$routes->get('/relatorios/(:num)', 'Relatorios::index/$1');
$routes->get('/relatorios/index', 'Relatorios::index');

$routes->get('/entregas', 'Entregas::index');
$routes->get('/entregas/index', 'Entregas::index');
$routes->get('/entregas/new', 'Entregas::new');
$routes->post('/entregas/create', 'Entregas::create');
$routes->get('/entregas/edit/(:any)', 'Entregas::edit/$1');
$routes->post('/entregas/update', 'Entregas::update');
$routes->post('entregas/search', 'Entregas::search');
$routes->get('/entregas/delete/(:num)', 'Entregas::delete/$1');
$routes->get('entregas/getEnderecoPorPedido/(:num)', 'Entregas::getEnderecoPorPedido/$1'); 

$routes->get('/estoques', 'Estoques::index');
$routes->get('/estoques/index', 'Estoques::index');
$routes->get('/estoques/new', 'Estoques::new');
$routes->post('/estoques/create', 'Estoques::create');
$routes->get('/estoques/edit/(:any)', 'Estoques::edit/$1');
$routes->post('/estoques/update', 'Estoques::update');
$routes->post('estoques/search', 'Estoques::search');
$routes->get('/estoques/delete/(:any)', 'Estoques::delete/$1');

$routes->get('/itens_pedido', 'ItensPedido::index');
$routes->get('/itens_pedido/index', 'ItensPedido::index');
$routes->get('/itens_pedido/new', 'ItensPedido::new');
$routes->post('/itens_pedido/create', 'ItensPedido::create');
$routes->get('/itens_pedido/edit/(:any)', 'ItensPedido::edit/$1');
$routes->post('/itens_pedido/update', 'ItensPedido::update');
$routes->get('/itens_pedido/delete/(:any)', 'ItensPedido::delete/$1');
$routes->post('/itens_pedido/search', 'ItensPedido::search');
$routes->get('/itens_pedido/finalizar/(:num)', 'ItensPedido::finalizar_pedido/$1');

// Rotas do Carrinho
$routes->get('/carrinho', 'CarrinhoController::index');
$routes->post('/carrinho/adicionar', 'CarrinhoController::adicionar');
$routes->post('/carrinho/atualizar', 'CarrinhoController::atualizarQuantidade');
$routes->post('/carrinho/remover', 'CarrinhoController::remover');
$routes->get('/carrinho/limpar', 'CarrinhoController::limpar');
$routes->post('/carrinho/aplicar-cupom', 'CarrinhoController::aplicarCupom');
$routes->post('/carrinho/remover-cupom', 'CarrinhoController::removerCupom');
$routes->get('/carrinho/contar-itens', 'CarrinhoController::contarItens');
$routes->get('/carrinho/checkout', 'CarrinhoController::checkout');
$routes->post('/carrinho/processar-pedido', 'CarrinhoController::processarPedido');
$routes->get('/carrinho/teste', 'CarrinhoController::teste');
$routes->get('/carrinho/debug-total', 'CarrinhoController::debugTotal');

// Rotas de Avaliações
$routes->get('/avaliacoes/produto/(:num)', 'AvaliacoesController::produto/$1');
$routes->get('/avaliacoes/adicionar/(:num)', 'AvaliacoesController::adicionar/$1');
$routes->post('/avaliacoes/salvar', 'AvaliacoesController::salvar');

// Rotas de Setup (temporárias)
$routes->get('/setup/verificar-tabelas', 'SetupController::verificarTabelas');
$routes->get('/setup/criar-tabela-carrinho', 'SetupController::criarTabelaCarrinho');
$routes->get('/avaliacoes/editar/(:num)', 'AvaliacoesController::editar/$1');
$routes->post('/avaliacoes/atualizar', 'AvaliacoesController::atualizar');
$routes->get('/avaliacoes/remover/(:num)', 'AvaliacoesController::remover/$1');
$routes->post('/avaliacoes/moderar', 'AvaliacoesController::moderar');
$routes->get('/avaliacoes/recentes', 'AvaliacoesController::recentes');

// Rotas de Cupons
$routes->get('/cupons', 'CuponsController::index');
$routes->get('/cupons/novo', 'CuponsController::novo');
$routes->post('/cupons/criar', 'CuponsController::criar');
$routes->get('/cupons/editar/(:num)', 'CuponsController::editar/$1');
$routes->post('/cupons/atualizar', 'CuponsController::atualizar');
$routes->get('/cupons/remover/(:num)', 'CuponsController::remover/$1');
$routes->post('/cupons/toggle-status', 'CuponsController::toggleStatus');
$routes->post('/cupons/validar', 'CuponsController::validar');
$routes->get('/cupons/disponiveis', 'CuponsController::disponiveis');
$routes->post('/cupons/gerar-codigo', 'CuponsController::gerarCodigo');

// Rotas de Notificações
$routes->get('/notificacoes', 'NotificacoesController::index');
$routes->post('/notificacoes/marcar-lida', 'NotificacoesController::marcarLida');
$routes->post('/notificacoes/marcar-todas-lidas', 'NotificacoesController::marcarTodasLidas');
$routes->post('/notificacoes/remover', 'NotificacoesController::remover');
$routes->get('/notificacoes/limpar-lidas', 'NotificacoesController::limparLidas');
$routes->get('/notificacoes/contar-nao-lidas', 'NotificacoesController::contarNaoLidas');
$routes->get('/notificacoes/nao-lidas', 'NotificacoesController::naoLidas');
$routes->post('/notificacoes/enviar-todos', 'NotificacoesController::enviarParaTodos');
$routes->get('/notificacoes/form-geral', 'NotificacoesController::formEnviarGeral');
$routes->get('/notificacoes/estatisticas', 'NotificacoesController::estatisticas');
$routes->get('/notificacoes/limpar-antigas', 'NotificacoesController::limparAntigas');

// API Routes
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
    // API de Produtos
    $routes->resource('produtos', ['controller' => 'ProdutosApi']);
    $routes->get('produtos/categoria/(:num)', 'ProdutosApi::porCategoria/$1');

    // API de Carrinho
    $routes->get('carrinho', 'CarrinhoApi::index');
    $routes->post('carrinho/adicionar', 'CarrinhoApi::adicionar');
    $routes->put('carrinho/(:num)', 'CarrinhoApi::atualizar/$1');
    $routes->delete('carrinho/(:num)', 'CarrinhoApi::remover/$1');

    // API de Avaliações
    $routes->get('avaliacoes/produto/(:num)', 'AvaliacoesApi::produto/$1');
    $routes->post('avaliacoes', 'AvaliacoesApi::criar');
    $routes->put('avaliacoes/(:num)', 'AvaliacoesApi::atualizar/$1');
    $routes->delete('avaliacoes/(:num)', 'AvaliacoesApi::remover/$1');

    // API de Cupons
    $routes->post('cupons/validar', 'CuponsApi::validar');
    $routes->get('cupons/disponiveis', 'CuponsApi::disponiveis');
});

// Rotas do Dashboard
$routes->get('/dashboard', 'DashboardController::index');
$routes->get('/dashboard/funcionario', 'DashboardController::funcionario');
$routes->get('/dashboard/cliente', 'DashboardController::cliente');
$routes->get('/dashboard/api/(:segment)', 'DashboardController::apiDados/$1');

// Rotas de Rastreamento
$routes->get('/rastrear/(:num)', 'RastreamentoController::rastrear/$1');
$routes->post('/rastreamento/atualizar-status', 'RastreamentoController::atualizarStatus');
$routes->get('/rastreamento/status/(:num)', 'RastreamentoController::statusAtual/$1');
$routes->get('/rastreamento/gerenciar', 'RastreamentoController::gerenciar');