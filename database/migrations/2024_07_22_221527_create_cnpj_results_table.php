<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cnpj_results', function (Blueprint $table) {
            $table->id();
            $table->string('nome_fantasia')->nullable();
            $table->string('razao_social')->nullable();
            $table->string('rua')->nullable();
            $table->string('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cep')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('cnpj')->unique();
            $table->json('inscricao_estadual')->nullable();
            $table->string('abertura')->nullable();
            $table->string('situacao')->nullable();
            $table->string('tipo')->nullable();
            $table->string('nome')->nullable();
            $table->string('porte')->nullable();
            $table->string('natureza_juridica')->nullable();
            $table->json('atividade_principal')->nullable();
            $table->json('atividades_secundarias')->nullable();
            $table->string('data_situacao')->nullable();
            $table->string('ultima_atualizacao')->nullable();
            $table->string('status')->nullable();
            $table->string('fantasia')->nullable();
            $table->string('capital_social')->nullable();
            $table->string('efr')->nullable();
            $table->string('motivo_situacao')->nullable();
            $table->string('situacao_especial')->nullable();
            $table->string('data_situacao_especial')->nullable();
            $table->json('consulta_cep')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cnpj_results');
    }
};
