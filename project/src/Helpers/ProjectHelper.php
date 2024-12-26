<?php

namespace Tritiyo\Project\Helpers;

//use Tritiyo\Task\Models\Task;
use Tritiyo\Project\Models\ProjectRange;
use DB;

class ProjectHelper
{

    public static function all_ranges($project_id)
    {
        $all_ranges_by_project_id = DB::SELECT("SELECT GROUP_CONCAT(CONCAT_WS(' | ', id, project_id, status_update_date, project_status, status_key) SEPARATOR ', ') AS status_string
                        FROM `project_ranges` WHERE project_id =  $project_id GROUP BY status_key ORDER BY id DESC");

        return $all_ranges_by_project_id;
    }
  
  
  	public static function get_all_ranges_by_project_id($project_id)
    {
        $all_ranges_by_project_id = DB::SELECT("SELECT qq.id, qq.start_date, (SELECT status_update_date FROM `project_ranges` WHERE status_key = qq.status_key AND project_id = $project_id AND project_status = 'Inactive') AS end_date, qq.status_key  FROM (
                                                SELECT 
                                                  id,
                                                  status_update_date as start_date, 
                                                  status_key
                                                FROM `project_ranges` WHERE project_id =  $project_id GROUP BY status_key ORDER BY id DESC
                                            ) AS qq");

        return $all_ranges_by_project_id;
    }
  
  	public static function get_range_by_status_key($status_key, $status)
    {
        //$range = DB::SELECT("SELECT `status_update_date` FROM `project_ranges` WHERE status_key = '$status_key' AND project_status = '$status'");
      	$range = DB::table('project_ranges')
          				->where('status_key', $status_key)
          				->where('project_status', $status)->first()->status_update_date ?? NULL;
        return $range;
    }

    public static function current_range_budgets($project_id, $range_id = false)
    {

        if(!empty($range_id)) {
            $r_id = $range_id;
        } else {
            $r_id = self::current_range_id($project_id);
        }
        // total current project budget till today
        return $allCurrentBudgets = \Tritiyo\Project\Models\ProjectBudget::where('project_id', $project_id)
            ->where('current_range_id', $r_id)
            ->orderBy('id', 'desc')
            ->sum('budget_amount');
    }

    public static function current_range_used_budgets($project_id){
        $ranges = \Tritiyo\Project\Helpers\ProjectHelper::all_ranges($project_id);
      	//$current_range_id = self::current_range_id($project_id);
        $i = 0;
        foreach ($ranges as $range) {
            if($i == 0) {

                $exploded = explode(',', $range->status_string);
                //dump($exploded[0]);
                $range_datas0 = explode('|', $exploded[0]);
                if (count($exploded) > 1) {
                    $range_datas1 = explode('|', $exploded[1]);
                } else {
                    $today = explode('|', $exploded[0]);
                    $range_datas1 = [
                        '0' => $today[0],
                        '1' => $today[1],
                        '2' => date('Y-m-d'),
                        '3' => $today[3],
                        '4' => $today[4]
                    ];
                }

                $multiple_tasks = \Tritiyo\Task\Models\Task::where('project_id', $project_id)->whereBetween('task_for', [$range_datas0[2], $range_datas1[2]])->get();

                $total_requisition = [];
                foreach ($multiple_tasks as $task) {
                    #SELECT * FROM `tasks` WHERE project_id = 8
                    $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id);
                    $total_requisition[] = $rm->getTotal();
                }
                $mobileBill = \Tritiyo\Project\Models\MobileBill::where('project_id', $project_id)->where('range_id', $range_datas0[0])->get()->sum('received_amount');
                $budgetuse = array_sum($total_requisition);

                return $budgetuse + $mobileBill;
            }
        $i++;
        }

    }

    public static function all_range_budgets($project_id)
    {
        // total project budget till today
        return $allBudgets = \Tritiyo\Project\Models\ProjectBudget::where('project_id', $project_id)->orderBy('id', 'desc')->sum('budget_amount');
    }

