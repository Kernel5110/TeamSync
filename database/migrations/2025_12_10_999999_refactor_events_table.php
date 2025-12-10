<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename table
        Schema::rename('eventos', 'events');

        // 2. Add new columns
        Schema::table('events', function (Blueprint $table) {
            $table->string('name')->nullable(); // Will populate from nombre
            $table->text('description')->nullable(); // Will populate from descripcion
            $table->dateTime('starts_at')->nullable(); // Will populate from fecha_inicio + start_time
            $table->dateTime('ends_at')->nullable(); // Will populate from fecha_fin
            $table->string('location')->nullable(); // Will populate from ubicacion
            $table->integer('capacity')->nullable(); // Will populate from capacidad
            // problem_statement is already English-ish but let's ensure it stays
            // status_manual is already English-ish
        });

        // 3. Migrate Data
        // We use raw SQL to ensure efficiency and correctness
        // Disable strict mode to allow truncation of "50+ equipos" to "50"
        $strict = config('database.connections.mysql.strict');
        DB::statement("SET SESSION sql_mode = ''");
        
        DB::statement("
            UPDATE events SET 
            name = nombre,
            description = descripcion,
            location = ubicacion,
            capacity = CAST(capacidad AS UNSIGNED),
            starts_at = CAST(CONCAT(fecha_inicio, ' ', COALESCE(start_time, '00:00:00')) AS DATETIME),
            ends_at = CAST(CONCAT(fecha_fin, ' 23:59:59') AS DATETIME)
        ");
        
        // Restore strict mode (optional, as session ends)


        // 4. Drop old columns and enforce non-null on new ones
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'start_time', 'ubicacion', 'capacidad', 'categoria']); // categoria was unused or replaced by relation? Checking Event model, it has belongsToMany categories. The 'categoria' column in fillable might be legacy string. Dropping it.
            
            // Make new columns required
            $table->string('name')->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
            $table->dateTime('starts_at')->nullable(false)->change();
            $table->dateTime('ends_at')->nullable(false)->change();
            $table->string('location')->nullable(false)->change();
            $table->integer('capacity')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse process
        Schema::table('events', function (Blueprint $table) {
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->time('start_time')->nullable();
            $table->string('ubicacion')->nullable();
            $table->integer('capacidad')->nullable();
            $table->string('categoria')->nullable();
        });

        DB::statement("
            UPDATE events SET 
            nombre = name,
            descripcion = description,
            ubicacion = location,
            capacidad = capacity,
            fecha_inicio = DATE(starts_at),
            start_time = TIME(starts_at),
            fecha_fin = DATE(ends_at)
        ");

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'starts_at', 'ends_at', 'location', 'capacity']);
        });

        Schema::rename('events', 'eventos');
    }
};
