<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCnpjJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CnpjBatchController extends Controller
{
    public function processExcel(Request $request)
    {
        // Verificar se o arquivo foi enviado
        if (!$request->hasFile('datafile')) {
            return response()->json(['error' => 'Nenhum arquivo enviado'], 400);
        }

        // Armazenar o arquivo temporariamente
        $path = $request->file('datafile')->store('temp');

        // Ler o conteúdo do arquivo
        $data = Excel::toArray([], storage_path('app/' . $path));

        $cnpjs = array_column($data[0], '1'); // Ajuste conforme o formato da planilha
//dd($cnpjs);
        foreach ($cnpjs as $index => $cnpj) {
            // Disparar um job a cada 20 segundos (3 por minuto)
            ProcessCnpjJob::dispatch($cnpj)->delay(now()->addSeconds($index * 30));
        }


        return redirect(url('welcome'));

        return response()->json(['message' => 'Processamento iniciado.']);
    }

    public function getProgress()
    {
        try {
            $totalCnpjs = DB::table('jobs')->count();
            $processedCnpjs = DB::table('cnpj_results')->count();
            $calc = $totalCnpjs + $processedCnpjs;


            $percentage = $calc != 0 ? ($processedCnpjs / $calc) * 100 : 0;

            return response()->json([
                'total' => $totalCnpjs,
                'processed' => $processedCnpjs,
                'percentage' => $percentage
               //    'results' => DB::table('cnpj_results')->get()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao conectar ao banco de dados: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao conectar ao banco de dados'], 500);
        }
    }
}
