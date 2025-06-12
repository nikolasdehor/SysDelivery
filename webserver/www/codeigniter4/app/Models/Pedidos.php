<?php

namespace App\Models;

use CodeIgniter\Model;

class Pedidos extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'pedidos_id';

    protected $allowedFields = [
        'clientes_id',
        'data_pedido',
        'status',
        'observacoes',
        'total_pedido'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $returnType = 'object';
}