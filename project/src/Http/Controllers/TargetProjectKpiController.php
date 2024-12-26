<?php
namespace Tritiyo\Project\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Tritiyo\Project\Models\Project;
use Tritiyo\Project\Models\TargetProjectKpi;


class TargetProjectKpiController {
    public function index(){

    }

    public function create(){
        return view('project::target_project_kpi');
    }

    public function store(Request $request){
//        /dd($request->all());
        $bytes = random_bytes(16);
        $status_key = bin2hex($bytes);
        foreach($request->meta_key as $key => $data){
            $kpi = new TargetProjectKpi();
            $kpi->project_id = $request->project_id;
            $kpi->project_range = $request->current_range_id;
            $kpi->manager = $request->manager;
            $kpi->target_range = $request->target_range;
            $kpi->target_range_date = $request->target_start_end_date;
            $kpi->year = $request->year;
            $kpi->meta_key = $key;
            $kpi->meta_value = $data['target'];
            $kpi->status_key = $status_key;
            $kpi->mark = $data['mark'] ?? NULL;
            $kpi->counting_type = $data['type'] ?? NULL;
            $kpi->save();
        }
        return redirect()->back()->with(['status' => 1, 'message' => 'Added Successfully']);
    }

    public function edit(Request $request){
        $project_kpi = TargetProjectKpi::where('status_key', $request->status_key)
            ->orderBy('id', 'desc')
            ->groupBy('status_key')
            ->get();
        $editKpi = [];
        foreach($project_kpi as $key => $kpi){
            $single_kpi = \Tritiyo\Project\Models\TargetProjectKpi::where('project_id', $kpi->project_id)
                ->where('status_key', $kpi->status_key)
                ->get();
            $editKpi []=(object) [
                'status_key' => $kpi->status_key,
                'target_range' => $kpi->target_range,
                'target_range_date' => $kpi->target_range_date,
                'year' => $kpi->year,
                'target_project_costing' =>  $single_kpi[0]['meta_value'],
                'mark_project_costing' =>  $single_kpi[0]['mark'],
                'target_task_limit' =>  $single_kpi[1]['meta_value'],
                'mark_task_limit' =>  $single_kpi[1]['mark'],
                'target_site_completion' =>  $single_kpi[2]['meta_value'],
                'mark_site_completion' =>  $single_kpi[1]['mark'],
                'target_invoice_submission' =>  $single_kpi[3]['meta_value'],
                'mark_invoice_submission' =>  $single_kpi[3]['mark'],
                'bonus_85_89' =>  $single_kpi[4]['meta_value'],
                'bonus_90_94' =>  $single_kpi[5]['meta_value'],
                'bonus_95_100' =>  $single_kpi[6]['meta_value'],
            ];
        }
        //dd($editKpi[0]->target_range_date);
        return view('project::target_project_kpi', compact('editKpi'));
    }

    public function update(Request $request){

        //dd($request->all());
        foreach($request->meta_key as $key => $data){
            $kpiGet = TargetProjectKpi::where('status_key', $request['status_key'])->where('meta_key', $key)->first();
            $kpi = TargetProjectKpi::find($kpiGet->id);
            $kpi->target_range = $request->target_range;
            $kpi->target_range_date = $request->target_start_end_date;
            //$kpi->meta_key = $key;
            $kpi->meta_value = $data['target'];
            $kpi->status_key = $request->status_key;
            $kpi->mark = $data['mark'] ?? NULL;
            $kpi->counting_type = $data['type'] ?? NULL;
            $kpi->save();
            //dump($kpi);
        }
        return redirect()->route('target.projects.kpi.create', ['project_id' => $request->project_id])->with(['status' => 1, 'message' => 'Edited Successfully']);
    }

    public function destroy(Request $request){
        $data = TargetProjectKpi::where('status_key', $request['status_key']);
        $data->delete();
        return redirect()->back()->with(['status' => 0, 'message' => 'Delected Successfully']);
    }


    public function viewTargetAllProjectCosting(){
        return view('project::view_all_target_poject_costing');
    }
}
