<?php


namespace Tritiyo\Task\Helpers;


use Tritiyo\Task\Models\TaskRequisitionBill;
use DB;

class SiteHeadTotal
{
	
    protected $column;
    protected $task_id;
    protected $sign;

    public function __construct($column, $task_id, $sign = false)
    {
        $this->column = $column;
        $this->task_id = $task_id;
        $this->sign = $sign;
        //dd($task_id);
    }


    public function getTotal()
    {
    	//dump($this->getTransportTotal());
        $in_total = $this->getRegularTotal()
            + $this->getVehicleTotal()
            + $this->getMaterialTotal()
            + $this->getTransportTotal()
            + $this->getPurchaseTotal();
        if($this->sign == true){
            return 'BDT  '.$in_total;
        } else {
            return $in_total;
        }
    }

    public function getVehicleTotal()
    {
        $vehicle_rent = DB::select("SELECT sum(vehicle_rent) AS vehicle_rent_total
                                    FROM tasks_requisition_bill, JSON_TABLE($this->column, '$.task_vehicle[*]'
                                    COLUMNS (vehicle_rent VARCHAR(255) PATH '$.vehicle_rent')) task_vehicle
                                    WHERE tasks_requisition_bill.task_id = " . $this->task_id);
        //dd($vehicle_rent);
        if(!empty($vehicle_rent)) {
            return $vehicle_rent[0]->vehicle_rent_total;
        } else {
            return false;
        }

    }

    public function getMaterialTotal()
    {

       $material_amount = DB::select("SELECT sum(material_amount) AS material_amount_total
                                   FROM tasks_requisition_bill, JSON_TABLE($this->column, '$.task_material[*]'
                                   COLUMNS (material_amount VARCHAR(255) PATH '$.material_amount')) task_material
                                   WHERE tasks_requisition_bill.task_id = " . $this->task_id);
       //dd($material_amount);
       if(!empty($material_amount)) {
            return $material_amount[0]->material_amount_total;
       } else {
           return false;
       }

    }


    public function getRegularTotal()
    {
        $ekta_var = $this->column;
        $regular_total_amount = DB::select("SELECT (da_amount + other_amount + labour_amount) AS regular_total_amount FROM (
                            SELECT
                                $ekta_var->>'$.task_regular_amount.da.da_amount' AS da_amount,
                                $ekta_var->>'$.task_regular_amount.other.other_amount' AS other_amount,
                                $ekta_var->>'$.task_regular_amount.labour.labour_amount' AS labour_amount
                            FROM tasks_requisition_bill WHERE task_id = $this->task_id
                        ) AS hhhmm");
        //dd($regular_total_amount[0]->regular_total_amount);
        //return $regular_total_amount[0]->regular_total_amount;
        if(!empty($regular_total_amount)) {
            return $regular_total_amount[0]->regular_total_amount;
        } else {
            return false;
        }
    }


    public function getTransportTotal()
    {
        $transport_total = DB::select("SELECT sum(ta_amount) AS transport_total
                                    FROM tasks_requisition_bill, JSON_TABLE($this->column, '$.task_transport_breakdown[*]'
                                    COLUMNS (ta_amount VARCHAR(255) PATH '$.ta_amount')) task_transport_breakdown
                                    WHERE tasks_requisition_bill.task_id = " . $this->task_id);

		//dump($transport_total);
        if(!empty($transport_total)) {
            return $transport_total[0]->transport_total;
       } else {
           return false;
       }
    }

    public function getPurchaseTotal()
    {
        $purchase_total = DB::select("SELECT sum(pa_amount) AS purchase_total
                                    FROM tasks_requisition_bill, JSON_TABLE($this->column, '$.task_purchase_breakdown[*]'
                                    COLUMNS (pa_amount VARCHAR(255) PATH '$.pa_amount')) task_purchase_breakdown
                                    WHERE tasks_requisition_bill.task_id = " . $this->task_id);

        if(!empty($purchase_total)) {
            return $purchase_total[0]->purchase_total;
       } else {
           return false;
       }
    }

  
  
  public function sqlGetTotal()
    {
        $column_name = $this->column;
        $task_id = $this->task_id;
        $total = DB::select("
            SELECT * FROM (
                SELECT
                    sum(material_amount) AS material_amount_total,
                    (
                        SELECT sum(vehicle_rent) AS vehicle_rent_total
                        FROM tasks_requisition_bill, JSON_TABLE($column_name, '$.task_vehicle[*]'
                        COLUMNS (vehicle_rent VARCHAR(255) PATH '$.vehicle_rent')) task_vehicle
                        WHERE tasks_requisition_bill.task_id = $task_id
                    ) AS vehicle_rent_total,
                    (
                        SELECT sum(pa_amount) AS purchase_total
                        FROM tasks_requisition_bill, JSON_TABLE($column_name, '$.task_purchase_breakdown[*]'
                        COLUMNS (pa_amount VARCHAR(255) PATH '$.pa_amount')) task_purchase_breakdown
                        WHERE tasks_requisition_bill.task_id = $task_id
                    ) AS task_purchase_breakdown,
                    (
                        SELECT sum(ta_amount) AS transport_total
                        FROM tasks_requisition_bill, JSON_TABLE($column_name, '$.task_transport_breakdown[*]'
                        COLUMNS (ta_amount VARCHAR(255) PATH '$.ta_amount')) task_transport_breakdown
                        WHERE tasks_requisition_bill.task_id = $task_id
                     ) AS task_transport_breakdown,
                     (
                         SELECT (da_amount + other_amount + labour_amount) AS regular_total_amount FROM (
                            SELECT
                            $column_name->>'$.task_regular_amount.da.da_amount' AS da_amount,
                            $column_name->>'$.task_regular_amount.other.other_amount' AS other_amount,
                            $column_name->>'$.task_regular_amount.labour.labour_amount' AS labour_amount
                            FROM tasks_requisition_bill WHERE task_id = $task_id
                        ) AS hhhmm
                     ) AS regular_total

                FROM tasks_requisition_bill,
                        JSON_TABLE($column_name, '$.task_material[*]' COLUMNS (material_amount VARCHAR(255) PATH '$.material_amount')) task_material
                WHERE tasks_requisition_bill.task_id = $task_id

            ) AS qwer
        ");
    	//return  $total;
    	dump($total);
    }
  
  
  
  public static  function  ttrbSiteHeadArchiveTransaction($column_name, $site_head, $start_date, $end_date){
    	$total =    DB::select("
        		SELECT SUM($column_name) AS ttrb_total FROM `ttrb` WHERE site_head = $site_head AND (task_for BETWEEN '$start_date' AND '$end_date')
        ");
            return $total[0]->ttrb_total ?? 0;
   // return 0;
  }
  
  public static  function  ttrbAmountPicker($column, $task_id, $sign = false) {    	
    	//dd($column);        
    	$total =    DB::select("SELECT $column AS amount FROM `ttrb` WHERE id = $task_id");
    	//dump($total);
    	if($sign = true) {
        	return 'BDT. ' . $total[0]->amount ?? 0;
        } else {
          	return $total[0]->amount ?? 0;
        }
  }
  
  
  public static function totalAmountRequisionBill($column, $task_id, $checkColumn = null){
                    if(!empty($checkColumn)){
                        $calculate = \Tritiyo\Task\Models\TaskRequisitionBill::where('task_id', $task_id)
                                    ->where($checkColumn, 'Yes')       
                                    ->first();
                    }else{
                        $calculate = \Tritiyo\Task\Models\TaskRequisitionBill::select('bpbr_amount', 'rpbm_amount', 'bebm_amount', 'rebc_amount', 'bebc_amount', 'reba_amount', 'beba_amount', )
                                        ->where('task_id', $task_id)->first();
                    }
  					return  $calculate->$column ?? 0;
  }
  
    /**
     * SELECT vehicle_rent->>'$.vehicle_rent', material_amount->>'$.material_amount' FROM (
     * SELECT
     * JSON_OBJECT('vehicle_rent', JSON_EXTRACT(`requisition_edited_by_accountant` , '$.task_vehicle[0].vehicle_rent') ) as vehicle_rent,
     * JSON_OBJECT('material_amount',JSON_EXTRACT(`requisition_edited_by_accountant`
     * , '$.task_material[0].material_amount')) as material_amount
     *
     * FROM `tasks_requisition_bill`
     * WHERE task_id = 1
     * ) AS total
     *
     *
     *  SELECT json_extract(requisition_prepared_by_manager, '$.task') as hits FROM tasks_requisition_bill WHERE id = 1
     *
     */

}
