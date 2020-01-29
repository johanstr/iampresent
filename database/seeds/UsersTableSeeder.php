<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'istrator',
            'abbr' => 'ADM',
            'email' => 'admin@admin.com',
            'password' => bcrypt('welkom'),
            'role' => 1
        ]);

        DB::table('users')->insert([
            'first_name' => 'Teacher',
            'last_name' => 'Alfa',
            'abbr' => 'TEA',
            'email' => 'teacher@alfa-college.nl',
            'password' => bcrypt('welkom'),
            'role' => 0
        ]);
    }
}
