<?php

namespace App\Controllers;


class Funcionarios extends BaseController
{
    public function index(): string
    {
        return view('funcionario/index');
    }

}

?>