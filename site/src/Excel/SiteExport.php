<?php


namespace Tritiyo\Site\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiteExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    public function collection()
    {
        $sites = \Tritiyo\Site\Models\Site::orderBy('id', 'desc')->get();
        //dd($sites);
        $v =[];
        foreach ($sites as $key => $site) {
            $getTask = \Tritiyo\Task\Models\TaskSite::select('task_id')->where('site_id', $site->id)->groupBy('task_id')->get();
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
                $value[] = $site->location,
                $value[] = $site->completion_status,
                $value[] = \Tritiyo\Project\Models\Project::where('id', $site->project_id)->first()->name ?? Null,
                $value[] = \App\Models\User::where('id', $site->pm)->first()->name ?? Null,
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
            'Location',
            'Completion Status',
            'Project',
            'Project Manager',
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
