<?php

namespace App\Controllers;
use App\Models\Usuarios as Usuarios_model;
use App\Models\Enderecos as Enderecos_model;

class Usuarios extends BaseController
{
    private $usuarios;
    private $enderecos;
    public function __construct(){
        $this->usuarios = new Usuarios_model();
        $this->enderecos = new Enderecos_model();
        $data['title'] = 'Usuarios';
        helper('functions');
    }
    public function index(): string
    {
        $data['title'] = 'Usuarios';
        $data['usuarios'] = $this->usuarios->findAll();
        return view('usuarios/index',$data);
    }

    public function new(): string
    {
        $data['title'] = 'Usuarios';
        $data['op'] = 'create';
        $data['form'] = 'cadastrar';
        $data['usuarios'] = (object) [
            'usuarios_nome'=> '',
            'usuarios_sobrenome'=> '',
            'usuarios_email'=> '',
            'usuarios_cpf'=> '',
            'usuarios_senha'=> '',
            'usuarios_fone'=> '',
            'usuarios_data_nasc'=> '',
            'usuarios_id'=> ''
        ];
        return view('usuarios/form',$data);
    }
    public function create()
    {

        // Checks whether the submitted data passed the validation rules.
        if(!$this->validate([
            'usuarios_nome' => 'required|max_length[255]|min_length[3]',
            'usuarios_sobrenome' => 'required',
            'usuarios_cpf' => 'required',
            'usuarios_email' => 'required',
            'usuarios_senha' => 'required',
            'usuarios_fone' => 'required',
            'usuarios_data_nasc' => 'required',
        ])) {
            
            // The validation fails, so returns the form.
            $data['usuarios'] = (object) [
                'usuarios_id' => '',
                'usuarios_nome' => $_REQUEST['usuarios_nome'],
                'usuarios_sobrenome' => $_REQUEST['usuarios_sobrenome'],
                'usuarios_email' => $_REQUEST['usuarios_email'],
                'usuarios_cpf' => moedaDolar($_REQUEST['usuarios_cpf']),
                'usuarios_data_nasc' => moedaDolar($_REQUEST['usuarios_data_nasc']),
                'usuarios_senha' => $_REQUEST['usuarios_senha'],
                'usuarios_fone' => $_REQUEST['usuarios_fone']
            ];
            
            $data['title'] = 'Usuarios';
            $data['form'] = 'Cadastrar';
            $data['op'] = 'create';
            return view('usuarios/form',$data);
        }


        helper('security');

        // Sanitiza e valida dados
        $dados = [
            'usuarios_nome' => sanitize_input($_REQUEST['usuarios_nome']),
            'usuarios_sobrenome' => sanitize_input($_REQUEST['usuarios_sobrenome']),
            'usuarios_email' => sanitize_input($_REQUEST['usuarios_email'], 'email'),
            'usuarios_cpf' => sanitize_input($_REQUEST['usuarios_cpf']),
            'usuarios_data_nasc' => $_REQUEST['usuarios_data_nasc'],
            'usuarios_fone' => sanitize_input($_REQUEST['usuarios_fone']),
            'usuarios_nivel' => 0
        ];

        // Valida CPF
        if (!validate_cpf($dados['usuarios_cpf'])) {
            $data['msg'] = msg('CPF inválido!', 'danger');
            $data['usuarios'] = $this->usuarios->findAll();
            $data['title'] = 'Usuarios';
            return view('usuarios/index', $data);
        }

        // Valida telefone
        if (!validate_phone($dados['usuarios_fone'])) {
            $data['msg'] = msg('Telefone inválido!', 'danger');
            $data['usuarios'] = $this->usuarios->findAll();
            $data['title'] = 'Usuarios';
            return view('usuarios/index', $data);
        }

        // Valida força da senha
        $senhaValidacao = validate_password_strength($_REQUEST['usuarios_senha']);
        if (!$senhaValidacao['valid']) {
            $errors = implode(', ', $senhaValidacao['errors']);
            $data['msg'] = msg('Senha fraca: ' . $errors, 'danger');
            $data['usuarios'] = $this->usuarios->findAll();
            $data['title'] = 'Usuarios';
            return view('usuarios/index', $data);
        }

        // Hash seguro da senha
        $dados['usuarios_senha'] = hash_password_secure($_REQUEST['usuarios_senha']);

        $this->usuarios->save($dados);
        
        $data['msg'] = msg('Cadastrado com Sucesso!','success');
        $data['usuarios'] = $this->usuarios->findAll();
        $data['title'] = 'Usuarios';
        return view('usuarios/index',$data);

    }

    public function delete($id)
    {
        $nivel = session()->get('login')->usuarios_nivel;
        if ($nivel == 2){
            $this->usuarios->where('usuarios_id', (int) $id)->delete();
            $data['msg'] = msg('Deletado com Sucesso!','success');
            $data['usuarios'] = $this->usuarios->findAll();
            $data['title'] = 'Usuarios';
            return view('usuarios/index',$data);
        }elseif ($nivel == 0 || $nivel == 1) {
            $this->usuarios->where('usuarios_id', (int) $id)->delete();
            session()->destroy();
            return redirect()->to(base_url('/'));
        }else{
            $data['msg'] = msg('Houve um problema com o seu acesso. Procure a Gerência de TI!','danger');
            session()->destroy();
            return view('/', $data);
        }
    }

