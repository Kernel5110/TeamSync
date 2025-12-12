<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $participanteRole = DB::table('roles')->where('name', 'participante')->first();
        $competidorRole = DB::table('roles')->where('name', 'competidor')->first();

        if ($participanteRole) {
            if (!$competidorRole) {
                // Si no existe competidor, simplemente renombramos
                DB::table('roles')
                    ->where('id', $participanteRole->id)
                    ->update(['name' => 'competidor']);
            } else {
                // Si ambos existen, movemos los usuarios de participante a competidor
                
                // Actualizar model_has_roles
                // Usamos UPDATE IGNORE o lógica similar para evitar duplicados si el usuario ya tiene ambos roles
                $participanteRelations = DB::table('model_has_roles')
                    ->where('role_id', $participanteRole->id)
                    ->get();

                foreach ($participanteRelations as $relation) {
                    $exists = DB::table('model_has_roles')
                        ->where('role_id', $competidorRole->id)
                        ->where('model_type', $relation->model_type)
                        ->where('model_id', $relation->model_id)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('model_has_roles')
                            ->where('role_id', $participanteRole->id)
                            ->where('model_type', $relation->model_type)
                            ->where('model_id', $relation->model_id)
                            ->update(['role_id' => $competidorRole->id]);
                    } else {
                        // Si ya tiene el rol competidor, simplemente borramos la relación vieja
                        DB::table('model_has_roles')
                            ->where('role_id', $participanteRole->id)
                            ->where('model_type', $relation->model_type)
                            ->where('model_id', $relation->model_id)
                            ->delete();
                    }
                }

                // Finalmente eliminamos el rol participante
                DB::table('roles')->where('id', $participanteRole->id)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir es complejo si fusionamos roles, pero podemos intentar renombrar 'competidor' a 'participante'
        // si eso era lo único que había.
        // Por simplicidad en este caso, no revertiremos la fusión de usuarios, 
        // solo el renombrado si es posible.
        
        $competidorRole = DB::table('roles')->where('name', 'competidor')->first();
        $participanteRole = DB::table('roles')->where('name', 'participante')->first();
        
        if ($competidorRole && !$participanteRole) {
             DB::table('roles')
                ->where('id', $competidorRole->id)
                ->update(['name' => 'participante']);
        }
    }
};
