<?php

namespace App\Controllers;
use App\Models\Imgprodutos as Imgprodutos_model;
use App\Models\Produtos as Produtos_model;

use CodeIgniter\Files\File;

class Imgprodutos extends BaseController
{
    protected $helpers = ['form'];
    private $imgprodutos;
    private $produtos;
    public function __construct(){
        $this->imgprodutos = new Imgprodutos_model();
        $this->produtos = new Produtos_model();
        $data['title'] = 'Imgprodutos';
        helper('functions');
    }
    public function index(): string
    {
        $data['title'] = 'Imgprodutos';
        $data['imgprodutos'] = $this->imgprodutos->findAll();
        return view('Imgprodutos/index',$data);
    }

    public function new(): string
    {
        $data['title'] = 'Imgprodutos';
        $data['produtos'] = $this->produtos->findAll();
        $data['op'] = 'create';
        $data['form'] = 'cadastrar';
        $data['imgprodutos'] = (object) [
            'imgprodutos_link'=> '',
            'imgprodutos_descricao'=> '',
            'imgprodutos_produtos_id'=> '',
            'imgprodutos_id'=> ''
        ];
        return view('Imgprodutos/form',$data);
    }
    public function create()
    {
        // Verificar qual tipo de imagem foi escolhido
        $tipoImagem = $this->request->getPost('tipo_imagem');

        if ($tipoImagem === 'link') {
            // Validação para link de imagem
            $validationRule = [
                'imgprodutos_url' => [
                    'label' => 'URL da Imagem',
                    'rules' => 'required|valid_url',
                ],
                'imgprodutos_produtos_id' => [
                    'label' => 'Produto',
                    'rules' => 'required|integer',
                ],
            ];
        } else {
            // Validação para upload de arquivo
            $validationRule = [
                'imgprodutos_file' => [
                    'label' => 'Arquivo de Imagem',
                    'rules' => [
                        'uploaded[imgprodutos_file]',
                        'is_image[imgprodutos_file]',
                        'mime_in[imgprodutos_file,image/jpg,image/jpeg,image/gif,image/png,image/webp]',
                        'max_size[imgprodutos_file,10000]',
                        'max_dims[imgprodutos_file,4024,4024]',
                    ],
                ],
                'imgprodutos_produtos_id' => [
                    'label' => 'Produto',
                    'rules' => 'required|integer',
                ],
            ];
        }

        if (!$this->validate($validationRule)) {
            $data = [
                'errors' => $this->validator->getErrors(),
                'imgprodutos' => (object) [
                    'imgprodutos_id' => '',
                    'imgprodutos_link' => $tipoImagem === 'link' ? $this->request->getPost('imgprodutos_url') : ($_FILES['imgprodutos_file']['name'] ?? ''),
                    'imgprodutos_descricao' => $this->request->getPost('imgprodutos_descricao'),
                    'imgprodutos_produtos_id' => $this->request->getPost('imgprodutos_produtos_id'),
                ],
                'produtos' => $this->produtos->findAll(),
                'title' => 'Imgprodutos',
                'form' => 'Cadastrar',
                'op' => 'create',
                'validation' => $this->validator,
            ];
            return view('Imgprodutos/form', $data);
        }

        // Processar baseado no tipo de imagem
        if ($tipoImagem === 'link') {
            // Processar link de imagem
            $imageUrl = $this->request->getPost('imgprodutos_url');

            // Validar se a URL é uma imagem válida
            if (!$this->isValidImageUrl($imageUrl)) {
                $data = [
                    'errors' => ['imgprodutos_url' => 'A URL fornecida não é uma imagem válida ou não está acessível.'],
                    'imgprodutos' => (object) [
                        'imgprodutos_id' => '',
                        'imgprodutos_link' => $imageUrl,
                        'imgprodutos_descricao' => $this->request->getPost('imgprodutos_descricao'),
                        'imgprodutos_produtos_id' => $this->request->getPost('imgprodutos_produtos_id'),
                    ],
                    'produtos' => $this->produtos->findAll(),
                    'title' => 'Imgprodutos',
                    'form' => 'Cadastrar',
                    'op' => 'create',
                ];
                return view('Imgprodutos/form', $data);
            }

            $form = [
                'imgprodutos_link' => $imageUrl,
                'imgprodutos_descricao' => $this->request->getPost('imgprodutos_descricao'),
                'imgprodutos_produtos_id' => $this->request->getPost('imgprodutos_produtos_id'),
            ];

        } else {
            // Processar upload de arquivo
            $img = $this->request->getFile('imgprodutos_file');

            if (!$img->hasMoved()) {
                $imgpath = $img->store();
                $filepath = WRITEPATH . 'uploads/' . $imgpath;

                $subPasta = explode("/", $imgpath);
                $pastaO = WRITEPATH . 'uploads/' . $subPasta[0] . '/';
                $pastaD = FCPATH . 'assets/uploads/' . $subPasta[0] . '/';

                if (!is_dir($pastaD)) {
                    mkdir($pastaD, 0777, true);
                }

                if (copy($pastaO . $subPasta[1], $pastaD . $subPasta[1])) {
                    $data['msg'] = msg("Sucesso no Upload", "success");
                } else {
                    $data['msg'] = msg("Falha no Upload", "danger");
                }

                unlink($pastaO . $subPasta[1]);

                $form = [
                    'imgprodutos_link' => 'uploads/' . $imgpath,
                    'imgprodutos_descricao' => $this->request->getPost('imgprodutos_descricao'),
                    'imgprodutos_produtos_id' => $this->request->getPost('imgprodutos_produtos_id'),
                ];
            } else {
                return redirect()->to('/imgprodutos')->with('msg', msg('Erro no upload do arquivo', 'danger'));
            }
        }

        // Confere se o produto existe antes de salvar
        if (!$this->produtos->find($form['imgprodutos_produtos_id'])) {
            $data['msg'] = msg("Produto inválido!", "danger");
            $data['produtos'] = $this->produtos->findAll();
            $data['imgprodutos'] = (object)$form;
            $data['form'] = 'Cadastrar';
            $data['title'] = 'Imgprodutos';
            $data['op'] = 'create';
            return view('Imgprodutos/form', $data);
        }

        $this->imgprodutos->save($form);

        $successMsg = $tipoImagem === 'link' ? 'Link de imagem cadastrado com sucesso!' : 'Upload realizado com sucesso!';
        return redirect()->to('/imgprodutos')->with('msg', msg($successMsg, 'success'));
    }

