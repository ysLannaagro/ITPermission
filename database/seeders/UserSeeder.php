<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create([
            'email' => 'admin@admin.com',
            'username' => 'admin',
            'password' => 'password',
        ]);
        // UserType::create([
        //     'name' => 'WH',
        // ]);
    }
}
