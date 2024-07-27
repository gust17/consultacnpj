<?php

namespace App\Jobs;

use App\Models\CnpjResult;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use function Pest\Laravel\json;

class ProcessCnpjJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cnpj;
    private $retryCount = 3; // Número de tentativas de retry
    private $retryDelay = 60; // Delay entre tentativas em segundos

    /**
     * Create a new job instance.
     */
    public function __construct($cnpj)
    {
        $this->cnpj = $this->cleanCnpj($cnpj);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (Cache::has($this->cnpj)) {
            return;
        }

        $client = new Client();

        for ($attempt = 0; $attempt <= $this->retryCount; $attempt++) {
            try {
                $response = $client->get("https://publica.cnpj.ws/cnpj/{$this->cnpj}");

                if ($response->getStatusCode() == 200) {
                    $data = json_decode($response->getBody()->getContents(), true);

                    // Adicionar log para verificar os dados recebidos
                    Log::info('Dados recebidos da API:', ['data' => $data]);

                    // Verificar se as inscrições estaduais estão presentes
                    $inscricoesEstaduais = $data['estabelecimento']['inscricoes_estaduais'] ?? [];

                    // Log para verificar as inscrições estaduais brutas
                    Log::info('Inscrições Estaduais brutas:', ['inscricoes_estaduais' => $inscricoesEstaduais]);

                    // Formatar inscrições estaduais
                    $inscricoesEstaduaisFormatadas = array_map(function ($inscricao) {
                        return [
                            'inscricao_estadual' => $inscricao['inscricao_estadual'] ?? null,
                            'ativo' => $inscricao['ativo'] ?? null,
                            'estado_sigla' => $inscricao['estado']['sigla'] ?? null,
                            'atualizado_em' => $inscricao['atualizado_em'] ?? null,
                        ];
                    }, $inscricoesEstaduais);

                    // Buscar dados do CEP
                    $cep = $this->cleanCep($data['estabelecimento']['cep'] ?? null);
                    $buscaCEP = $client->get("https://viacep.com.br/ws/{$cep}/json/");

                    $dataCEP = null;
                    if ($buscaCEP->getStatusCode() == 200) {
                        $dataCEP = json_decode($buscaCEP->getBody()->getContents(), true);
                    }

                    // Log para verificar as inscrições estaduais formatadas
                    Log::info('Inscrições Estaduais formatadas:', ['inscricoes_estaduais' => $inscricoesEstaduaisFormatadas]);

                    // Salvar os dados no banco de dados
                    $result = CnpjResult::updateOrCreate(
                        ['cnpj' => $data['estabelecimento']['cnpj']],
                        [
                            'nome_fantasia' => $data['estabelecimento']['nome_fantasia'] ?? null,
                            'razao_social' => $data['razao_social'] ?? null,
                            'rua' => $data['estabelecimento']['logradouro'] ?? null,
                            'numero' => $data['estabelecimento']['numero'] ?? null,
                            'complemento' => $data['estabelecimento']['complemento'] ?? null,
                            'bairro' => $data['estabelecimento']['bairro'] ?? null,
                            'cep' => $cep,
                            'cidade' => $data['estabelecimento']['cidade']['nome'] ?? null,
                            'estado' => $data['estabelecimento']['estado']['sigla'] ?? null,
                            'email' => $data['estabelecimento']['email'] ?? null,
                            'telefone' => $this->cleanTelefone($data['estabelecimento']['telefone1'] ?? null),
                            'cnpj' => $data['estabelecimento']['cnpj'] ?? null,
                            'inscricao_estadual' => !empty($inscricoesEstaduaisFormatadas) ? json_encode($inscricoesEstaduaisFormatadas) : null,
                            'abertura' => $data['estabelecimento']['data_inicio_atividade'] ?? null,
                            'situacao' => $data['estabelecimento']['situacao_cadastral'] ?? null,
                            'tipo' => $data['estabelecimento']['tipo'] ?? null,
                            'nome' => $data['razao_social'] ?? null,
                            'porte' => $data['porte']['descricao'] ?? null,
                            'natureza_juridica' => $data['natureza_juridica']['descricao'] ?? null,
                            'atividade_principal' => isset($data['estabelecimento']['atividade_principal']) ? json_encode([$data['estabelecimento']['atividade_principal']]) : null,
                            'atividades_secundarias' => isset($data['estabelecimento']['atividades_secundarias']) ? json_encode($data['estabelecimento']['atividades_secundarias']) : null,
                            'data_situacao' => $data['estabelecimento']['data_situacao_cadastral'] ?? null,
                            'ultima_atualizacao' => $data['atualizado_em'] ?? null,
                            'status' => $data['estabelecimento']['situacao_cadastral'] ?? null,
                            'fantasia' => $data['estabelecimento']['nome_fantasia'] ?? null,
                            'capital_social' => $data['capital_social'] ?? null,
                            'efr' => null,  // Campo não encontrado na resposta da API
                            'motivo_situacao' => $data['motivo_situacao_cadastral'] ?? null,
                            'situacao_especial' => $data['estabelecimento']['situacao_especial'] ?? null,
                            'data_situacao_especial' => $data['estabelecimento']['data_situacao_especial'] ?? null,
                            'consulta_cep' => isset($dataCEP) ? json_encode($dataCEP) : null,
                        ]
                    );

                    // Log após salvar os dados no banco de dados
                    Log::info('Dados salvos no banco de dados para o CNPJ: ' . $this->cnpj, ['result' => $result]);

                    // Armazenar no cache por 1 hora
                    Cache::put($this->cnpj, $data, 3600);

                    return;
                }
            } catch (RequestException $e) {
                if ($e->getResponse() && $e->getResponse()->getStatusCode() == 429) {
                    Log::warning('Erro 429 Too Many Requests. Tentativa ' . ($attempt + 1) . ' de ' . $this->retryCount);
                    sleep($this->retryDelay); // Esperar antes de tentar novamente
                } else {
                    Log::error('Erro ao consultar CNPJ', ['cnpj' => $this->cnpj, 'message' => $e->getMessage()]);
                    break;
                }
            }
        }
    }


    private function cleanCnpj($cnpj)
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    private function cleanCep($cep)
    {
        return preg_replace('/[^0-9]/', '', $cep);
    }

    private function cleanTelefone($telefone)
    {
        return preg_replace('/[^0-9]/', '', $telefone);
    }
}
