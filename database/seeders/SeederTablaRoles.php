<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeederTablaRoles extends Seeder
{
    public function run()
    {
        $admin        = Role::create(['name' => 'admin']);
        $coordinador  = Role::create(['name' => 'coordinador']);
        $instructor   = Role::create(['name' => 'instructor']);
        $alumno       = Role::create(['name' => 'alumno']);

        // Admin tiene todos los permisos
        $admin->givePermissionTo(Permission::all());

        // Coordinador puede ver roles y blogs
        $coordinador->givePermissionTo(['ver-rol', 'ver-blog']);

        // Instructor solo puede ver blogs y roles
        $instructor->givePermissionTo(['ver-rol', 'ver-blog']);

        // Alumno solo puede ver blogs
        $alumno->givePermissionTo(['ver-blog']);
    }
}
