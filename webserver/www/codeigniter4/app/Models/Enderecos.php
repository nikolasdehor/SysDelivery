<?php

namespace App\Models;

use CodeIgniter\Model;

class Enderecos extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'enderecos';
    protected $primaryKey       = 'enderecos_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'enderecos_rua',
        'enderecos_numero',
        'enderecos_complemento',
        'enderecos_status',
        'enderecos_cidade_id',
        'enderecos_usuario_id'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
