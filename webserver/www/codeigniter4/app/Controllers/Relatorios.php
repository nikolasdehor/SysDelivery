<?php

namespace App\Controllers;

use App\Models\Usuarios as Usuarios_model;
use App\Models\Produtos as Produtos_model;
use App\Models\Enderecos as Enderecos_model;
use App\Models\Cidades as Cidades_model;
use App\Models\Categorias as Categorias_model;
use App\Models\Pedidos as Pedidos_model;
use App\Models\Vendas as Vendas_model;
use App\Models\ItensPedido as ItensPedido_model;
use App\Models\Estoques as Estoques_model;
use App\Models\Clientes as Clientes_model;
use App\Models\Entregas as Entregas_model;
use App\Models\Funcionarios as Funcionarios_model;
use App\Libraries\RelatorioPDF;

class Relatorios extends BaseController
{
    protected $usuarios;
    protected $produtos;
    protected $enderecos;
    protected $cidades;
    protected $categorias;
    protected $pedidos;
    protected $vendas;
    protected $itensPedido;
    protected $estoques;
    protected $clientes;
    protected $entregas;
    protected $funcionarios;

    public function __construct()
    {
        $this->usuarios = new Usuarios_model();
        $this->produtos = new Produtos_model();
        $this->enderecos = new Enderecos_model();
        $this->cidades = new Cidades_model();
        $this->categorias = new Categorias_model();
        $this->pedidos = new Pedidos_model();
        $this->vendas = new Vendas_model();
        $this->itensPedido = new ItensPedido_model();
        $this->estoques = new Estoques_model();
        $this->clientes = new Clientes_model();
        $this->entregas = new Entregas_model();
        $this->funcionarios = new Funcionarios_model();
    }

    public function index(int $id)
    {
        require_once(APPPATH . 'Libraries/RelatorioPDF.php');

        $pdf = new \RelatorioPDF();
        $pdf->AliasNbPages();
        $pdf->SetMargins(25, 25, 20);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddPage('P', 'A4');

        $pdf->SetFont('Arial', 'B', 15);

        if ($id == 1) {
            $dados = $this->usuarios->findAll();
            $titulo = 'Relatório de Usuários';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(20, 10, 'ID', 1, 0, 'L', true);
            $pdf->Cell(70, 10, 'Nome', 1, 0, 'L', true);
            $pdf->Cell(70, 10, 'CPF', 1, 0, 'L', true);
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 12);
            foreach ($dados as $item) {
                $pdf->Cell(20, 8, $item->usuarios_id, 1);
                $pdf->Cell(70, 8, utf8_decode($item->usuarios_nome . ' ' . $item->usuarios_sobrenome), 1);
                $pdf->Cell(70, 8, utf8_decode($item->usuarios_cpf), 1);
                $pdf->Ln();
            }

            $pdf->Output('I', 'RelatorioUsuarios.pdf');
        } elseif ($id == 2) {
            $pdf->SetMargins(15, 25, 20);
            $dados = $this->produtos->findAll();
            $titulo = 'Relatório de Produtos';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(15, 10, 'ID', 1, 0, 'L', true);
            $pdf->Cell(40, 10, 'Nome', 1, 0, 'L', true);
            $pdf->Cell(50, 10, utf8_decode('Descrição'), 1, 0, 'L', true);
            $pdf->Cell(25, 10, 'Custo (R$)', 1, 0, 'L', true);
            $pdf->Cell(25, 10, 'Venda (R$)', 1, 0, 'L', true);
            $pdf->Cell(25, 10, 'Categoria ID', 1, 0, 'L', true);
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 10);
            foreach ($dados as $item) {
                $pdf->Cell(15, 8, $item->produtos_id, 1);
                $pdf->Cell(40, 8, utf8_decode($item->produtos_nome), 1);
                $pdf->Cell(50, 8, utf8_decode($item->produtos_descricao), 1);
                $pdf->Cell(25, 8, 'R$ ' . number_format($item->produtos_preco_custo, 2, ',', '.'), 1);
                $pdf->Cell(25, 8, 'R$ ' . number_format($item->produtos_preco_venda, 2, ',', '.'), 1);
                $pdf->Cell(25, 8, $item->produtos_categorias_id, 1);
                $pdf->Ln();
            }