    public function edit($id)
    {
        $nivel = session()->get('usuarios_nivel');
        if ($nivel == 2){
            $data['usuarios'] = $this->usuarios->find(['usuarios_id' => (int) $id])[0];
            $data['title'] = 'Usuários';
            $data['form'] = 'Editar';
            $data['op'] = 'update';
            return view('usuarios/form',$data);
        }elseif ($nivel == 0 || $nivel == 1) {
            $data['usuarios'] = $this->usuarios->find(['usuarios_id' => (int) $id])[0];
            $data['title'] = 'Meu Perfil';
            $data['form'] = 'Editar';
            $data['op'] = 'update';
            return view('usuarios/form',$data);
        }else{
            $data['msg'] = msg('Houve um problema com o seu acesso. Procure a Gerência de TI!','danger');
            return view('/home',$data);
        }
    }

    public function update()
    {
        $dataForm = [
            'usuarios_id' => $_REQUEST['usuarios_id'],
            'usuarios_nome' => $_REQUEST['usuarios_nome'],
            'usuarios_sobrenome' => $_REQUEST['usuarios_sobrenome'],
            'usuarios_email' => $_REQUEST['usuarios_email'],
            'usuarios_cpf' => $_REQUEST['usuarios_cpf'],
            'usuarios_data_nasc' => $_REQUEST['usuarios_data_nasc'],
            'usuarios_fone' => $_REQUEST['usuarios_fone'],
            'usuarios_nivel' => $_REQUEST['usuarios_nivel'] ?? 0,
        ];

        $this->usuarios->update($_REQUEST['usuarios_id'], $dataForm);
        if (session()->get('login')->usuarios_nivel == 2) {
            $data['msg'] = msg('Alterado com Sucesso!','success');
            $data['usuarios'] = $this->usuarios->find(['usuarios_id' => (int) $_REQUEST['usuarios_id']])[0];
            $data['title'] = 'Perfil';
            $data['form'] = 'Editar';
            $data['op'] = 'update';
            return view('usuarios/form', $data);
        }elseif (session()->get('login')->usuarios_nivel == 0 || session()->get('login')->usuarios_nivel == 1) {
            return redirect()->to(base_url('usuarios/perfil/' . $_REQUEST['usuarios_id']));
        } else {
            $data['msg'] = msg('Houve um problema com o seu acesso. Procure a Gerência de TI!', 'danger');
            session()->destroy();
            return view('/', $data);
        }
    }

    public function search()
    {
        //$data['usuarios'] = $this->usuarios->like('usuarios_nome', $_REQUEST['pesquisar'])->find();
        $data['usuarios'] = $this->usuarios->like('usuarios_nome', $_REQUEST['pesquisar'])->orlike('usuarios_cpf', $_REQUEST['pesquisar'])->find();
        $total = count($data['usuarios']);
        $data['msg'] = msg("Dados Encontrados: {$total}",'success');
        $data['title'] = 'Usuarios';
        return view('usuarios/index',$data);

    }

    public function edit_senha($usuarioId): string
    {
        $data['forms'] = (object) [
            'usuarios_senha_atual' => '',
            'usuarios_nova_senha' => '',
            'usuarios_confirmar_senha' => '',
            'usuarios_id' => $usuarioId
        ];

        $data['title'] = 'Usuarios';
        return view('usuarios/edit_senha', $data);
    }

