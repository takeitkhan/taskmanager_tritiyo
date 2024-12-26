<?php


namespace Tritiyo\Site\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
class ManagerSiteReport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
  	protected $manager_id;
  
  	public function __construct($manager_id){
    	$this->manager_id = $manager_id;
    }
  
    public function collection()
    {
        $sites = \Tritiyo\Site\Models\Site::leftjoin('projects', 'projects.id', 'sites.project_id')
          								->select('projects.name as project_name', 'sites.site_code', 'sites.id', 'sites.pm', 'sites.location', 'sites.completion_status', 'sites.task_limit')
          								->where('sites.pm', $this->manager_id)
          								->orderBy('id', 'desc')
          								->get();
        $v =[];
        foreach ($sites as $key => $site) {
            $getTotalTask = \Tritiyo\Task\Models\TaskSite::select('task_id')->where('site_id', $site->id)->groupBy('task_id')->get();
          	$taskCreated =  \Tritiyo\Task\Models\TaskSite::select('task_for')->where('site_id', $site->id)->orderBy('id', 'ASC')->groupBy('task_id')->first()->task_for ?? NULL;
            $reba = [];
            foreach( $getTotalTask as $data){
              	$totalSite = count(\Tritiyo\Task\Models\TaskSite::select('site_id')->where('task_id', $data->task_id)->groupBy('site_id')->get());
                $reba []= \Tritiyo\Task\Models\TaskRequisitionBill::select('reba_amount')->where('task_id', $data->task_id)->get()->sum('reba_amount')/$totalSite;
            }


            $value = [];
            $v[] = [
                $value[] = $site->project_name,
                $value[] = \App\Models\User::where('id', $site->pm)->first()->name ?? Null,
                $value[] = $site->site_code,
                $value[] = $site->location,
              	$value[] = $taskCreated,
             	 $value[] = $site->task_limit,
                $value[] = $site->completion_status,
                $value[] = $getTotalTask->count() ?? '0',
                $value[] = array_sum($reba) ?? 0,
            ];
        }


        //dd($v);
        return collect([$v]);
    }
    public function headings(): array
    {
        return [
          	'Project Name',
            'Project Manager',
            'Site Code',
            'Location',
          	'Task Created',
          	'Limit Task',
            'Completion Status',
            'Total Task',
            'Budget used',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
