<?php

namespace App\Models;

use CodeIgniter\Model;

class Vendas extends Model
{
    protected $table = 'vendas';
    protected $primaryKey = 'vendas_id';

    protected $allowedFields = [
        'pedidos_id',
        'data_venda',
        'forma_pagamento',
        'valor_total',
        'observacoes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $returnType = 'object';
}