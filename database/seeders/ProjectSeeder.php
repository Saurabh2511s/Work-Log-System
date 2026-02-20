<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = ['Project A','Project B','Internal','Support','R&D'];

        foreach ($projects as $p) {
            DB::table('projects')->updateOrInsert(
                ['name' => $p],
                ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}