    public function delete($id)
    {
        // busca imagem
        $imagem = $this->imgprodutos->where('imgprodutos_id', (int) $id)->first();

        if (!$imagem) {
            // Imagem não encontrada
            $data['msg'] = msg('Imagem não encontrada!','danger');
            $data['imgprodutos'] = $this->imgprodutos->findAll();
            $data['title'] = 'Imgprodutos';
            return view('Imgprodutos/index', $data);
        }

        // deleta do banco
        $this->imgprodutos->delete($id);

        // deleta do disco apenas se for arquivo local (não link externo)
        $isExternalLink = str_starts_with($imagem->imgprodutos_link, 'http://') ||
                         str_starts_with($imagem->imgprodutos_link, 'https://');

        if (!$isExternalLink) {
            $caminho = 'assets/' . $imagem->imgprodutos_link;
            if (file_exists($caminho)) {
                unlink($caminho);
            }
        }

        // sucesso
        $data['msg'] = msg('Deletado com Sucesso!','success');
        $data['imgprodutos'] = $this->imgprodutos->findAll();
        $data['title'] = 'Imgprodutos';
        return view('Imgprodutos/index', $data);
    }


    public function edit($id)
    {
        $data['imgprodutos'] = $this->imgprodutos->find(['imgprodutos_id' => (int) $id])[0];
        $data['produtos'] = $this->produtos->findAll();
        $data['title'] = 'Imgprodutos';
        $data['form'] = 'Alterar';
        $data['op'] = 'update';
        return view('Imgprodutos/form',$data);
    }

