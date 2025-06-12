<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ItensPedido as Itens_Pedido;
use App\Models\Pedidos as Pedidos;
use App\Models\Produtos as Produtos;

helper('functions');

class ItensPedido extends BaseController
{
    protected $itens_Pedido;
    protected $pedidos;
    protected $produtos;

    public function __construct()
    {
        $this->itens_Pedido = new Itens_Pedido();
        $this->pedidos = new Pedidos();
        $this->produtos = new Produtos();
    }

    public function index()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('itens_pedido');
        $builder->select('itens_pedido.*, produtos.produtos_nome AS produto_nome, produtos.produtos_preco_venda');
        $builder->join('produtos', 'produtos.produtos_id = itens_pedido.produtos_id', 'left');
        $builder->join('pedidos', 'pedidos.pedidos_id = itens_pedido.pedidos_id', 'left');

        $builder->orderBy('itens_pedido.pedidos_id', 'ASC');
        $builder->orderBy('itens_pedido.itens_pedido_id', 'ASC');

        $query = $builder->get();

        $data['title'] = 'Itens do Pedido';
        $data['itens_pedido'] = $query->getResult();

        return view('itens_pedido/index', $data);
    }
    public function new()
    {
        $data['title'] = 'Novo Item do Pedido';
        $data['op'] = 'create';
        $data['form'] = 'Cadastrar';
        $data['pedidos'] = $this->pedidos->findAll();
        $data['produtos'] = $this->produtos->findAll();
        $data['itens_pedido'] = (object) [
            'pedidos_id' => '',
            'produtos_id' => '',
            'quantidade' => '',
            'preco_unitario' => '',
            'itens_pedido_id' => ''
        ];
        return view('itens_pedido/form', $data);
    }

    public function create()
    {
        if (
            !$this->validate([
                'pedidos_id' => 'required',
                'produtos_id' => 'required',
                'quantidade' => 'required|integer',
                'preco_unitario' => 'required|decimal'
            ])
        ) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->itens_Pedido->save([
            'pedidos_id' => $this->request->getPost('pedidos_id'),
            'produtos_id' => $this->request->getPost('produtos_id'),
            'quantidade' => $this->request->getPost('quantidade'),
            'preco_unitario' => $this->request->getPost('preco_unitario')
        ]);

        return redirect()->to('/itens_pedido')->with('msg', '<div class="alert alert-success">Item do pedido cadastrado com sucesso!</div>');
    }

    public function edit($id)
    {
        $data['title'] = 'Editar Item do Pedido';
        $data['op'] = 'update';
        $data['form'] = 'Atualizar';
        $data['pedidos'] = $this->pedidos->findAll();
        $data['produtos'] = $this->produtos->findAll();
        $data['itens_pedido'] = $this->itens_Pedido->find($id);

        if (!$data['itens_pedido']) {
            return redirect()->to(site_url('itens_pedido'))->with('error', 'Item do pedido não encontrado.');
        }

        return view('itens_pedido/form', $data);
    }

    public function update()
    {
        $id = $this->request->getPost('itens_pedido_id');

        if (
            !$this->validate([
                'pedidos_id' => 'required',
                'produtos_id' => 'required',
                'quantidade' => 'required|integer',
                'preco_unitario' => 'required|decimal'
            ])
        ) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->itens_Pedido->update($id, [
            'pedidos_id' => $this->request->getPost('pedidos_id'),
            'produtos_id' => $this->request->getPost('produtos_id'),
            'quantidade' => $this->request->getPost('quantidade'),
            'preco_unitario' => $this->request->getPost('preco_unitario')
        ]);

        return redirect()->to('/itens_pedido')->with('msg', msg('Item do pedido atualizado com sucesso!', 'success'));
    }

    public function delete($id)
    {
        if (!$this->itens_Pedido->find($id)) {
            return redirect()->to('/itens_pedido')->with('msg', msg('Item não encontrado!', 'warning'));
        }
        $this->itens_Pedido->delete($id);
        return redirect()->to('/itens_pedido')->with('msg', msg('Item do pedido deletado com sucesso!', 'success'));
    }

    public function search()
    {
        $search = $this->request->getGet('search');
        $data['title'] = 'Itens do Pedido - Pesquisa';
        $data['itens_pedido'] = $this->itens_Pedido
            ->join('pedidos', 'pedidos.pedidos_id = itens_pedido.pedidos_id')
            ->join('produtos', 'produtos.produtos_id = itens_pedido.produtos_id')
            ->select('itens_pedido.*, pedidos.pedidos_id as pedido_id, produtos.produtos_nome, produtos.produtos_preco_venda')
            ->like('produtos.produtos_nome', $search)
            ->orLike('pedidos.pedidos_id', $search)
            ->findAll();

        return view('itens_pedido/index', $data);
    }

    public function finalizar_pedido($id)
    {
        $pedido = $this->pedidos->find($id);
        if (!$pedido) {
            return redirect()->to('/itens_pedido')->with('msg', msg('Pedido não encontrado!', 'danger'));
        }

        $itens = $this->itens_Pedido->where('pedidos_id', $id)->findAll();

        if (empty($itens)) {
            return redirect()->to('/itens_pedido')->with('msg', msg('O pedido não possui itens!', 'warning'));
        }

        // Soma o total do pedido
        $totalPedido = 0;
        foreach ($itens as $item) {
            $totalPedido += $item->preco_unitario;
        }

        if ($this->pedidos->update($id, ['total_pedido' => $totalPedido])) {
            return redirect()->to('/itens_pedido')->with('msg', msg('Pedido finalizado com sucesso!', 'success'));
        } else {
            return redirect()->to('/itens_pedido')->with('msg', msg('Erro ao atualizar o pedido!', 'danger'));
        }
    }


}