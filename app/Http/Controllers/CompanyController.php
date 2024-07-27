<?php

namespace App\Http\Controllers;

use App\Imports\CnpjDataImport;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CompanyController extends Controller
{
    public function import(Request $request)
    {
        set_time_limit(600);  // Aumenta o limite de tempo para 5 minutos

        $request->validate([
            'datafile' => 'required|file|mimes:xls,xlsx'
        ]);

        // Ler os dados do arquivo Excel
        $data = Excel::toArray(new CnpjDataImport(), request()->file('datafile'));

        $results = [];
        // Agora $data contém os dados do Excel
        // Aqui você pode iterar sobre os dados e buscar informações adicionais de cada CNPJ
        foreach ($data[0] as $row) { // Certifique-se de ajustar o índice conforme necessário
            $cnpj = $row['cnpj']; // Ajuste a chave conforme seu arquivo
            $cnpj = ($this->removeSpecialCharacters($cnpj));
            $response = $this->getCnpjInfo($cnpj);

            // Adicionar a resposta ao array de resultados
            if ($response['success']) {
                $results[] = $response['data'];
            } else {
                $results[] = ['cnpj' => $cnpj, 'error' => 'Informações não disponíveis'];
            }
            dd($results);




            // Aqui você faria uma chamada a API ou outro serviço para buscar informações
            // Por exemplo: $info = $this->fetchCnpjInfo($cnpj);
            // Você pode adicionar essas informações ao array $row ou tratá-las como preferir
        }

        dd($results);
        return view('padrao.busca',compact('results'));

        dd($data);
        // Após processar todos os dados, você pode retorná-los como JSON ou outra resposta
        return response()->json($data);
    }


    // Você pode adicionar um método para buscar informações do CNPJ aqui
    public function getCnpjInfo($cnpj)
    {
        $client = new Client();
        try {
            $response = $client->request('GET', "https://publica.cnpj.ws/cnpj/$cnpj");
            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'data' => $data
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao buscar informações do CNPJ: ' . $e->getMessage()
            ];
        }
    }

    public function removeSpecialCharacters($string)
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}
