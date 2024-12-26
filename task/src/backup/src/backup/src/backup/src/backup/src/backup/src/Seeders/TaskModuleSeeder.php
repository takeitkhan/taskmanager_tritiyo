<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Routelist;

class TaskModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $routes = [
            [
               'route_name'=>'Tasks',
               'route_url'=>'tasks.index',
               'route_hash'=>bcrypt('samraj77'),
               'route_description'=>'All Tasks View',
               'route_note'=>'All Tasks View',
               'route_grouping'=>'1',
               'to_role'=>'1',
               'show_menu'=>'1',
               'dashboard_menu'=>'1',
               'font_awesome'=>'fas fa-tasks',
               'bulma_class_icon_bg'=>'is-info',
               'is_active'=>'1'
            ]
        ];

        foreach ($routes as $key => $value) {
            Routelist::create($value);
        }
    }
}