    public static function used_range_budgets($project_id)
    {
        $multiple_tasks = \Tritiyo\Task\Models\Task::where('project_id', $project_id)->get();

        $total_requisition = [];
        foreach($multiple_tasks as $task) {
            #SELECT * FROM `tasks` WHERE project_id = 8
            $rm = new \Tritiyo\Task\Helpers\SiteHeadTotal('requisition_edited_by_accountant', $task->id);
            $total_requisition[] = $rm->getTotal();
        }

        return $usedBudget = array_sum($total_requisition);
        // total project budget till today
        //return $allBudgets = \Tritiyo\Project\Models\ProjectBudget::where('project_id', $project_id)->orderBy('id', 'desc')->sum('budget_amount');
    }


    public static function current_range_id($project_id)
    {
        // Current Range Latest ID by project id
        $exists = ProjectRange::where('project_id', $project_id)->orderBy('id', 'desc')->first()->id ?? 0;
        return $exists;
    }

    public static function current_range_id_for_import($project_id)
    {
        // Current Range Latest ID by project id
        $exists = ProjectRange::where('project_id', $project_id)->orderBy('id', 'desc')->first();
        return $exists;
    }

    public static function project_status($project_id)
    {
        return $exists = ProjectRange::where('project_id', $project_id)->orderBy('id', 'desc')->first()->project_status ?? null;
    }
  
  	public static function ttrbGetTotalByProject($column_name, $project_id, $current_range) {
        /*
      	if($current_range == true) {
          $range_id = DB::SELECT("SELECT id FROM `project_ranges` WHERE project_id = " . $project_id . " ORDER BY id DESC LIMIT 0,1");
          if(!empty($range_id[0]->id)) {
	          $total = DB::SELECT("SELECT SUM($column_name) AS ttrb_total FROM `ttrb` WHERE project_id = " . $project_id . " AND current_range_id = " . $range_id[0]->id );
          }
          return $total[0]->ttrb_total ?? 0;
        } else {
          $total = DB::SELECT("SELECT SUM($column_name) AS ttrb_total FROM `ttrb` WHERE project_id = " . $project_id );
          return $total[0]->ttrb_total ?? 0;
        }
        */
      	//return $current_range;
        $total = DB::SELECT("SELECT SUM($column_name) AS ttrb_total FROM `ttrb` WHERE project_id = " . $project_id . " AND current_range_id =  $current_range");
        return $total[0]->ttrb_total ?? 0;

    }
    
    
    
    
    //Project Lock percentage
    public static function projectLockPercentage(){
    		$getSettings = \App\Models\Setting::where('id', 5)->first();
      		$getValue = json_decode($getSettings->settings)->project_lock_percentage;
      		return (int) $getValue;
    }


    //Remaining Balance of Project Based in Current Range id
    public static function remainingBalance($project_id, $current_range_id){
        /** Current Range Budget **/
        $total_budget = DB::select("SELECT SUM(budget_amount) AS total_budget FROM `project_budgets` WHERE `project_id` = '". $project_id ."' AND `current_range_id` = '". $current_range_id ."' ");
        $current_range_total_budget = (float) $total_budget[0]->total_budget;


        /** Current Range Usage **/
        $total_usages_on_current_range = DB::select("SELECT SUM(reba_amount) AS reba_amount FROM `ttrb` WHERE  `project_id` = '". $project_id ."' AND `current_range_id` = '". $current_range_id ."' ");
        $range_usage = (float)$total_usages_on_current_range[0]->reba_amount;

        $total_mobile_bill_on_current_range = DB::select("SELECT SUM(received_amount) AS total_mobile_bill FROM `mobile_bill` WHERE `project_id` = '". $project_id ."' AND `range_id` = '". $current_range_id ."' ");
        $mobile_bill = (float)$total_mobile_bill_on_current_range[0]->total_mobile_bill;

        /** Actual Usage **/
        $actual_usage =  $current_range_total_budget - ($range_usage + $mobile_bill);
        return $actual_usage;
    }


}


/**
 *
 * 2021-05-01 + start
 * 2021-07-25 + end
 *
 */


/**
 * 2021-05-01 to 2021-07-25
 *
 * Update project ranges from project table
 *
 * INSERT INTO `project_ranges` (`project_id`, `status_update_date`, `project_status`, `is_active`) SELECT id, DATE_FORMAT(`created_at`, '%Y-%m-%d'), 'Active', 1 FROM projects
 *
 * Project Range Select Code
 * SELECT GROUP_CONCAT(CONCAT_WS('|', project_id, status_update_date, project_status, status_key) SEPARATOR ',') FROM `project_ranges` WHERE project_id = 9 GROUP BY status_key
 */