    public function update()
    {
        // Verificar qual tipo de imagem foi escolhido
        $tipoImagem = $this->request->getPost('tipo_imagem');
        $id = $this->request->getPost('imgprodutos_id');

        // Buscar a imagem atual
        $imagemAtual = $this->imgprodutos->find($id);

        if (!$imagemAtual) {
            return redirect()->to('/imgprodutos')->with('msg', msg('Imagem não encontrada!', 'danger'));
        }

        $dataForm = [
            'imgprodutos_descricao' => $this->request->getPost('imgprodutos_descricao'),
            'imgprodutos_produtos_id' => $this->request->getPost('imgprodutos_produtos_id')
        ];

        // Se o tipo de imagem foi alterado ou uma nova imagem foi fornecida
        if ($tipoImagem === 'link') {
            $newUrl = $this->request->getPost('imgprodutos_url');
            if (!empty($newUrl) && $newUrl !== $imagemAtual->imgprodutos_link) {
                // Validar a nova URL
                if (!$this->isValidImageUrl($newUrl)) {
                    return redirect()->to('/imgprodutos/edit/' . $id)->with('msg', msg('URL de imagem inválida!', 'danger'));
                }
                $dataForm['imgprodutos_link'] = $newUrl;

                // Se a imagem anterior era um arquivo local, deletá-lo
                $isOldLocal = !str_starts_with($imagemAtual->imgprodutos_link, 'http://') &&
                             !str_starts_with($imagemAtual->imgprodutos_link, 'https://');
                if ($isOldLocal) {
                    $oldPath = 'assets/' . $imagemAtual->imgprodutos_link;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            }
        } else {
            // Upload de novo arquivo (se fornecido)
            $newFile = $this->request->getFile('imgprodutos_file');
            if ($newFile && $newFile->isValid() && !$newFile->hasMoved()) {
                // Processar upload do novo arquivo
                $imgpath = $newFile->store();
                $subPasta = explode("/", $imgpath);
                $pastaO = WRITEPATH . 'uploads/' . $subPasta[0] . '/';
                $pastaD = FCPATH . 'assets/uploads/' . $subPasta[0] . '/';

                if (!is_dir($pastaD)) {
                    mkdir($pastaD, 0777, true);
                }

                if (copy($pastaO . $subPasta[1], $pastaD . $subPasta[1])) {
                    // Deletar arquivo antigo se era local
                    $isOldLocal = !str_starts_with($imagemAtual->imgprodutos_link, 'http://') &&
                                 !str_starts_with($imagemAtual->imgprodutos_link, 'https://');
                    if ($isOldLocal) {
                        $oldPath = 'assets/' . $imagemAtual->imgprodutos_link;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $dataForm['imgprodutos_link'] = 'uploads/' . $imgpath;
                }

                unlink($pastaO . $subPasta[1]);
            }
        }

        $this->imgprodutos->update($id, $dataForm);
        return redirect()->to('/imgprodutos')->with('msg', msg('Alterado com Sucesso!', 'success'));
    }

    public function search()
    {

        $data['imgprodutos'] = $this->imgprodutos->like('imgprodutos_id', $_REQUEST['pesquisar'])->find();
        $total = count($data['imgprodutos']);
        $data['msg'] = msg("Dados Encontrados: {$total}",'success');
        $data['title'] = 'Imgprodutos';
        return view('Imgprodutos/index',$data);

    }

    /**
     * Valida se a URL fornecida é uma imagem válida e acessível
     */
    private function isValidImageUrl($url)
    {
        // Verificar se a URL tem uma extensão de imagem válida
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $urlParts = parse_url($url);

        if (!$urlParts || !isset($urlParts['path'])) {
            return false;
        }

        $pathInfo = pathinfo($urlParts['path']);
        $extension = strtolower($pathInfo['extension'] ?? '');

        if (!in_array($extension, $validExtensions)) {
            return false;
        }

        // Verificar se a URL é acessível (opcional - pode ser removido se causar problemas)
        try {
            $headers = @get_headers($url, 1);
            if (!$headers || strpos($headers[0], '200') === false) {
                return false;
            }

            // Verificar se o content-type é de imagem
            $contentType = $headers['Content-Type'] ?? '';
            if (is_array($contentType)) {
                $contentType = $contentType[0];
            }

            return strpos($contentType, 'image/') === 0;
        } catch (Exception $e) {
            // Se não conseguir verificar, aceitar baseado na extensão
            return true;
        }
    }

}
