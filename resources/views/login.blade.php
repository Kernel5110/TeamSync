@extends('layouts.app')

@section('title', 'Iniciar Sesión - TeamSync')



@section('content')
<div class="auth-container">
    <div class="auth-card">
        <h1 class="auth-title">Crear Cuenta</h1>
        <p class="auth-subtitle">Ingresa tus credenciales para acceder a TeamSync</p>

        <form action="{{ route('login.post') }}" method="POST" class="auth-form">
            @csrf <div class="input-group">
                <label for="email">Correo</label>
                <div class="input-wrapper">
                    <span class="input-icon left">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" color="#9ca3af"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                    </span>
                    <input type="email" id="email" name="email" placeholder="tu@email.com" required>
                </div>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <div class="input-wrapper">
                    <span class="input-icon left">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" color="#9ca3af"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <span class="input-icon right toggle-password">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" color="#9ca3af"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
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