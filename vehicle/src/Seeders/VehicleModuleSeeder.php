<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Routelist;

class VehicleModuleSeeder extends Seeder
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
               'route_name'=>'Vehicles',
               'route_url'=>'vehicles.index',
               'route_hash'=>bcrypt('samraj77'),
               'route_description'=>'All Vehicles View',
               'route_note'=>'All Vehicles View',
               'route_grouping'=>'1',
               'to_role'=>'1',
               'show_menu'=>'1',
               'dashboard_menu'=>'1',
               'font_awesome'=>'fas fa-project-diagram',
               'bulma_class_icon_bg'=>'is-danger',
               'is_active'=>'1'
            ]
        ];

        foreach ($routes as $key => $value) {
            Routelist::create($value);
        }
    }
}
