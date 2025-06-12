<?php

namespace App\Controllers;
use App\Models\Usuarios as Usuarios_login;
use App\Models\Produtos as Produtos_login;
use App\Models\Imgprodutos as Imagem_produtos_login;

class Login extends BaseController
{
    private $data;
    private $usuarios;
    private $produtos;
    private $imagem_produtos;
    public $session;
    public function __construct(){
        helper(['functions', 'security']);
        $this->session = \Config\Services::session();
        $this->usuarios = new Usuarios_login();
        $this->produtos = new Produtos_login();
        $this->imagem_produtos = new Imagem_produtos_login();
        $this->data['title'] = 'Login';
        $this->data['msg'] = '';
    }
    public function index(): string
    { 
        return view('login',$this->data);
    }

    public function logar()
    {
        helper('security');

        $login = sanitize_input($_REQUEST['login'], 'string');
        $senha = $_REQUEST['senha'];

        // Rate limiting para tentativas de login
        $rateLimitKey = "login_attempts_" . $this->request->getIPAddress();
        if (!rate_limit_check($rateLimitKey, 5, 900)) { // 5 tentativas em 15 minutos
            $this->data['msg'] = msg('Muitas tentativas de login. Tente novamente em 15 minutos.', 'danger');
            log_security_event('LOGIN_RATE_LIMIT_EXCEEDED', ['ip' => $this->request->getIPAddress(), 'login' => $login]);
            return view('login', $this->data);
        }

        // Busca usuário por CPF ou email
        $this->data['usuarios'] = $this->usuarios->where('usuarios_cpf', $login)
            ->orWhere('usuarios_email', $login)->find();
        $this->data['produtos'] = $this->produtos->findAll();
        $this->data['imgprodutos'] = $this->imagem_produtos
        ->join('produtos', 'produtos.produtos_id = imgprodutos_produtos_id')
        ->select('imgprodutos.*, produtos.*')->find();

        if($this->data['usuarios'] == []){
            $this->data['msg'] = msg('O usuário ou a senha são inválidos!','danger');
            log_security_event('LOGIN_FAILED_USER_NOT_FOUND', ['login' => $login]);
            return view('login',$this->data);
        } else {
            $usuario = $this->data['usuarios'][0];

            // Verifica se a senha está no formato MD5 (migração gradual)
            $senhaValida = false;
            if (strlen($usuario->usuarios_senha) === 32 && ctype_xdigit($usuario->usuarios_senha)) {
                // Senha antiga em MD5
                $senhaValida = ($usuario->usuarios_senha === md5($senha));

                // Se login bem-sucedido, atualiza para hash seguro
                if ($senhaValida) {
                    $novoHash = hash_password_secure($senha);
                    $this->usuarios->update($usuario->usuarios_id, ['usuarios_senha' => $novoHash]);
                }
            } else {
                // Senha nova com hash seguro
                $senhaValida = verify_password_secure($senha, $usuario->usuarios_senha);
            }

            if(($usuario->usuarios_email == $login || $usuario->usuarios_cpf == $login) && $senhaValida){
                $infoSession = (object)[
                    'usuarios_id' => $usuario->usuarios_id,
                    'usuarios_nivel' => $usuario->usuarios_nivel,
                    'usuarios_nome' => $usuario->usuarios_nome,
                    'usuarios_sobrenome' => $usuario->usuarios_sobrenome,
                    'usuarios_cpf' => $usuario->usuarios_cpf,
                    'usuarios_email' => $usuario->usuarios_email,
                    'logged_in' => TRUE,
                    'login_time' => time()
                ];
                $this->session->set('login', $infoSession);

                // Log de login bem-sucedido
                log_security_event('LOGIN_SUCCESS', [
                    'user_id' => $usuario->usuarios_id,
                    'user_level' => $usuario->usuarios_nivel,
                    'login' => $login
                ]);

                if($usuario->usuarios_nivel == 0){
                    return view('user/index',$this->data);
                }
                elseif($usuario->usuarios_nivel == 1){
                    return view('funcionario/index',$this->data);
                }
                elseif($usuario->usuarios_nivel == 2){
                    return view('admin/index',$this->data);
                }else{
                    $this->data['msg'] = msg('Houve um problema com o seu acesso. Procure a Gerência de TI!','danger');
                    log_security_event('LOGIN_INVALID_LEVEL', ['user_id' => $usuario->usuarios_id, 'level' => $usuario->usuarios_nivel]);
                    return view('login',$this->data);
                }
            }else{
                $this->data['msg'] = msg('O usuário ou a senha são inválidos!','danger');
                log_security_event('LOGIN_FAILED_INVALID_CREDENTIALS', ['login' => $login]);
                return view('login',$this->data);
            }
        }
    }

    public function logout()
    {
        // $this->session->remove('login');
        $this->data['msg'] = msg('Usuário desconectado','success');
        // return redirect()->route('home');
        // //return redirect()->to('home');
        session()->destroy(); // Destrói todos os dados da sessão
        //return redirect('/'); // Redireciona para a página inicial
        return redirect()->to('/home')->with('msg', msg('Usuário desconectado','success'));
    }



}
