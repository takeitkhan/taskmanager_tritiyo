<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Routelist;

class MaterialModuleSeeder extends Seeder
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
               'route_name'=>'Materials',
               'route_url'=>'materials.index',
               'route_hash'=>bcrypt('samraj77'),
               'route_description'=>'All Materials View',
               'route_note'=>'All Materials View',
               'route_grouping'=>'1',
               'to_role'=>'1',
               'show_menu'=>'1',
               'dashboard_menu'=>'1',
               'font_awesome'=>'fas fa-tools',
               'bulma_class_icon_bg'=>'is-danger',
               'is_active'=>'1'
            ]
        ];

        foreach ($routes as $key => $value) {
            Routelist::create($value);
        }
    }
}
