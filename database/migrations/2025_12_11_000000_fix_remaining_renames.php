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
        // Rename Columns: participants
        if (Schema::hasColumn('participants', 'equipo_id')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->renameColumn('equipo_id', 'team_id');
            });
        }

        // Rename Columns: requests
        if (Schema::hasColumn('requests', 'equipo_id')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->renameColumn('equipo_id', 'team_id');
            });
        }

        // Rename Table: categoria_evento -> category_event
        if (Schema::hasTable('categoria_evento')) {
            Schema::rename('categoria_evento', 'category_event');
        }

        // Rename Columns: category_event
        if (Schema::hasTable('category_event')) {
            Schema::table('category_event', function (Blueprint $table) {
                if (Schema::hasColumn('category_event', 'categoria_id')) {
                    $table->renameColumn('categoria_id', 'category_id');
                }
                if (Schema::hasColumn('category_event', 'evento_id')) {
                    $table->renameColumn('evento_id', 'event_id');
                }
            });
        }

        // Rename Columns: criteria
        if (Schema::hasTable('criteria')) {
            Schema::table('criteria', function (Blueprint $table) {
                if (Schema::hasColumn('criteria', 'evento_id')) {
                    $table->renameColumn('evento_id', 'event_id');
                }
            });
        }

        // Rename Columns: evaluations
        if (Schema::hasTable('evaluations')) {
            Schema::table('evaluations', function (Blueprint $table) {
                if (Schema::hasColumn('evaluations', 'equipo_id')) {
                    $table->renameColumn('equipo_id', 'team_id');
                }
                if (Schema::hasColumn('evaluations', 'evento_id')) {
                    $table->renameColumn('evento_id', 'event_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse Rename Columns: evaluations
        if (Schema::hasTable('evaluations')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->renameColumn('team_id', 'equipo_id');
                $table->renameColumn('event_id', 'evento_id');
            });
        }

        // Reverse Rename Columns: criteria
        if (Schema::hasTable('criteria')) {
            Schema::table('criteria', function (Blueprint $table) {
                $table->renameColumn('event_id', 'evento_id');
            });
        }

        // Reverse Rename Columns: category_event
        if (Schema::hasTable('category_event')) {
            Schema::table('category_event', function (Blueprint $table) {
                $table->renameColumn('category_id', 'categoria_id');
                $table->renameColumn('event_id', 'evento_id');
            });
        }

        // Reverse Rename Table: category_event -> categoria_evento
        if (Schema::hasTable('category_event')) {
            Schema::rename('category_event', 'categoria_evento');
        }

        // Reverse Rename Columns: requests
        if (Schema::hasTable('requests')) {
            Schema::table('requests', function (Blueprint $table) {
                $table->renameColumn('team_id', 'equipo_id');
            });
        }

        // Reverse Rename Columns: participants
        if (Schema::hasTable('participants')) {
            Schema::table('participants', function (Blueprint $table) {
                $table->renameColumn('team_id', 'equipo_id');
            });
        }
    }
};
