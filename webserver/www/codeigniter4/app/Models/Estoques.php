<?php

namespace App\Models;

use CodeIgniter\Model;

class Estoques extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'estoques';
    protected $primaryKey = 'estoques_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['produto_id', 'quantidade'];

    protected $useTimestamps = true; 
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
}