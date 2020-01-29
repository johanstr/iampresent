<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([           // 0
            'group_name' => 'ONBEKEND',
            'daypart' => 0,
            'cohort' => 1920,
            'mentor' => ''
        ]);

        DB::table('groups')->insert([           // 1
            'group_name' => 'B-ITA4-1a',
            'daypart' => 9,
            'cohort' => 1920,
            'mentor' => 'IkBenMentor'
        ]);

        DB::table('groups')->insert([           // 2
            'group_name' => 'B-ITA4-1b',
            'daypart' => 9,
            'cohort' => 1920,
            'mentor' => 'IkBenMentor'
        ]);

        DB::table('groups')->insert([           // 3
            'group_name' => 'B-ITB4-1a',
            'daypart' => 9,
            'cohort' => 1920,
            'mentor' => 'IkBenMentor'
        ]);

        DB::table('groups')->insert([           // 4
            'group_name' => 'B-ITB4-1b',
            'daypart' => 9,
            'cohort' => 1920,
            'mentor' => 'IkBenMentor'
        ]);

        DB::table('groups')->insert([           // 5
            'group_name' => 'B-ITB4-1c',
            'daypart' => 9,
            'cohort' => 1920,
            'mentor' => 'IkBenMentor'
        ]);
    }
}
