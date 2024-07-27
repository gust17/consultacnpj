<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CnpjResult extends Model
{
    use HasFactory;

    protected $table = 'cnpj_results';
    protected $fillable = [
        'nome_fantasia',
        'razao_social',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cep',
        'cidade',
        'estado',
        'email',
        'telefone',
        'cnpj',
        'inscricao_estadual',
        'abertura',
        'situacao',
        'tipo',
        'nome',
        'porte',
        'natureza_juridica',
        'atividade_principal',
        'atividades_secundarias',
        'data_situacao',
        'ultima_atualizacao',
        'status',
        'capital_social',
        'consulta_cep'
    ];

    protected $casts = [
        'atividade_principal' => 'array',
        'atividades_secundarias' => 'array',
    ];
}
