<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Arquivo</title>
    <!-- Link para o CSS do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- Card para o formulário de upload -->
    <div class="card">
        <div class="card-header">
            Upload de Arquivo
        </div>
        <div class="card-body">
            <!-- Formulário para enviar o arquivo -->

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome/Nome Fantasia</th>
                        <th>Sobrenome/Razao Social</th>
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
                        <th>Inscricao Estadual</th>
                    </tr>
                    </thead>
                    <tbody>
                    {{--                @dd($results)--}}
                    @forelse($results as $result)
                        @php
                            $estabelecimento = $result['estabelecimento'] ?? [];
                        @endphp
                        <tr>
                            <td>{{$result->id}}</td>
                            <td>{{ $estabelecimento['nome_fantasia'] ?? 'N/A' }}</td>
                            <td>{{ $result['razao_social'] ?? 'N/A' }}</td>
                            <td>{{ $estabelecimento['tipo_logradouro'] ?? '' }} -- {{ $estabelecimento['logradouro'] ?? '' }}</td>
                            <td>{{ $estabelecimento['numero'] ?? 'N/A' }}</td>
                            <td>{{ $estabelecimento['complemento'] ?? 'N/A' }}</td>
                            <td>{{ $estabelecimento['bairro'] ?? 'N/A' }}</td>
                            <td>{{ $estabelecimento['cep'] ?? 'N/A' }}</td>
                            <td>{{ $estabelecimento['cidade']['nome'] ?? 'N/A' }}</td>
                            <td>{{ $estabelecimento['estado']['nome'] ?? 'N/A' }}</td>
                            <td>{{ $estabelecimento['email'] ?? 'N/A' }}</td>
                            <td>{{ $estabelecimento['ddd1'] ?? '' }}{{ $estabelecimento['telefone1'] ?? '' }}</td>
                            <td>{{ $estabelecimento['cnpj'] ?? 'N/A' }}</td>
                            <td>
                                @forelse ($estabelecimento['inscricoes_estaduais'] ?? [] as $estaduais)
                                    {{ $estaduais['inscricao_estadual'] ?? 'N/A' }} -- {{ $estaduais['estado']['nome'] ?? 'N/A' }}<br>
                                @empty
                                @endforelse
                            </td>
                        </tr>
                    @empty
                    @endforelse



                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript Bundle with Popper for Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
