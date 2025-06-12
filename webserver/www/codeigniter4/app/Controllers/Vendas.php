<?php

namespace App\Controllers;

use App\Models\ItensPedido;
use App\Models\Vendas as Venda;
use App\Models\Pedidos as Pedido;
use App\Controllers\Pedidos as PedidosController;


class Vendas extends BaseController
{
    private $vendas;
    private $pedidos;
    private $itensPedido;

    private $estoquesController;

    private $pedidosController;

    private $db;

    public function __construct()
    {
        $this->vendas = new Venda();
        $this->pedidos = new Pedido();
        $this->estoquesController = new Estoques();
        $this->pedidosController = new PedidosController();
        $this->itensPedido = new ItensPedido();
        $this->db = \Config\Database::connect();
        helper('functions');
    }

    public function index(): string
    {
        $data['title'] = 'Vendas';
        $data['vendas'] = $this->vendas
            ->select('vendas.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, pedidos.data_pedido')
            ->join('pedidos', 'vendas.pedidos_id = pedidos.pedidos_id')
            ->join('clientes', 'pedidos.clientes_id = clientes.clientes_id')
            ->join('usuarios', 'clientes.clientes_usuario_id = usuarios.usuarios_id')
            ->findAll();

        return view('vendas/index', $data);
    }

    public function new(): string
    {
        $data['title'] = 'Nova Venda';
        $data['op'] = 'create';
        $data['form'] = 'Cadastrar';
        $data['pedidos'] = $this->pedidos
            ->select('pedidos.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
            ->join('usuarios', 'usuarios.usuarios_id = pedidos.clientes_id')
            ->findAll();
        $data['venda'] = (object) [
            'pedidos_id' => '',
            'data_venda' => date('Y-m-d H:i:s'),
            'forma_pagamento' => '',
            'valor_total' => '0.00',
            'observacoes' => '',
            'vendas_id' => ''
        ];
        return view('vendas/form', $data);
    }

    public function create()
    {
        if (!$this->validate([
            'pedidos_id' => 'required',
            'forma_pagamento' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        try{
            $this->db->transStart();

            //Montei a query perfeitinha
            $query = $this->itensPedido->select('produtos_id')
                           ->selectSum('quantidade', 'quantidade_total') 
                           ->where('pedidos_id', $this->request->getPost('pedidos_id'))
                           ->groupBy('produtos_id')
                           ->get();
            $itemDoPedido = $query->getResult();
            
            //Abati no estoque
            foreach ($itemDoPedido as $item) {
                $this->estoquesController->saida_estoque($item->quantidade_total, $item->produtos_id);
            }      
            //Cadastrei a venda papae
            $this->vendas->save([
                'pedidos_id' => $this->request->getPost('pedidos_id'),
                'data_venda' => date('Y-m-d H:i:s'),
                'forma_pagamento' => $this->request->getPost('forma_pagamento'),
                'valor_total' => moedaDolar($this->request->getPost('valor_total')),
                'observacoes' => $this->request->getPost('observacoes')
            ]);
            //Venda Cadastrada = Pedido Concluído
            $this->pedidosController->atualizaStatus('Concluido', $this->request->getPost('pedidos_id'));
            $this->db->transCommit();
            $this->db->transComplete();


        }catch(\Exception $e){
            //Deu ruim volta
            $this->db->transRollback();
            throw $e;
        }

        return redirect()->to('/vendas')->with('msg', msg('Venda cadastrada com sucesso, o pedido referente essa venda será marcado como concluído automaticamente.', 'success'));
    }

    public function delete($id)
    {
        $this->vendas->delete($id);
        return redirect()->to('/vendas')->with('msg', msg('Venda deletada com sucesso!', 'success'));
    }

    public function edit($id)
    {
        $data['title'] = 'Editar Venda';
        $data['pedidos'] = $this->pedidos
            ->select('pedidos.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
            ->join('usuarios', 'usuarios.usuarios_id = pedidos.clientes_id')
            ->findAll();
        $data['venda'] = $this->vendas->find($id);
        $data['op'] = 'update';
        $data['form'] = 'Alterar';
        return view('vendas/form', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('vendas_id');

        if (
            !$this->validate([
                'pedidos_id' => 'required|is_natural_no_zero',
                'data_venda' => 'required|valid_date[Y-m-d\TH:i]',
                'forma_pagamento' => 'required',
                'valor_total' => 'required',
                'observacoes' => 'permit_empty'
            ])
        ) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->vendas->update($id, [
            'pedidos_id' => $this->request->getPost('pedidos_id'),
            'data_venda' => $this->request->getPost('data_venda'),
            'forma_pagamento' => $this->request->getPost('forma_pagamento'),
            'valor_total' => moedaDolar($this->request->getPost('valor_total')),
            'observacoes' => $this->request->getPost('observacoes')
        ]);

        return redirect()->to('/vendas')->with('msg', msg('Venda atualizada com sucesso!', 'success'));
    }



    public function search()
    {
        $search = $this->request->getPost('pesquisar');
        $data['vendas'] = $this->vendas
            ->select('vendas.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, pedidos.data_pedido')
            ->join('pedidos', 'vendas.pedidos_id = pedidos.pedidos_id')
            ->join('clientes', 'pedidos.clientes_id = clientes.clientes_id')
            ->join('usuarios', 'clientes.clientes_usuario_id = usuarios.usuarios_id')
            ->like('forma_pagamento', $search)
            ->orLike('observacoes', $search)
            ->findAll();

        $total = count($data['vendas']);
        $data['msg'] = msg("Dados encontrados: {$total}", 'success');
        $data['title'] = 'Vendas';
        return view('vendas/index', $data);
    }

    public function getTotalPedido($id)
    {
        $pedido = $this->pedidos->find($id);

        if (!$pedido) {
            return $this->response->setStatusCode(404)->setJSON(['erro' => 'Pedido não encontrado']);
        }

        return $this->response->setJSON(['total_pedido' => $pedido->total_pedido]);
    }

}