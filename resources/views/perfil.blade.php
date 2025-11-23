@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - TeamSync</title>

    @vite(['resources/css/perfil.css'])

    {{-- Fuentes e Iconos --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <span class="material-icons">people</span> TeamSync
        </div>
        <div class="nav-links">
            <a href="#">Inicio</a>
            <a href="#">Eventos</a>
            <a href="#">Equipo</a>
            <a href="#" class="active">Perfil</a>
            <a href="#">Admin</a>
        </div>
        <div class="auth-buttons">
            <button>Iniciar Sesión</button>
        </div>
    </nav>

    <div class="container">
        <div class="profile-header">
            <div class="profile-avatar">AG</div>

            <div class="profile-info">
                <h1>Ana García</h1>
                <p>&lt;> Programador</p>
                <p>Estudiante de Ingeniería en Sistemas apasionada por el desarrollo web y la inteligencia artificial.</p>
                <div class="profile-badges">
                    <span class="profile-badge">React</span>
                    <span class="profile-badge">Python</span>
                    <span class="profile-badge">Machine Learning</span>
                    <span class="profile-badge">+2 más</span>
                </div>
            </div>

            <div class="profile-actions">
                <button><span class="material-icons">edit</span> Editar</button>
                <div class="settings-icon">
                    <span class="material-icons">settings</span>
                </div>
            </div>
        </div>

        <div class="profile-stats-nav">
            <a href="#" class="active">Resumen</a>
            <a href="#">Equipos</a>
            <a href="#">Logros</a>
            <a href="#">Actividad</a>
        </div>

        <div class="profile-cards">
            <div class="card active">
                <div class="number">5</div>
                <div class="label">Eventos Participados</div>
            </div>
            <div class="card">
                <div class="number">3</div>
                <div class="label">Equipos Formados</div>
            </div>
            <div class="card">
                <div class="number">2</div>
                <div class="label">Competencias Ganadas</div>
            </div>
            <div class="card">
                <div class="number">47</div>
                <div class="label">Contribuciones</div>
            </div>
        </div>

        <div class="profile-details-grid">

            <div class="details-card">
                <h3><span class="material-icons">person</span> Información Personal</h3>
                <div class="details-list">
                    <p><span class="material-icons">email</span> ana.garcia@estudiante.mx</p>
                    <p><span class="material-icons">date_range</span> Miembro desde Enero 2024</p>
                    <p><span class="material-icons">school</span> Instituto Tecnológico de Monterrey</p>
                </div>
            </div>

            <div class="details-card">
                <h3><span class="material-icons">bar_chart</span> Habilidades Técnicas</h3>
                <p>Tu progreso en diferentes tecnologías</p>

                <div class="progress-bar-container">
                    <div class="skill-name">
                        <span>React</span>
                        <span>85%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: 85%;"></div>
                    </div>
                </div>

                <div class="progress-bar-container">
                    <div class="skill-name">
                        <span>Python</span>
                        <span>75%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: 75%;"></div>
                    </div>
                </div>

                <div class="progress-bar-container">
                    <div class="skill-name">
                        <span>Machine Learning</span>
                        <span>65%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: 65%;"></div>
                    </div>
                </div>

                <div class="progress-bar-container">
                    <div class="skill-name">
                        <span>UI/UX</span>
                        <span>55%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: 55%;"></div>
                    </div>
                </div>

                <div class="progress-bar-container">
                    <div class="skill-name">
                        <span>Node.js</span>
                        <span>45%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: 45%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
@endsection
