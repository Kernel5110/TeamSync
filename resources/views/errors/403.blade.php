@extends('layouts.app')

@section('title', 'Acceso Denegado - TeamSync')

@section('content')
<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh; text-align: center;">
    <div style="font-size: 120px; font-weight: 800; color: #ef4444; line-height: 1;">403</div>
    <h1 style="font-size: 2rem; color: #1f2937; margin-top: 20px; margin-bottom: 10px;">Acceso Denegado</h1>
    <p style="color: #6b7280; font-size: 1.1rem; max-width: 500px; margin-bottom: 30px;">
        Lo sentimos, no tienes permisos para acceder a esta página.
    </p>
    <a href="{{ route('index') }}" style="background-color: #4f46e5; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background-color 0.2s;">
        Volver al Inicio
    </a>
</div>
@endsection