    public function salvar_senha():string {
        $nivel = session()->get('login')->usuarios_nivel;

        // Checks whether the submitted data passed the validation rules.
        if(!$this->validate([
            'usuarios_senha_atual' => 'required',
            'usuarios_nova_senha' => 'required|max_length[14]|min_length[6]',
            'usuarios_confirmar_senha' => 'required|max_length[14]|min_length[6]'
        ])) {
            
            // The validation fails, so returns the form.
            $data['usuarios'] = (object) [
                'usuarios_senha_atual' => $_REQUEST['usuarios_senha_atual'],
                'usuarios_nova_senha' => $_REQUEST['usuarios_nova_senha'],
                'usuarios_confirmar_senha' => $_REQUEST['usuarios_confirmar_senha']
            ];
            $data['title'] = 'Usuarios';
            $data['msg'] = msg("Divergência de dados ou a senha deve ter no mínimo 6 digitos!","danger");
            return view('usuarios/edit_senha',$data);
        }
        $usuarioId = (int) $_REQUEST['usuarios_id'];

        $data['forms'] = (object)[
            'usuarios_senha_atual' => $_REQUEST['usuarios_senha_atual'],
            'usuarios_nova_senha' => $_REQUEST['usuarios_nova_senha'],
            'usuarios_confirmar_senha' => $_REQUEST['usuarios_confirmar_senha'],
            'usuarios_id' => $usuarioId
        ]; 
        $data['usuarios'] = $this->usuarios->find($usuarioId);
        if (!$data['usuarios']) {
            $data['msg'] = msg('Usuário não encontrado!', 'danger');
            $data['title'] = 'Usuarios';
            return view('usuarios/edit_senha', $data);
        }

        // Verifica senha atual (compatibilidade com MD5 e hash seguro)
        $senhaAtualValida = false;
        if (strlen($data['usuarios']->usuarios_senha) === 32 && ctype_xdigit($data['usuarios']->usuarios_senha)) {
            // Senha antiga em MD5
            $senhaAtualValida = (md5($_REQUEST['usuarios_senha_atual']) === $data['usuarios']->usuarios_senha);
        } else {
            // Senha nova com hash seguro
            $senhaAtualValida = verify_password_secure($_REQUEST['usuarios_senha_atual'], $data['usuarios']->usuarios_senha);
        }

        if($senhaAtualValida){
            if($_REQUEST['usuarios_nova_senha'] == $_REQUEST['usuarios_confirmar_senha']){

                // Valida força da nova senha
                $senhaValidacao = validate_password_strength($_REQUEST['usuarios_nova_senha']);
                if (!$senhaValidacao['valid']) {
                    $errors = implode(', ', $senhaValidacao['errors']);
                    $data['msg'] = msg('Nova senha fraca: ' . $errors, 'danger');
                    $data['title'] = 'Usuarios';
                    return view('usuarios/edit_senha', $data);
                }

                $dataForm = [
                    'usuarios_id' => $usuarioId,
                    'usuarios_senha' => hash_password_secure($_REQUEST['usuarios_nova_senha'])
                ];
                
                if($nivel == 2){
                    $this->usuarios->update($usuarioId, $dataForm);
                    $data['msg'] = msg('Senha alterada!','success');
                    $data['title'] = 'Escolher Usuário';
                    $data['usuarios'] = $this->usuarios->findAll();
                    return view('usuarios/acessoADM', $data);
                }elseif($nivel == 0){
                    $this->usuarios->update($usuarioId, $dataForm);
                    $data['msg'] = msg('Senha alterada!','success');
                    $data['usuarios'] = $this->usuarios->findAll();
                    $data['title'] = 'Login';
                    $data['msg'] .= msg('Faça o login novamente!','warning');
                    session()->destroy();
                    return view('/login', $data);
                }else{
                    $data['msg'] = msg('Houve um problema com o seu acesso. Procure a Gerência de TI!','danger');
                    session()->destroy();
                    return view('/', $data);
                }

            }else{
                $data['title'] = 'Usuarios';
                $data['msg'] = msg("As senhas não são iguais!","danger");
                return view('usuarios/edit_senha', $data);
            }
        }else{
            $data['title'] = 'Usuarios';
            $data['msg'] = msg("A senha atual é invalida","danger");
            return view('usuarios/edit_senha', $data);
        }
    }
    
    public function edit_nivel(): string
    {
        $data['nivel'] = [
            ['id' => 0, 'nivel' => "Cliente"],
            ['id' => 1, 'nivel' => "Funcionário"],
            ['id' => 2, 'nivel' => "Administrador"]
        ];

        $data['usuarios'] = $this->usuarios->findAll();
        $data['title'] = 'Usuarios';


        $data['usuarios'] = $this->usuarios->findAll();
        $data['title'] = 'Usuarios';
        return view('usuarios/edit_nivel',$data);
    }

    public function salvar_nivel(): string
    {

        $dataForm = [
            'usuarios_id' => $_REQUEST['usuarios_id'],
            'usuarios_nivel' => $_REQUEST['usuarios_nivel']
        ];

        $this->usuarios->update($_REQUEST['usuarios_id'], $dataForm);
        $data['msg'] = msg('Nivel alterada!','success');
        $data['usuarios'] = $this->usuarios->findAll();
        $data['title'] = 'Usuarios';
        return view('usuarios/index',$data);
    }

    public function perfil($usuarioId): string
    {
        $data['title'] = 'Meu Perfil';
        $data['msg'] = '';
        $data['usuario'] = $this->usuarios->find(['usuarios_id' => (int) $usuarioId])[0];
        
        if (!$data['usuario']) {
            $data['msg'] = msg('Usuário não encontrado!', 'danger');
            return view('usuarios/perfil', $data);
        }

        $data['enderecos'] = $this->enderecos->
        join('cidades', 'cidades.cidades_id = enderecos.enderecos_cidade_id')
        ->select('enderecos.*, cidades.*')->where('enderecos_usuario_id', (int) $usuarioId)->findAll();
        return view('usuarios/perfil', $data);
    }

    public function acess():string{
        $data['nivel'] = 'olá';
        $data['title'] = 'Escolher Usuário';
        $data['usuarios'] = $this->usuarios->findAll();

        return view('usuarios/acessoADM', $data);
    }

}