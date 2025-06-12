<?php

namespace App\Models;

use CodeIgniter\Model;

class ItensPedido extends Model
{
    protected $table = 'itens_pedido';
    protected $primaryKey = 'itens_pedido_id';

    protected $allowedFields = [
        'pedidos_id',
        'produtos_id',
        'quantidade',
        'preco_unitario'
    ];
    
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $returnType = 'object';
}
