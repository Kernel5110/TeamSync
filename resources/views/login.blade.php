@extends('layouts.app')

@section('title', 'Iniciar Sesión - TeamSync')

@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title">Crear Cuenta</h1>
        <p class="auth-subtitle">Ingresa tus credenciales para acceder a TeamSync</p>

        <form action="{{ route('login.store') }}" method="POST" class="auth-form">
            @csrf
            <div class="input-group">
                <label for="email">Correo</label>
                <div class="input-wrapper">
                    <span class="input-icon left">
                        <x-icon-outline name="email" />
                    </span>
                    <input type="email" id="email" name="email" placeholder="tu@email.com" required>
                </div>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <span class="input-icon left">
                        <x-icon-outline name="lock" />
                    </span>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <span class="input-icon right toggle-password">
                    </span>
                </div>
            </div>

            <div class="auth-links">
                <a href="#" class="forgot-password">Olvide mi contraseña</a>
                <a href="{{ route('register') }}" class="register-link">Registrarme</a>
            </div>

            <button type="submit" class="btn-submit">
                Iniciar
            </button>
        </form>
    </div>
</div>
@endsection