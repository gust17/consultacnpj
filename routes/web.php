<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-db-connection', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        return 'Conexão ao banco de dados está funcionando!';
    } catch (\Exception $e) {
        return 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
    }
});


Route::get('/', function () {
    return view('padrao.upload');
})->name('upload');
Route::get('/welcome', function () {

    $results = \App\Models\CnpjResult::all();
    //dd($results);



    // Iterar sobre os resultados e decodificar o JSON
    foreach ($results as $result) {
        $result->inscricoes_estaduais = json_decode($result->inscricao_estadual, true);
        $result->buscaCEP = json_decode($result->consulta_cep, true);

        // Verifique se a decodificação JSON falhou
        if (json_last_error() !== JSON_ERROR_NONE) {
            $result->inscricoes_estaduais = [];
            $result->buscaCEP = [];
        }

    }

    //dd($results);
    return view('welcome',compact('results'));
})->name('consultar');

//Route::post('upload',[\App\Http\Controllers\CompanyController::class,'import'])->name('upload');



Route::post('/upload', [\App\Http\Controllers\CnpjBatchController::class, 'processExcel']);
Route::get('/progress', [\App\Http\Controllers\CnpjBatchController::class, 'getProgress']);
