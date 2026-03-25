<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeederTablaRoles extends Seeder
{
    public function run()
    {
        $admin      = Role::create(['name' => 'admin']);
        $instructor = Role::create(['name' => 'instructor']);
        $alumno     = Role::create(['name' => 'alumno']);

        // Admin tiene todos los permisos
          // Admin tiene todos los permis
        $admin->givePermissionTo(Permission::all());

        // Instructor solo puede ver blogs y roles
        $instructor->givePermissionTo(['ver-rol', 'ver-blog']);

        // Alumno solo puede ver blogs
        $alumno->givePermissionTo(['ver-blog']);
    }
}

        // Admin tiene todos los permisos
        // Admin tiene todos los permisos
        // Admin tiene todos los permisos
        // Admin tiene todos los permisos
        // Admin tiene todos los permisosv
        // Admin tiene todos los permisos
        // Admin tiene todos los permisosvv