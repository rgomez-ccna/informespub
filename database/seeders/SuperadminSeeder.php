<?php

namespace Database\Seeders;

use App\Models\Congregacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        

        User::updateOrCreate(
            ['email' => env('SUPERADMIN_EMAIL', 'informes@informes.ar')],
            [
                'congregacion_id' => null,
                'name' => 'Superadmin',
                'password' => Hash::make(env('SUPERADMIN_PASSWORD', '!Qwer1234$')),
                'role' => 'superadmin',
            ]
        );
    }
}