<?php

namespace App\Controllers;
use App\Models\Pedidos as Pedido;
use App\Models\Clientes as Cliente;
use App\Models\ItensPedido as ItensPedido;
use App\Models\Produtos as Produtos;
use App\Models\Enderecos as Enderecos;

class Pedidos extends BaseController
{
    private $pedidos;
    private $clientes;
    private $itensPedido;
    private $produtos;
    private $enderecos;
    private $db;

    public function __construct()
    {
        $this->pedidos = new Pedido();
        $this->clientes = new Cliente();
        $this->itensPedido = new ItensPedido();
        $this->produtos = new Produtos();
        $this->enderecos = new Enderecos();
        $this->db = \Config\Database::connect();
        helper('functions');
    }

    public function index()
    {
        $data = $this->request->getPost();

        $session = session();
        $usuarioId = $session->get('login')->usuarios_id;
        $usuarioNivel = $session->get('login')->usuarios_nivel;

        $cliente = $this->clientes->select('clientes_id')->where('clientes_usuario_id', $usuarioId)->first();

        if ($usuarioNivel == 2 || $usuarioNivel == 1) {
            $data['title'] = 'Pedidos';
            $data['pedidos'] = $this->pedidos
                ->join('clientes', 'clientes.clientes_id = pedidos.clientes_id')
                ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
                ->select('pedidos.*, clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
                ->findAll();
                return view('pedidos/index', $data);
        }elseif($usuarioNivel == 0){
            $data['title'] = 'Meus Pedidos';
            $data['itensPedido'] = $this->itensPedido
                ->join('produtos', 'produtos.produtos_id = itens_pedido.produtos_id')
                ->join('pedidos', 'pedidos.pedidos_id = itens_pedido.pedidos_id')
                ->select('itens_pedido.*, pedidos.*, produtos.*')
                ->where('pedidos.clientes_id', $cliente->clientes_id)
                ->orderBy('itens_pedido.pedidos_id', 'ASC')
                ->orderBy('itens_pedido.itens_pedido_id', 'ASC')
                ->findAll();
            return view('pedidos/index', $data);
        }
        return redirect()->to('/')->with('msg', msg('Nível de usuário não autorizado para acessar os pedidos.', 'danger'));
    }

    public function new()
    {
        $session = session();
        $usuarioId = $session->get('login')->usuarios_id;
        $usuarioNivel = $session->get('login')->usuarios_nivel;
        if ($usuarioNivel == 2 || $usuarioNivel == 1) {
            $data['title'] = 'Novo Pedido';
            $data['op'] = 'create';
            $data['form'] = 'Cadastrar';
    
            $data['clientes'] = $this->clientes
                ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
                ->select('clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, usuarios.usuarios_cpf')
                ->findAll();
    
            $data['produtos'] = $this->produtos
                ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
                ->select('produtos.*, categorias.categorias_nome')
                ->findAll();
    
            $data['enderecos'] = $this->enderecos
                ->join('cidades', 'cidades.cidades_id = enderecos.enderecos_cidade_id')
                ->join('usuarios', 'usuarios.usuarios_id = enderecos.enderecos_usuario_id')
                ->select('enderecos.*, cidades.cidades_nome, cidades.cidades_uf, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
                ->where('usuarios.usuarios_id', $usuarioId)
                ->findAll();
    
            $data['itens_pedido'] = $this->itensPedido
                ->join('produtos', 'produtos.produtos_id = itens_pedido.produtos_id')
                ->select('itens_pedido.*, produtos.produtos_nome, produtos.produtos_preco_venda')
                ->findAll();
    
            $data['pedidos'] = $this->pedidos
                ->join('clientes', 'clientes.clientes_id = pedidos.clientes_id')
                ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
                ->select('pedidos.*, clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
                ->orderBy('pedidos.pedidos_id', 'ASC')
                ->findAll();
    
    
            return view('pedidos/form', $data);
        }elseif ($usuarioNivel == 0) {
            $cliente = $this->clientes->select('*')->where('clientes_usuario_id', $usuarioId)->first();

            if (!$cliente) {
                $data['errors'] = ['Cliente não encontrado para o usuário logado.'];
                $data['title'] = 'Login';

                return view('login', $data);
            }

            $data['title'] = 'Meus Pedidos';
            $data['op'] = 'createPedido';
            $data['form'] = 'Cadastrar';
            $data['pedidos'] = null;
            $data['clientes'] = [$cliente];
            $data['enderecos'] = $this->enderecos
                ->join('cidades', 'cidades.cidades_id = enderecos.enderecos_cidade_id')
                ->join('usuarios', 'usuarios.usuarios_id = enderecos.enderecos_usuario_id')
                ->select('enderecos.*, cidades.cidades_nome, cidades.cidades_uf, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
                ->where('usuarios.usuarios_id', $usuarioId)
                ->findAll();
            $data['produtos'] = $this->produtos
                ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
                ->select('produtos.*, categorias.categorias_nome')
                ->findAll();
            $data['itensPedido'] = null;

            return view('pedidos/form', $data);
        } else {
            return redirect()->to('/')->with('msg', msg('Nível de usuário não autorizado para acessar os pedidos.', 'danger'));
        }

    }

    public function selectProduto($produtoId){
        $session = session();
        if($session){
            $usuarioId = $session->get('login')->usuarios_id ?? null;
    
            if($usuarioId){
                if($produtoId){
                    $data['selectProduto'] = $this->produtos
                        ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
                        ->select('produtos.*, categorias.categorias_nome')
                        ->where('produtos_id', $produtoId)->findAll();
                }
        
                $cliente = $this->clientes->select('*')->where('clientes_usuario_id', $usuarioId)->first();
        
                if (!$cliente) {
                    $data['errors'] = ['Cliente não encontrado para o usuário logado.'];
                    $data['title'] = 'Login';
        
                    return view('login', $data);
                }
        
                $data['title'] = 'Meus Pedidos';
                $data['op'] = 'createPedido';
                $data['form'] = 'Cadastrar';
                $data['msg'] = msg('Agora finalize o pedido!', 'success');
                $data['pedidos'] = null;
                $data['clientes'] = [$cliente];
                $data['enderecos'] = $this->enderecos
                    ->join('cidades', 'cidades.cidades_id = enderecos.enderecos_cidade_id')
                    ->join('usuarios', 'usuarios.usuarios_id = enderecos.enderecos_usuario_id')
                    ->select('enderecos.*, cidades.cidades_nome, cidades.cidades_uf, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
                    ->where('usuarios.usuarios_id', $usuarioId)
                    ->findAll();
                $data['produtos'] = $this->produtos
                    ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
                    ->select('produtos.*, categorias.categorias_nome')
                    ->findAll();
                $data['itensPedido'] = null;
        
                return view('pedidos/form', $data);
            }else{
                return redirect()->to('/login')->with('msg', msg('Cadastre-se ou faça Login!', 'danger'));
            }
        }
    }

    public function create()
    {
        if (!$this->validate([
            'clientes_id' => 'required',
            'status' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pedidos->save([
            'clientes_id' => $this->request->getPost('clientes_id'),
            'data_pedido' => date('Y-m-d H:i:s'),
            'status' => $this->request->getPost('status'),
            'observacoes' => $this->request->getPost('observacoes'),
            'total_pedido' => moedaDolar($this->request->getPost('total_pedido'))
        ]);

        return redirect()->to('/pedidos')->with('msg', msg('Pedido cadastrado com sucesso!', 'success'));
    }

    public function createPedido(){
        $data = $this->request->getPost();

        $session = session();
        $usuarioId = $session->get('login')->usuarios_id;

        $cliente = $this->clientes->select('clientes_id')->where('clientes_usuario_id', $usuarioId)->first();

        if (!$cliente) {
            $data['errors'] = ['Cliente não encontrado para o usuário logado.'];
            $data['title'] = 'Login';

            return view('login', $data);
        }

        if (!$this->validate([
            'status' => 'required',
            'produtos' => 'required',
            'quantidades' => 'required',
        ])) {
            $data['msg'] = msg('Erro ao validar os dados do pedido.', 'danger');
            return view('pedidos', $data);
        }

        $produtos = $data['produtos'];
        $quantidades = $data['quantidades'];
        $total = 0;

        
        $this->db->transStart();

        $pedidoData = [
            'clientes_id' => $cliente->clientes_id,
            'data_pedido' => date('Y-m-d H:i:s'),
            'status' => $data['status'],
            'observacoes' => $data['observacoes'] ?? null,
            'total_pedido' => 0
        ];
        $this->pedidos->save($pedidoData);
        $pedidos_id = $this->pedidos->getInsertID();

        foreach ($produtos as $index => $produto_id) {
            $quantidade = $quantidades[$index] ?? 0;
            if ($quantidade > 0) {
                $produto = $this->produtos->find($produto_id);
                if ($produto) {
                    $preco_unitario = moedaDolar($produto->produtos_preco_venda);
                    $subtotal = $preco_unitario * $quantidade;
                    $total += $subtotal;

                    $this->itensPedido->save([
                        'pedidos_id' => $pedidos_id,
                        'produtos_id' => $produto_id,
                        'quantidade' => $quantidade,
                        'preco_unitario' => $preco_unitario
                    ]);
                }
            }
        }

        if ($total == 0) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('errors', ['Nenhum item válido foi adicionado.']);
        }

        $this->pedidos->update($pedidos_id, [
            'total_pedido' => $total
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('errors', ['Erro ao salvar pedido e itens.']);
        }

        return redirect()->to('/pedidos')->with('msg', msg('Pedido e itens cadastrados com sucesso!', 'success'));
    }

    public function show($id)
    {
        $data['title'] = 'Detalhes do Pedido';
        $data['pedidos'] = $this->pedidos
            ->join('clientes', 'clientes.clientes_id = pedidos.clientes_id')
            ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
            ->select('pedidos.*, clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
            ->find($id);
        $data['itens_pedido'] = $this->itensPedido
            ->join('produtos', 'produtos.produtos_id = itens_pedido.produtos_id')
            ->where('itens_pedido.pedidos_id', $id)
            ->select('itens_pedido.*, produtos.produtos_nome, produtos.produtos_preco')
            ->findAll();

        if (!$data['pedidos']) {
            return redirect()->to('/pedidos')->with('msg', msg('Pedido não encontrado!', 'danger'));
        }

        return view('pedidos/show', $data);
    }

    public function delete($id)
    {
        $this->pedidos->delete($id);
        return redirect()->to('/pedidos')->with('msg', msg('Pedido deletado com sucesso!', 'success'));
    }

    public function edit($id)
    {
        $nivel = session()->get('login')->usuarios_nivel;
        if ($nivel == 2 || $nivel == 1) {
            $data['title'] = 'Pedido';
            $data['clientes'] = $this->clientes
                ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
                ->select('clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, usuarios.usuarios_cpf')
                ->findAll();
            $data['produtos'] = $this->produtos
                ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
                ->select('produtos.*, categorias.categorias_nome')
                ->findAll();
            $data['enderecos'] = $this->enderecos
                ->join('cidades', 'cidades.cidades_id = enderecos.enderecos_cidade_id')
                ->join('usuarios', 'usuarios.usuarios_id = enderecos.enderecos_usuario_id')
                ->select('enderecos.*, cidades.cidades_nome, cidades.cidades_uf, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
                ->findAll();
            $data['itensPedido'] = null;
            $data['pedidos'] = $this->pedidos->find($id);
            $data['op'] = 'update';
            $data['form'] = 'Editar';
            return view('pedidos/form', $data);
        }elseif ($nivel == 0) {
            $usuarioId = session()->get('login')->usuarios_id;

            $cliente = $this->clientes->select('*')->where('clientes_usuario_id', $usuarioId)->first();

            if (!$cliente) {
                $data['errors'] = ['Cliente não encontrado para o usuário logado.'];
                $data['title'] = 'Login';

                return view('login', $data);
            }

            $data['title'] = 'Meus Pedidos';
            $data['op'] = 'update';
            $data['form'] = 'Alterar';
            $data['pedidos'] = $this->pedidos
                ->join('clientes', 'clientes.clientes_id = pedidos.clientes_id')
                ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
                ->select('pedidos.*, clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
                ->where('pedidos.pedidos_id', $id)
                ->where('clientes.clientes_usuario_id', $usuarioId)
                ->first();
            if (!$data['pedidos']) {
                return redirect()->to('/pedidos')->with('msg', msg('Pedido não encontrado ou não autorizado!', 'danger'));
            }
            $data['clientes'] = $this->clientes
                ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
                ->select('clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, usuarios.usuarios_cpf')
                ->where('clientes.clientes_usuario_id', $usuarioId)
                ->findAll();
            $data['enderecos'] = $this->enderecos
                ->join('cidades', 'cidades.cidades_id = enderecos.enderecos_cidade_id')
                ->join('usuarios', 'usuarios.usuarios_id = enderecos.enderecos_usuario_id')
                ->select('enderecos.*, cidades.cidades_nome, cidades.cidades_uf, usuarios.usuarios_nome, usuarios.usuarios_sobrenome')
                ->where('usuarios.usuarios_id', $usuarioId)
                ->findAll();
            $data['produtos'] = $this->produtos
                ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
                ->select('produtos.*, categorias.categorias_nome')
                ->findAll();
            $data['itensPedido'] = $this->itensPedido
                ->join('produtos', 'produtos.produtos_id = itens_pedido.produtos_id')
                ->join('pedidos', 'pedidos.pedidos_id = itens_pedido.pedidos_id')
                ->select('itens_pedido.*, pedidos.*, produtos.*')
                ->where('pedidos.clientes_id', $cliente->clientes_id)
                ->where('pedidos.pedidos_id', $id)
                ->orderBy('itens_pedido.pedidos_id', 'ASC')
                ->orderBy('itens_pedido.itens_pedido_id', 'ASC')
                ->findAll();
            return view('pedidos/form', $data);
        }else {
            $data['msg'] = msg("Sem permissão de acesso!", "danger");
            return view('login', $data);
        }
    }

    public function atualizaStatus($status, $pedido_id){
        $updateStatus = $this->pedidos->where('pedidos_id', $pedido_id)
                            ->set('status', $status)
                            ->update();

        if (!$updateStatus) {
            $this->db->transRollback();
            throw new \Exception('Atualização de status não realizada operação revertida', 501);
        }

        $this->db->transCommit();
        return $updateStatus;
    }

    public function update()
    {
        $nivel = session()->get('login')->usuarios_nivel;
        if ($nivel == 2 || $nivel == 1) {
            // Administrador
            $id = $this->request->getPost('pedidos_id');

            if (!$this->validate([
                'clientes_id' => 'required',
                'status' => 'required',
                'observacoes' => 'permit_empty',
                'total_pedido' => 'required|decimal'
            ])) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
            $this->db->transStart();
            $this->atualizaStatus($this->request->getPost('status') ,$id);
            $this->pedidos->update($id, [
                'clientes_id' => $this->request->getPost('clientes_id'),
                'observacoes' => $this->request->getPost('observacoes'),
                'total_pedido' => moedaDolar($this->request->getPost('total_pedido'))
            ]);
            $this->db->transCommit();
            if ($this->request->getPost('status') !== 'em rota de entrega'){
                return redirect()->to('/pedidos')->with('msg', msg('Pedido alterado com sucesso!', 'success'));
            }
            return redirect()->to('/entregas')->with('msg', msg('O pedido está em rota de entrega, realize o cadastro da entrega', 'success'));
            

        } elseif ($nivel == 0) {
            $usuarioId = session()->get('login')->usuarios_id;
        
            $cliente = $this->clientes->select('*')->where('clientes_usuario_id', $usuarioId)->first();
        
            if (!$cliente) {
                return redirect()->to('/pedidos')->with('msg', msg('Cliente não encontrado para o usuário logado.', 'danger'));
            }
        
            $data = $this->request->getPost();

            
            $this->db->transStart();

            // Atualiza o pedido principal
            $this->atualizaStatus($data['status'], $data['pedidos_id']);
            $this->pedidos->update($data['pedidos_id'], [
                'observacoes' => $data['observacoes'],
                'status' => $data['status'] ,$data['pedidos_id'],
                'enderecos_id' => $data['enderecos_id'],
                'total_pedido' => str_replace(['R$', '.', ','], ['', '', '.'], $data['total_pedido']),
            ]);

            // Remove os itens antigos
            $this->itensPedido->where('pedidos_id', $data['pedidos_id'])->delete();

            // Insere os novos itens
            $produtos = $data['produtos'];
            $quantidades = $data['quantidades'];

            foreach ($produtos as $index => $produto_id) {
                $this->itensPedido->insert([
                    'pedidos_id' => $data['pedidos_id'],
                    'produtos_id' => $produto_id,
                    'quantidade' => $quantidades[$index]
                ]);
            }

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                return redirect()->to('/pedidos');
            }

            return redirect()->to('/pedidos')->with('msg', 'Pedido atualizado com sucesso!');
        }
    }

    public function search()
    {
        $search = $this->request->getPost('pesquisar');
        $data['pedidos'] = $this->pedidos
            ->join('clientes', 'clientes.clientes_id = pedidos.clientes_id')
            ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
            ->like('usuarios.usuarios_nome', $search)
            ->orLike('pedidos.status', $search)
            ->findAll();
        
        $total = count($data['pedidos']);
        $data['msg'] = msg("Dados encontrados: {$total}", 'success');
        $data['title'] = 'Meus Pedidos';
        return view('pedidos/index', $data);
    }
}