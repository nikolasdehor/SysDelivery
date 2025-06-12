<?php

namespace App\Models;

use CodeIgniter\Model;

class Clientes extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'clientes';
    protected $primaryKey = 'clientes_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['clientes_usuario_id', 'clientes_observacoes'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $returnType = 'object';

    public function getClientesComUsuarios()
    {
        return $this->select('clientes.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, usuarios.usuarios_cpf')
            ->join('usuarios', 'usuarios.usuarios_id = clientes.clientes_usuario_id')
            ->findAll();
    }



}