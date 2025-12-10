<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename Tables
        // Schema::rename('eventos', 'events'); // Already done in refactor_events_table
        Schema::rename('participantes', 'participants');
        Schema::rename('equipos', 'teams');
        Schema::rename('carreras', 'careers');
        Schema::rename('instituciones', 'institutions');
        Schema::rename('solicitudes', 'requests');
        Schema::rename('categorias', 'categories');
        Schema::rename('evento_juez', 'event_judge');
        Schema::rename('categoria_evento', 'category_event');
        // Schema::rename('criterios', 'criteria'); // Already criteria

        // Rename Columns: events - SKIPPED (Handled in refactor_events_table)
        /*
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('nombre', 'name');
            $table->renameColumn('descripcion', 'description');
            $table->renameColumn('fecha_inicio', 'start_date');
            $table->renameColumn('fecha_fin', 'end_date');
            $table->renameColumn('ubicacion', 'location');
            $table->renameColumn('capacidad', 'capacity');
        });
        */

        // Rename Columns: participants
        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('usuario_id', 'user_id');
            $table->renameColumn('carrera_id', 'career_id');
            $table->renameColumn('num_control', 'control_number');
            $table->renameColumn('institucion', 'institution');
        });

        // Rename Columns: teams
        Schema::table('teams', function (Blueprint $table) {
            $table->renameColumn('nombre', 'name');
            $table->renameColumn('evento_id', 'event_id');
        });

        // Rename Columns: careers
        Schema::table('careers', function (Blueprint $table) {
            $table->renameColumn('nombre', 'name');
        });

        // Rename Columns: institutions
        Schema::table('institutions', function (Blueprint $table) {
            $table->renameColumn('nombre', 'name');
        });
        
        // Rename Columns: requests
        Schema::table('requests', function (Blueprint $table) {
             $table->renameColumn('equipo_id', 'team_id');
             // user_id is likely already user_id
        });
        
        // Rename Columns: category_event
        Schema::table('category_event', function (Blueprint $table) {
            $table->renameColumn('categoria_id', 'category_id');
            $table->renameColumn('evento_id', 'event_id');
        });

        // Rename Columns: evaluations
        Schema::table('evaluations', function (Blueprint $table) {
            $table->renameColumn('equipo_id', 'team_id');
            $table->renameColumn('evento_id', 'event_id');
        });

        // Rename Columns: criteria
        Schema::table('criteria', function (Blueprint $table) {
            $table->renameColumn('evento_id', 'event_id');
            // $table->renameColumn('nombre', 'name'); // Already name
            // $table->renameColumn('descripcion', 'description'); // Already description
        });
        
        // Rename Columns: categories
        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('nombre', 'name');
        });

        // Rename Columns: event_judge
        Schema::table('event_judge', function (Blueprint $table) {
            $table->renameColumn('evento_id', 'event_id');
            // user_id is likely already user_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse Rename Columns
        Schema::table('event_judge', function (Blueprint $table) {
            $table->renameColumn('event_id', 'evento_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('name', 'nombre');
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->renameColumn('name', 'nombre');
        });

        Schema::table('careers', function (Blueprint $table) {
            $table->renameColumn('name', 'nombre');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->renameColumn('name', 'nombre');
            $table->renameColumn('event_id', 'evento_id');
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->renameColumn('user_id', 'usuario_id');
            $table->renameColumn('career_id', 'carrera_id');
            $table->renameColumn('control_number', 'num_control');
            $table->renameColumn('institution', 'institucion');
        });

        /*
        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('name', 'nombre');
            $table->renameColumn('description', 'descripcion');
            $table->renameColumn('start_date', 'fecha_inicio');
            $table->renameColumn('end_date', 'fecha_fin');
            $table->renameColumn('location', 'ubicacion');
            $table->renameColumn('capacity', 'capacidad');
        });
        */

        // Reverse Rename Tables
        Schema::rename('event_judge', 'evento_juez');
        Schema::rename('categories', 'categorias');
        Schema::rename('requests', 'solicitudes');
        Schema::rename('institutions', 'instituciones');
        Schema::rename('careers', 'carreras');
        Schema::rename('teams', 'equipos');
        Schema::rename('participants', 'participantes');
        // Schema::rename('events', 'eventos');
    }
};
