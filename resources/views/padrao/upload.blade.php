@extends('padrao.padrao')
@section('content')
    <div class="container mt-5">
        <!-- Card para o formulário de upload -->
        <div class="card">
            <div class="card-header">
                Upload de Arquivo
            </div>
            <div class="card-body">
                <!-- Formulário para enviar o arquivo -->
                <form action="{{url('upload')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="fileUpload" class="form-label">Escolha um arquivo</label>
                        <input type="file" class="form-control" id="fileUpload" name="datafile" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </div>
        </div>
    </div>

@endsection
