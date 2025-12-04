<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->string('github_repo')->nullable();
            $table->string('github_pages')->nullable();
            $table->string('project_name')->nullable();
            $table->text('technologies')->nullable();
            $table->text('project_description')->nullable();
            // submission_path can remain as optional or be removed later if strictly not needed
        });
    }

    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn(['github_repo', 'github_pages', 'project_name', 'technologies', 'project_description']);
        });
    }
};
