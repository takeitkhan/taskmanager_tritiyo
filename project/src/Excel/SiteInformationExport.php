<?php


namespace Tritiyo\Project\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SiteInformationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    //private $id;
    private $project_id;
    private $range_id;
    //private $date;

    //public function __construct($id, $date, $project_id)
    public function __construct($project_id, $range_id)
    {
        $this->project_id = $project_id;
        $this->range_id = $range_id;
    }

    public function collection()
    {
        $projectId = $this->project_id;
        $rangeId = $this->range_id;
        //$sites = \Tritiyo\Site\Models\Site::where('sites.project_id', $projectId)->get();
        $sites = \Tritiyo\Task\Models\TaskSite::leftjoin('tasks', 'tasks.id', 'tasks_site.task_id')
                    ->leftjoin('sites', 'tasks_site.site_id', 'sites.id')
                    ->where('sites.project_id', $projectId)
                    ->where('tasks.current_range_id', $rangeId)
                    ->groupBy('sites.id')
                    ->get();
        //dd($sites);
        $v =[];
        foreach ($sites as $key => $site) {
            //$getTask = \Tritiyo\Task\Models\TaskSite::select('task_id')->where('site_id', $site->id)->get()->groupBy('task_id');
            $getTask = \Tritiyo\Task\Models\TaskSite::leftjoin('tasks', 'tasks.id', 'tasks_site.task_id')
                ->select('tasks_site.task_id')
                ->where('tasks_site.site_id', $site->id)
                ->where('tasks.current_range_id', $rangeId)
                ->groupBy('task_id')
                ->get();
            //dd($invoice_type);
            $reba = [];
            foreach($getTask as $data){
                $reba []= \Tritiyo\Task\Models\TaskRequisitionBill::select('reba_amount')->where('task_id', $data->task_id)->get()->sum('reba_amount');
            }

            $bpbr = [];
            foreach($getTask as $data){
                $bpbr []= \Tritiyo\Task\Models\TaskRequisitionBill::select('bpbr_amount')->where('task_id', $data->task_id)->get()->sum('bpbr_amount');
            }

            $beba = [];
            foreach($getTask as $data){
                $beba []= \Tritiyo\Task\Models\TaskRequisitionBill::select('beba_amount')->where('task_id', $data->task_id)->get()->sum('beba_amount');
            }


            $value = [];
            $v[] = [
                $value[] = $site->site_code,
                $value[] = $site->completion_status,
                $value[] = $getTask->count() ?? '0',
                $value[] = array_sum($reba) ?? 0,
                $value[] = array_sum($bpbr) ?? 0,
                $value[] = array_sum($beba) ?? 0,
            ];
        }


        //dd($v);
            return collect([$v]);
    }

    public function headings(): array
    {
        return [
            'Site Code',
            'Completion Status',
            'Task Count',
            'Approved Requisition',
            'Bill Submitted',
            'Approved Bill'
        ];
    }

    public function columnFormats(): array
    {
        return [
            //'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
