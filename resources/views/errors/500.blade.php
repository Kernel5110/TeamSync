@extends('layouts.app')

@section('title', 'Error del Servidor - TeamSync')

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh; text-align: center;">
    <div style="font-size: 120px; font-weight: 800; color: #f59e0b; line-height: 1;">500</div>
    <h1 style="font-size: 2rem; color: #1f2937; margin-top: 20px; margin-bottom: 10px;">Error del Servidor</h1>
    <p style="color: #6b7280; font-size: 1.1rem; max-width: 500px; margin-bottom: 30px;">
        Ocurrió un error inesperado en nuestros servidores. Por favor, inténtalo de nuevo más tarde.
    </p>
    <a href="{{ route('index') }}" style="background-color: #4f46e5; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background-color 0.2s;">
        Volver al Inicio
    </a>
</div>
@endsection
