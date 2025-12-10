<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean'],
            ['key' => 'site_title', 'value' => 'TeamSync', 'type' => 'string'],
            ['key' => 'welcome_message', 'value' => 'Bienvenido a la plataforma de gestión de eventos.', 'type' => 'string'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
