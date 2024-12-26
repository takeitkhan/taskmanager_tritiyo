<?php


namespace Tritiyo\Task\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use DB;
class ManagerTaskReport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
  	protected $manager_id;
  
  	public function __construct($manager_id){
    	$this->manager_id = $manager_id;
    }
  
    public function collection()
    {
        $tasks = DB::select("
        	SELECT 
            tasks.id AS task_id, 
            (SELECT name FROM users WHERE id = tasks.user_id) AS manager,
            tasks.task_name AS task_name, projects.name AS project_name, 
            (SELECT name FROM users WHERE id = tasks.site_head) AS site_head, 
            (SELECT name FROM users WHERE id = tasks_site.resource_id) AS resource,
            tasks.task_for
            FROM `tasks` 

            LEFT JOIN tasks_site 
            ON tasks.id = tasks_site.task_id

            LEFT JOIN projects
            ON tasks.project_id = projects.id
            WHERE tasks.user_id = $this->manager_id
            GROUP BY task_id;");
        $v =[];
        foreach ($tasks as $key => $task) {
            $value = [];
            $v[] = [
                $value[] = $task->task_id,
                $value[] = $task->task_name,
				$value[] = $task->manager,
                $value[] = $task->project_name,
              	$value[] = $task->site_head,
             	 $value[] = $task->resource,
                $value[] = Date::stringToExcel(date('d/m/Y', strtotime($task->task_for))) ?? NULL,
            ];
        }


        //dd($v);
        return collect([$v]);
    }
    public function headings(): array
    {
        return [
          	'Task ID',
          	'Task Name',
          	'Project Manager',
          	'Project Name',
            'Site Head',
            'Resource',
          	'Task For',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
