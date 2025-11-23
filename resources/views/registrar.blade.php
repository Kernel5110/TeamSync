@extends('layouts.app')

@push('styles')
    @vite(['resources/css/registrar.css'])
@endpush

@section('content')
    <div class="contenedor-registrar">
        <div class="tarjeta">
            <h1>Crear cuenta</h2>
            <p>Únete a TeanSync y forma parte de equipos increibles</p>
            <form>
                <div class="arriba">
                    <div>
                        <label for="nombre">Nombre</label>
                        <br>
                        <input type="text" placeholder="Nombre" name="nombre">
                    </div>
                    <div>
                        <label for="apellido">Apellido</label>
                        <br>
                        <input type="text" placeholder="Apellido" name="apellido">
                    </div>
                </div>
                <div class="medio">
                    <label for="institucion">Institución</label>
                    <select id="institucion">
                        <option value="ITO" selected>Instituto Tecnologico de Oaxaca</option>
                        <option value="ITD">Instituto Tecnologico de Durango</option>
                        <option value="ITA">Instituto Tecnologico de Aguascalientes</option>
                    </select>
                    <label for="carrera">Carrera</label>
                    <select id="carrera">
                        <option value="Ing. Sistemas" selected>Ing. Sistemas</option>
                        <option value="Ing. Civil">Ing. Civil</option>
                        <option value="Ing. Electrónica">Ing. Electrónica</option>
                        <option value="Ing. Electrica">Ing. Electrica</option>
                    </select>
                    <label for="correo">Correo</label>
                    <input type="email" placeholder="tu@email.com" name="correo">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" placeholder="••••••••" name="contraseña">
                </div>
                <div class="boton">
                    <button class="btn-registrar">Registrar</button>
                </div>
                <div class="abajo">
                    <span>¿Ya tienes cuenta?</span><a href="">Iniciar Sesión</a>
                </div>
            </form>
        </div>
    </div>
@endsection