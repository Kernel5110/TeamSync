@extends('layouts.app')

@section('title', 'Registrarse - TeamSync')



@section('content')
    <div class="contenedor-registrar">
        <div class="tarjeta">
            <h1>Crear cuenta</h1>
            <p>Únete a TeamSync y forma parte de equipos increíbles</p>
            
            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="arriba">
                    <div>
                        <label for="nombre">Nombre</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </span>
                            <input type="text" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios" maxlength="255" class="@error('nombre') is-invalid @enderror">
                        </div>
                        @error('nombre')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="apellido">Apellido</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </span>
                            <input type="text" id="apellido" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios" maxlength="255" class="@error('apellido') is-invalid @enderror">
                        </div>
                        @error('apellido')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="medio">
                    <div>
                        <label for="institucion">Institución</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M8 10h.01"></path><path d="M16 10h.01"></path><path d="M8 14h.01"></path><path d="M16 14h.01"></path></svg>
                            </span>
                            <select id="institucion" name="institucion" required class="@error('institucion') is-invalid @enderror">
                                <option value="" disabled selected>Selecciona tu institución</option>
                                @if(isset($instituciones))
                                    @foreach($instituciones as $inst)
                                        <option value="{{ $inst->name }}" {{ old('institucion') == $inst->name ? 'selected' : '' }}>{{ $inst->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        @error('institucion')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="carrera">Carrera</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                            </span>
                            <select id="carrera" name="carrera" required class="@error('carrera') is-invalid @enderror">
                                <option value="" disabled selected>Selecciona tu carrera</option>
                                @foreach($carreras as $carrera)
                                    <option value="{{ $carrera->id }}" {{ old('carrera') == $carrera->id ? 'selected' : '' }}>{{ $carrera->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('carrera')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="correo">Correo</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                            </span>
                            <input type="email" id="correo" placeholder="tu@email.com" name="correo" value="{{ old('correo') }}" required maxlength="255" class="@error('correo') is-invalid @enderror">
                        </div>
                        @error('correo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="contraseña">Contraseña</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input type="password" id="contraseña" placeholder="••••••••" name="contraseña" required minlength="8" class="@error('contraseña') is-invalid @enderror">
                        </div>
                        @error('contraseña')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="control_number">Número de Control (Opcional)</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </span>
                            <input type="text" id="control_number" name="control_number" placeholder="12345678" value="{{ old('control_number') }}" pattern="[0-9]{7,12}" title="Entre 7 y 12 dígitos numéricos" maxlength="12" class="@error('control_number') is-invalid @enderror">
                        </div>
                        @error('control_number')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn-registrar">Registrar</button>

                <div class="abajo">
                    <span>¿Ya tienes cuenta?</span>
                    <a href="{{ route('login') }}">Iniciar Sesión</a>
                </div>
            </form>
        </div>
    </div>
@endsection