            $pdf->Output('I', 'RelatorioProdutos.pdf');
        } elseif ($id == 3) {
            $pdf->SetMargins(2, 25, 20);
            $dados = $this->enderecos->findAll();
            $titulo = 'Relatório de Endereços';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(15, 10, 'ID', 1, 0, 'L', true);
            $pdf->Cell(40, 10, 'Rua', 1, 0, 'L', true);
            $pdf->Cell(25, 10, utf8_decode('Número'), 1, 0, 'L', true);
            $pdf->Cell(50, 10, 'Complemento', 1, 0, 'L', true);
            $pdf->Cell(25, 10, 'Status', 1, 0, 'L', true);
            $pdf->Cell(25, 10, 'Cidade ID', 1, 0, 'L', true);
            $pdf->Cell(25, 10, utf8_decode('Usuário ID'), 1, 0, 'L', true);
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 10);
            foreach ($dados as $item) {
                $status = ($item->enderecos_status == '1') ? 'Ativo' : 'Inativo';
                $pdf->Cell(15, 8, $item->enderecos_id, 1);
                $pdf->Cell(40, 8, utf8_decode($item->enderecos_rua), 1);
                $pdf->Cell(25, 8, utf8_decode($item->enderecos_numero), 1);
                $pdf->Cell(50, 8, utf8_decode($item->enderecos_complemento), 1);
                $pdf->Cell(25, 8, utf8_decode($status), 1);
                $pdf->Cell(25, 8, utf8_decode($item->enderecos_cidade_id), 1);
                $pdf->
                Cell(25, 8, utf8_decode($item->enderecos_usuario_id), 1);
                $pdf->Ln();
            }
            $pdf->Output('I', 'RelatorioEnderecos.pdf');
        } elseif ($id == 4) {
            $pdf->SetMargins(65, 25, 10);
            $dados = $this->cidades->findAll();
            $titulo = 'Relatório de Cidades';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(15, 10, 'ID', 1, 0, 'L', true);
            $pdf->Cell(40, 10, 'Nome', 1, 0, 'L', true);
            $pdf->Cell(25, 10, 'Estado', 1, 0, 'L', true);
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 10);
            foreach ($dados as $item) {
                $pdf->Cell(15, 8, $item->cidades_id, 1);
                $pdf->Cell(40, 8, utf8_decode($item->cidades_nome), 1);
                $pdf->Cell(25, 8, utf8_decode($item->cidades_uf), 1);
                $pdf->Ln();
            }

            $pdf->Output('I', 'RelatorioCidades.pdf');
        } elseif ($id == 5) {
            $pdf->SetMargins(60, 25, 20);
            // Relatório de Categorias
            $dados = $this->categorias->findAll();
            $titulo = 'Relatório de Categorias';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            // Cabeçalho da tabela
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', 'B', 12);

            // Definindo as colunas
            $pdf->Cell(20, 10, 'ID', 1,
                0, 'L', true);
            $pdf->Cell(70, 10, 'Nome', 1,
                0, 'L', true);
            $pdf->Ln();
            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', '', 12);
            // Adicionando os dados
            foreach ($dados as $item) {
                $pdf->Cell(20, 8, $item->categorias_id, 1);
                $pdf->Cell(70, 8, utf8_decode($item->categorias_nome), 1);
                $pdf->Ln();
            }
            // Saída do PDF
            $pdf->Output('I', 'RelatorioCategorias.pdf');
        } elseif ($id == 6) {
            $pdf->SetMargins(10, 25, 20);
            // Relatório de Pedidos
            $dados = $this->pedidos->findAll();
            $titulo = 'Relatório de Pedidos';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            // Cabeçalho da tabela
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', 'B', 12);

            // Definindo as colunas
            $pdf->Cell(20, 10, 'ID', 1,
                0, 'L', true);
            $pdf->Cell(70, 10, 'Cliente', 1,
                0, 'L', true);
            $pdf->Cell(40, 10, 'Data do Pedido', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Status', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Total (R$)', 1,
                0, 'L', true);
            $pdf->Ln();

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', '', 12);

            // Adicionando os dados
            foreach ($dados as $item) {
                $cliente = $this->clientes->find($item->clientes_id);
                $usuario = $this->usuarios->find($cliente->clientes_usuario_id);
                $status = ucfirst($item->status);
                $total = number_format($item->total_pedido, 2, ',', '.');

                $pdf->Cell(20, 8, $item->pedidos_id, 1);
                $pdf->Cell(70, 8,
                    utf8_decode($usuario->usuarios_nome . ' ' . $usuario->usuarios_sobrenome), 1);
                $pdf->Cell(40, 8,
                    date('d/m/Y H:i:s', strtotime($item->data_pedido)), 1);
                $pdf->Cell(30, 8, utf8_decode($status), 1);
                $pdf->Cell(30, 8, 'R$ ' . $total, 1);
                $pdf->Ln();
            }
            // Saída do PDF
            $pdf->Output('I', 'RelatorioPedidos.pdf');
        } elseif ($id == 7) {
            $pdf->SetMargins(0, 25, 20);
            // Relatório de Vendas
            $dados = $this->vendas->findAll();
            $titulo = 'Relatório de Vendas';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            // Cabeçalho da tabela
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', 'B', 12);

            // Definindo as colunas
            $pdf->Cell(20, 10, 'ID', 1,
                0, 'L', true);
            $pdf->Cell(80, 10, 'Cliente', 1,
                0, 'L', true);
            $pdf->Cell(40, 10, 'Data da Venda', 1,
                0, 'L', true);
            $pdf->Cell(35, 10, 'Forma Pagamento', 1,
                0, 'L', true);
            $pdf->Cell(35, 10, 'Valor Total (R$)', 1,
                0, 'L', true);
            $pdf->Ln();

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', '', 12);

            // Adicionando os dados
            foreach ($dados as $item) {
                $pedido = $this->pedidos->find($item->pedidos_id);
                $cliente = $this->clientes->find($pedido->clientes_id);
                $usuario = $this->usuarios->find($cliente->clientes_usuario_id);
                $forma_pagamento = ucfirst($item->forma_pagamento);
                $valor_total = number_format($item->valor_total, 2, ',', '.');

                $pdf->Cell(20, 10, $item->vendas_id, 1);
                $pdf->Cell(80, 10, $usuario->usuarios_nome . ' ' . $usuario->usuarios_sobrenome, 1);
                $pdf->Cell(40, 10,
                    date('d/m/Y H:i:s', strtotime($item->data_venda
                    )), 1);
                $pdf->Cell(35, 10, utf8_decode($forma_pagamento), 1);
                $pdf->Cell(35, 10, 'R$ ' . $valor_total, 1);
                $pdf->Ln();
            }
            // Saída do PDF
            $pdf->Output('I', 'RelatorioVendas.pdf');
        } elseif ($id == 8) {
            $pdf->SetMargins(30, 25, 20);
            // Relatório de Itens do Pedido
            $dados = $this->itensPedido->findAll();
            $titulo = 'Relatório de Itens do Pedido';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            // Cabeçalho da tabela
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', 'B', 12);

            // Definindo as colunas
            $pdf->Cell(20, 10, 'ID', 1,
                0, 'L', true);
            $pdf->Cell(70, 10, 'Produto', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Quantidade', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Preço Unitário (R$)', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Total (R$)', 1,
                0, 'L', true);
            $pdf->Ln();

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', '', 12);

            // Adicionando os dados
            foreach ($dados as $item) {
                $produto = $this->produtos->find($item->produtos_id);
                if ($produto) {
                    $total = number_format($item->preco_unitario * $item->quantidade,
                        2, ',', '.');
                    $preco_unitario = number_format($item->preco_unitario,
                        2, ',', '.');

                    $pdf->Cell(20, 8, $item->itens_pedido_id, 1);
                    $pdf->Cell(70, 8,
                        utf8_decode($produto->produtos_nome), 1);
                    $pdf
                        ->Cell(30, 8, $item->quantidade, 1);
                    $pdf->Cell(30, 8, 'R$ ' . $preco_unitario, 1);
                    $pdf->Cell(30, 8, 'R$ ' . $total, 1);
                    $pdf->Ln();
                } else {
                    $pdf->Cell(20, 8, $item->itens_pedido_id, 1);
                    $pdf->Cell(70, 8, 'Produto não encontrado', 1);
                    $pdf->Cell(30, 8, $item->quantidade, 1);
                    $pdf->Cell(30, 8, 'R$ 0.00', 1);
                    $pdf->Cell(30, 8, 'R$ 0.00', 1);
                    $pdf->Ln();
                }
            }
            // Saída do PDF
            $pdf->Output('I', 'RelatorioItensPedido.pdf');
        } elseif ($id == 9) {
            $pdf->SetMargins(40, 25, 20);
            // Relatório de Estoques
            $dados = $this->estoques->findAll();
            $titulo = 'Relatório de Estoques';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            // Cabeçalho da tabela
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', 'B', 12);

            // Definindo as colunas
            $pdf->Cell(20, 10, 'ID', 1,
                0, 'L', true);
            $pdf->Cell(70, 10, 'Produto', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Quantidade', 1,
                0, 'L', true);
            $pdf->Ln();

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', '', 12);

            // Adicionando os dados
            foreach ($dados as $item) {
                $produto = $this->produtos->find($item->produto_id);
                if ($produto) {
                    $pdf->Cell(20, 8, $item->estoques_id, 1);
                    $pdf->Cell(70, 8,
                        utf8_decode($produto->produtos_nome), 1);
                    $pdf->Cell(30, 8,
                        number_format($item->quantidade,
                            2), 1);
                    $pdf->Ln();
                } else {
                    $pdf->Cell(20, 8, $item->estoques_id, 1);
                    $pdf->Cell(70, 8,
                        'Produto não encontrado', 1);
                    $pdf->Cell(30, 8,
                        number_format($item->quantidade,
                            2), 1);
                    $pdf->Ln();
                }
            }
            // Saída do PDF
            $pdf->Output('I', 'RelatorioEstoques.pdf');
        } elseif ($id == 10) {
            $pdf->SetMargins(30, 25, 20);
            // Relatório de Clientes
            $dados = $this->clientes->findAll();
            $titulo = 'Relatório de Clientes';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            // Cabeçalho da tabela
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', 'B', 12);

            // Definindo as colunas
            $pdf->Cell(20, 10, 'ID', 1,
                0, 'L', true);
            $pdf->Cell(70, 10, 'Nome', 1,
                0, 'L', true);
            $pdf->Cell(70, 10, 'CPF', 1,
                0, 'L', true);
            $pdf->Ln();

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', '', 12);

            // Adicionando os dados
            foreach ($dados as $item) {
                $usuario = $this->usuarios->find($item->clientes_usuario_id);
                if ($usuario) {
                    $pdf->Cell(20, 8, $item->clientes_id, 1);
                    $pdf->Cell(70, 8,
                        utf8_decode($usuario->usuarios_nome . ' ' . $usuario->usuarios_sobrenome), 1);
                    $pdf->Cell(70, 8,
                        utf8_decode($usuario->usuarios_cpf), 1);
                    $pdf->Ln();
                }else{
                    $pdf->Cell(20, 8, $item->clientes_id, 1);
                    $pdf->Cell(70, 8,
                        'Usuário não encontrado', 1);
                    $pdf->Cell(70, 8,
                        'N/A', 1);
                    $pdf->Ln();
                }
            }
            // Saída do PDF
            $pdf->Output('I', 'RelatorioClientes.pdf');
        } elseif ($id == 11) {
            $pdf->SetMargins(5, 25, 20);
            // Relatório de Entregas
            $dados = $this->entregas->findAll();
            $titulo = 'Relatório de Entregas';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);
            // Cabeçalho da tabela
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);
            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', 'B', 12);
            // Definindo as colunas
            $pdf->Cell(20, 10, 'ID', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Pedido ID', 1,
                0, 'L', true);
            $pdf->Cell(70, 10, 'Funcionário', 1,
                0, 'L', true);
            $pdf->Cell(50, 10, 'Endereço', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Status', 1,
                0, 'L', true);
            $pdf->Ln();
            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', '', 12);
            // Adicionando os dados
            foreach ($dados as $item) {
                $funcionario = $this->funcionarios->find($item->funcionario_id);
                $usuario = $this->usuarios->find($funcionario->funcionarios_usuario_id);
                if ($usuario) {
                    $funcionario->usuarios_nome = $usuario->usuarios_nome;
                    $funcionario->usuarios_sobrenome = $usuario->usuarios_sobrenome;
                } else {
                    $funcionario->usuarios_nome = 'N/A';
                    $funcionario->usuarios_sobrenome = '';
                }
                $endereco = $this->enderecos->find($item->endereco_id);
                $status = ucfirst($item->status_entrega);

                $pdf->Cell(20, 8, $item->entregas_id, 1);
                $pdf->Cell(30, 8, $item->pedido_id, 1);
                $pdf->Cell(70, 8,
                    utf8_decode($funcionario->usuarios_nome . ' ' . $funcionario->usuarios_sobrenome), 1);
                $pdf->Cell(50, 8,
                    utf8_decode($endereco->enderecos_rua . ', ' . $endereco->enderecos_numero), 1);
                $pdf->Cell(30, 8, utf8_decode($status), 1);
                $pdf->Ln();
            }
            // Saída do PDF
            $pdf->Output('I', 'RelatorioEntregas.pdf');
        } elseif ($id == 12) {
            $pdf->SetMargins(10, 25, 20);
            // Relatório de Funcionários
            $dados = $this->funcionarios->findAll();
            $titulo = 'Relatório de Funcionários';
            $pdf->Cell(0, 10, utf8_decode($titulo), 0, 1, 'C');
            $pdf->Ln(3);

            // Cabeçalho da tabela
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', 'B', 8);

            // Definindo as colunas
            $pdf->Cell(5, 10, 'ID', 1,
                0, 'L', true);
            $pdf->Cell(60, 10, 'Nome', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'CPF', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Cargo', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Salário (R$)', 1,
                0, 'L', true);
            $pdf->Cell(30, 10, 'Data Admissão', 1,
                0, 'L', true);
            $pdf->Ln();

            // Definindo a fonte e o tamanho
            $pdf->SetFont('Arial', '', 8);

            // Adicionando os dados
            foreach ($dados as $item) {
                $usuario = $this->usuarios->find($item->funcionarios_usuario_id);
                if ($usuario) {
                    $pdf->Cell(5, 8, $item->funcionarios_id, 1);
                    $pdf->Cell(60, 8,
                        utf8_decode($usuario->usuarios_nome . ' ' . $usuario->usuarios_sobrenome), 1);
                    $pdf->Cell(30, 8,
                        utf8_decode($usuario->usuarios_cpf), 1);
                    $pdf->Cell(30, 8,
                        utf8_decode($item->funcionarios_cargo), 1);
                    $pdf->Cell(30, 8,
                        'R$ ' . number_format($item->funcionarios_salario, 2, ',', '.'), 1);
                    $pdf->Cell(30, 8,
                        date('d/m/Y', strtotime($item->funcionarios_data_admissao)), 1);
                    $pdf->Ln();
                } else {
                    $pdf->Cell(20, 8, $item->funcionarios_id, 1);
                    $pdf->Cell(70, 8,
                        'Usuário não encontrado', 1);
                    $pdf->Cell(70, 8,
                        'N/A', 1);
                    $pdf->Cell(30, 8,
                        utf8_decode($item->funcionarios_cargo), 1);
                    $pdf->Cell(30, 8,
                        'R$ 0.00', 1);
                    $pdf->Cell(30, 8,
                        'N/A', 1);
                    $pdf->Ln();
                }
            }
            // Saída do PDF
            $pdf->Output('I', 'RelatorioFuncionarios.pdf');
        }  else {
            echo "Relatório não encontrado.";
        }
        exit;
    }
}
