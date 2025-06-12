<?php

namespace App\Models;

use CodeIgniter\Model;

class Entregas extends Model
{
    protected $table = 'entregas';
    protected $primaryKey = 'entregas_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false; 
    protected $protectFields = true;
    protected $allowedFields = ['pedido_id', 'endereco_id', 'funcionario_id', 'status_entrega'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true; 
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'pedido_id' => 'permit_empty|is_natural_no_zero',
        'endereco_id' => 'permit_empty|is_natural_no_zero',
        'funcionario_id' => 'permit_empty|is_natural_no_zero',
        'status_entrega' => 'permit_empty|in_list[A CAMINHO,ENTREGUE,CANCELADO]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}