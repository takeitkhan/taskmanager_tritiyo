<?php


namespace Tritiyo\Project\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SiteExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    //private $id;
    private $project_id;
    private $key;

    //public function __construct($id, $date, $project_id)
    public function __construct($project_id, $options = [])
    {
      	$default = [
        	'key' => null,
        ];
      	$merge = array_merge($default, $options);
      	$this->key = $merge['key'];
        $this->project_id = $project_id;
    }

    public function collection()
    {
        $projectId = $this->project_id;
        //$sites = \Tritiyo\Site\Models\Site::where('sites.project_id', $projectId)->get();
      	$project = \Tritiyo\Project\Models\Project::where('id', $projectId)->first();
      	if(!empty($this->key)){
          	$key = $this->key;
          	$sites = \Tritiyo\Site\Models\Site::where('project_id', $projectId ?? NULL)
                ->where(function($group) use ($key)  {
                  	$group->orWhere('location', 'LIKE', '%' . $key . '%');
                    $group->orWhere('site_code', 'LIKE', '%' . $key . '%');
                    $group->orWhere('material', 'LIKE', '%' . $key . '%');
                    $group->orWhere('site_head', 'LIKE', '%' . $key . '%');
                    $group->orWhere('completion_status', 'LIKE', '%' . $key . '%');
                })->get();
        }else{
        	$sites = \Tritiyo\Site\Models\Site::where('project_id', $projectId)->get();
        }
        //dd($sites);
        $v =[];
        foreach ($sites as $key => $site) {

            $value = [];
            $v[] = [
                $value[] = $site->site_code,
              	$value[] = $site->location,
                $value[] = $site->completion_status,
              	$value[] = $project->name,
            ];
        }


        //dd($v);
            return collect([$v]);
    }

    public function headings(): array
    {
        return [
            'Site Code',
          	'Location',
            'Completion Status',
            'Project Name',
        ];
    }

    public function columnFormats(): array
    {
        return [
            //'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
