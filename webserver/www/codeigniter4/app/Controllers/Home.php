<?php

namespace App\Controllers;
use App\Models\Produtos as Produtos_model;
use App\Models\Categorias as Categorias_model;
use App\Models\Imgprodutos as Imgprodutos_model;

class Home extends BaseController
{
    private $produtos;
    private $categorias;
    private $imagens;

    public function __construct(){
        $this->produtos = new Produtos_model();
        $this->categorias = new Categorias_model();
        $this->imagens = new Imgprodutos_model();
        helper('functions');
    }


    public function index()
    {   
        $data['titulo'] = "Home";
        $data['produtos'] = $this->produtos
            ->join('categorias', 'categorias.categorias_id = produtos.produtos_categorias_id')
            ->select('produtos.*, categorias.categorias_nome')
            ->findAll();
        $data['imgprodutos'] = $this->imagens
            ->join('produtos', 'produtos.produtos_id = imgprodutos_produtos_id')
            ->select('imgprodutos.*, produtos.*')
            ->findAll();
        $data['categorias'] = $this->categorias->findAll();
        return view('home/index',$data);
    }

}
