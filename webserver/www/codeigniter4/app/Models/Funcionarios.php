<?php

namespace App\Models;

use CodeIgniter\Model;

class Funcionarios extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'funcionarios';
    protected $primaryKey = 'funcionarios_id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'funcionarios_usuario_id',
        'funcionarios_cargo',
        'funcionarios_salario',
        'funcionarios_data_admissao',
        'funcionarios_observacoes'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $returnType = 'object';

    /**
     * Retorna funcionários com informações do usuário associado
     */
    public function getFuncionariosComUsuarios()
    {
        return $this->select('funcionarios.*, usuarios.usuarios_nome, usuarios.usuarios_sobrenome, usuarios.usuarios_cpf')
            ->join('usuarios', 'usuarios.usuarios_id = funcionarios.funcionarios_usuario_id')
            ->findAll();
    }
}