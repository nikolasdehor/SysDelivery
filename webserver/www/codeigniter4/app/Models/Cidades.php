<?php

namespace App\Models;

use CodeIgniter\Model;

class Cidades extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'cidades';
    protected $primaryKey       = 'cidades_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false; 
    protected $protectFields    = true;
    protected $allowedFields    = ['cidades_nome', 'cidades_uf'];

    protected $useTimestamps = true; 
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

}