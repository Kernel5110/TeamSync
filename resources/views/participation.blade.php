@extends('layouts.app')



@section('content')
<div class="contenedor-participacion">
    <div class="tarjeta-participacion">
        <div class="header-participacion">
            <h1>{{ $evento->nombre }}</h1>
            <p>Participación en el evento</p>
        </div>
        
        <div class="contenido-participacion">
            <div class="seccion-problema">
                <h2>Descripción del Problema</h2>
                <div class="descripcion-problema">
                    @if($evento->problem_statement)
                        {!! nl2br(e($evento->problem_statement)) !!}
                    @else
                        <p style="font-style: italic; color: #9ca3af;">La descripción del problema aún no ha sido publicada.</p>
                    @endif
                </div>
            </div>

            <div class="seccion-upload">
                <h2>Subir Solución</h2>
                
                @if(session('success'))
                    <div class="alert-success" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-error" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                <form action="{{ route('participation.upload', $evento->id) }}" method="POST" enctype="multipart/form-data" class="form-upload">
                    @csrf
                    <div class="form-group">
                        <label for="submission">Seleccionar archivo (PDF, ZIP, DOCX - Max 10MB)</label>
                        <input type="file" name="submission" id="submission" class="input-file" required>
                    </div>
                    
                    <button type="submit" class="btn-subir">
                        Subir Archivo
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
