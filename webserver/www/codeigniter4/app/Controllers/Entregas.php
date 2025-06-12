<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Enderecos;
use App\Models\Entregas as ModelsEntregas;
use App\Models\Funcionarios;
use App\Models\Pedidos;
use CodeIgniter\Database\Config;


helper('functions');

class Entregas extends BaseController
{
    private $entregas;
    protected $db;
    private $pedido;
    private $endereco;
    private $funcionario;

    public function __construct()
    {
        $this->entregas = new ModelsEntregas();
        $this->pedido = new Pedidos();
        $this->endereco = new Enderecos();
        $this->funcionario = new Funcionarios();
        $this->db = Config::connect();

    }

    private function getEntregasComJoins()
    {
        return $this->entregas
            ->select('entregas.*, pedidos.*, enderecos.enderecos_rua, usuarios.usuarios_nome AS funcionario_nome')
            ->join('pedidos', 'pedido_id = pedidos_id')
            ->join('enderecos', 'endereco_id = enderecos_id')
            ->join('funcionarios', 'funcionario_id = funcionarios_id')
            ->join('usuarios', 'usuarios.usuarios_id = funcionarios.funcionarios_usuario_id')
            ->findAll();
    }


    public function index(): string
    {
        $data['title'] = 'Entregas';
        $data['msg'] = session()->getFlashdata('msg') ?? '';
        $data['entregas'] = $this->getEntregasComJoins();
        return view('entregas/index', $data);
    }

    public function new(): string
    {
        $data['title'] = 'Nova Entrega';
        $data['op'] = 'create';
        $data['form'] = 'Cadastrar';
        $data['pedidos'] = $this->pedido->findAll();
        $data['enderecos'] = $this->endereco->findAll();
        $data['funcionarios'] = $this->funcionario
            ->select('funcionarios.funcionarios_id, usuarios.usuarios_nome')
            ->join('usuarios', 'usuarios.usuarios_id = funcionarios.funcionarios_usuario_id')
            ->findAll();

        $data['entregas'] = (object) [
            'pedido_id' => '',
            'endereco_id' => '',
            'funcionario_id' => '',
            'status_entrega' => ''
        ];
        return view('entregas/form', $data);
    }

    public function create()
    {
        $post = $this->request->getPost();

        if (
            !$this->validate([
                'pedido_id' => 'required|is_natural_no_zero',
                'endereco_id' => 'required|is_natural_no_zero',
                'funcionario_id' => 'required|is_natural_no_zero',
                'status_entrega' => 'required|in_list[A CAMINHO,ENTREGUE,CANCELADO]',
            ])
        ) {
            return redirect()->back()->withInput()->with('msg', msg('Erro na validação!', 'danger'));
        }

        $this->entregas->save($post);

        return redirect()->to('/entregas')->with('msg', msg('Entrega cadastrada com sucesso!', 'success'));
    }

    public function delete($id)
    {
        $this->entregas->delete((int) $id);
        return redirect()->to('/entregas')->with('msg', msg('Entrega deletada com sucesso!', 'success'));
    }

    public function edit($id)
    {
        $data['entrega'] = $this->entregas->find($id);
        $data['pedidos'] = $this->pedido->findAll();
        $data['enderecos'] = $this->endereco->findAll();
        $data['funcionarios'] = $this->funcionario
            ->select('funcionarios.funcionarios_id, usuarios.usuarios_nome')
            ->join('usuarios', 'usuarios.usuarios_id = funcionarios.funcionarios_usuario_id')
            ->findAll();

        $data['title'] = 'Editar Entrega';
        $data['form'] = 'Editar';
        $data['op'] = 'update';
        return view('entregas/form', $data);
    }

    public function atualiza_status_entrega($status_entrega, $entregas_id){
        $updateStatus = $this->entregas->where('entregas_id', $entregas_id)
                            ->set('status_entrega', $status_entrega)
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
        $postData = $this->request->getPost();
        if (
            !$this->validate([
                'pedido_id' => 'required|is_natural_no_zero',
                'endereco_id' => 'required|is_natural_no_zero',
                'funcionario_id' => 'required|is_natural_no_zero',
                'status_entrega' => 'required|in_list[A CAMINHO,ENTREGUE,CANCELADO]',
            ])
        ) {
            return redirect()->back()->withInput()->with('msg', msg('Erro na validação!', 'danger'));
        }
        $this->db->transStart();
        
        $this->atualiza_status_entrega($postData['status_entrega'], $postData['entregas_id']);
        $updateStatus = $this->entregas->update($postData['entregas_id'], [
            'pedido_id' => $postData['pedido_id'],
            'endereco_id' => $postData['endereco_id'],
            'funcionario_id' => $postData['funcionario_id'],
        ]);

        if (!$updateStatus) {
            $this->db->transRollback();
        }
        if ($postData['status_entrega'] != 'ENTREGUE') {
            return redirect()->to('/entregas')->with('msg', msg('Entrega atualizada com sucesso!', 'success'));
        }
        return redirect()->to('/vendas')->with('msg', msg('Entrega efetuada, realize o cadastro de venda', 'success'));
    }

    public function search()
    {
        $pesquisar = $this->request->getPost('pesquisar') ?? '';

        $data['entregas'] = $this->entregas
            ->join('pedidos', 'pedido_id = pedidos_id')
            ->join('enderecos', 'endereco_id = enderecos_id')
            ->join('funcionarios', 'funcionario_id = funcionarios_id')
            ->like('status_entrega', $pesquisar)
            ->find();

        $total = count($data['entregas']);
        $data['msg'] = msg("Entregas encontradas: {$total}", 'success');
        $data['title'] = 'Entregas';

        return view('entregas/index', $data);
    }

    public function getEnderecoPorPedido($pedidoId)
    {
        $pedido = $this->db->table('pedidos p')
            ->select('e.enderecos_id, e.enderecos_rua, e.enderecos_numero, e.enderecos_complemento')
            ->join('clientes c', 'c.clientes_id = p.clientes_id')
            ->join('enderecos e', 'e.enderecos_usuario_id = c.clientes_usuario_id')
            ->where('p.pedidos_id', $pedidoId)
            ->limit(1)
            ->get()
            ->getRow();

        if ($pedido) {
            return $this->response->setJSON([
                'success' => true,
                'endereco' => $pedido
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Endereço não encontrado para o pedido informado.'
            ]);
        }
    }

}