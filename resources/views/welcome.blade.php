@extends('padrao.padrao')

@section('content')

    <div class="container">
        <h1>CNPJ PROCESSADOS</h1>
        <div class="progress">
            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
        </div>
        <p>Enviados: <span id="total">0</span></p>
        <p>Processados: <span id="processed">0</span></p>
    </div>
    <div class="container">

        <div class="card">

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nome/Nome Fantasia</th>
                        <th>Sobrenome/Razão Social</th>
                        <th>Rua</th>
                        <th>N</th>
                        <th>Complemento</th>
                        <th>Bairro</th>
                        <th>CEP</th>
                        <th>Cidade</th>
                        <th>Estado</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>CNPJ</th>
                        <th>Inscrição Estadual</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{$result->id}}</td>
                            <td>{{ $result->nome_fantasia }}</td>
                            <td>{{ $result->razao_social }}</td>
                            <td>{{ $result->rua }}<br>
                                @if (is_array($result->buscaCEP))
                                    @if (isset($result['buscaCEP']['erro']))
                                        <div style="color: red">INCORRETO</div>
                                    @else
                                        <div
                                            style="{{ $result->rua != $result['buscaCEP']['logradouro'] ? 'color: red' : '' }}">
                                            {{ $result['buscaCEP']['logradouro'] }}
                                        </div>
                                    @endif
                                @endif

                            </td>
                            <td>{{ $result->numero }}</td>
                            <td>{{ $result->complemento }}</td>
                            <td>
                                {{ $result->bairro }} <br>
                                @if (is_array($result->buscaCEP))
                                    @if (isset($result['buscaCEP']['erro']))
                                        <div style="color: red">INCORRETO</div>
                                    @else
                                        <div
                                            style="{{ $result->bairro != strtoupper($result['buscaCEP']['bairro']) ? 'color: red' : '' }}">
                                            {{ !empty($result['buscaCEP']['bairro']) ? $result['buscaCEP']['bairro'] : 'Não informado' }}

                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>

                                {{ $result->cep }}
                                @if (is_array($result->buscaCEP))
                                    @if(isset($result['buscaCEP']['erro']))
                                        <div style="color: red">INCORRETO</div>
                                    @endif
                                @endif


                            </td>
                            <td>

                                {{ $result->cidade }} <br>

                                @if (is_array($result->buscaCEP))
                                    @if (isset($result['buscaCEP']['erro']))
                                        <div style="color: red">INCORRETO</div>
                                    @else
                                        <div
                                            style="{{ $result->cidade != $result['buscaCEP']['localidade'] ? 'color: red' : '' }}">
                                            {{ $result['buscaCEP']['localidade'] }}
                                        </div>
                                    @endif
                                @endif


                            </td>
                            <td>{{ $result->estado }}</td>
                            <td>{{ $result->email }}</td>
                            <td>{{ $result->telefone }}</td>
                            <td>{{$result->cnpj}}</td>
                            <td>
                                @if (is_array($result->inscricoes_estaduais))
                                    @foreach ($result->inscricoes_estaduais as $inscricao)
                                        <div>
                                            <strong>Inscrição
                                                Estadual:</strong> {{ $inscricao['inscricao_estadual'] }}<br>
                                            <strong>Ativo:</strong> {{ $inscricao['ativo'] ? 'Sim' : 'Não' }}<br>
                                            <strong>Estado:</strong> {{ $inscricao['estado_sigla'] }}<br>
                                            <strong>Atualizado em:</strong> {{ $inscricao['atualizado_em'] }}<br>
                                            <hr>
                                        </div>
                                    @endforeach
                                @else
                                    <div>Nenhuma inscrição estadual encontrada.</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13">Nenhum resultado encontrado.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>


    </div>

@endsection

@section('js')

    <script>
        function fetchProgress() {
            $.ajax({
                url: '/progress',
                method: 'GET',
                success: function(data) {
                    $('#total').text(data.total);
                    $('#processed').text(data.processed);
                    $('#progress-bar').css('width', data.percentage + '%');
                    $('#progress-bar').attr('aria-valuenow', data.percentage);
                    $('#progress-bar').text(data.percentage.toFixed(2) + '%');
                }
            });
        }

        $(document).ready(function() {
            fetchProgress();
            setInterval(fetchProgress, 20000); // 20000ms = 20 segundos
        });
    </script>

@endsection
