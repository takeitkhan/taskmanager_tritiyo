<?php


namespace Tritiyo\Task\Helpers;

use Tritiyo\Task\Models\TaskRequisitionBill;

class RequisitionData
{

    protected $column;
    protected $task_id;

    public function __construct($column, $task_id)
    {
        $this->column = $column;
        $this->task_id = $task_id;
        //dd($task_id);
    }


    public function getSiteHead()
    {
        $v = TaskRequisitionBill::select($this->column)->where('task_id', $this->task_id)->first();
        if (!empty($v)) {
            $chunk = $v->toArray();
            if (!empty($chunk)) {
                $extracted = (object)json_decode($chunk[$this->column]);
                //dd($extracted->task[0]->site_head);
                return $extracted->task[0]->site_head;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getVehicleData()
    {
        $v = TaskRequisitionBill::select($this->column)->where('task_id', $this->task_id)->first();
        if (!empty($v)) {
            $chunk = $v->toArray();
            if ($chunk[$this->column] != null) {
                $extracted = (object)json_decode($chunk[$this->column]);
                 //dd($chunk);
                return $extracted->task_vehicle;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function getRegularData()
    {
        $v = TaskRequisitionBill::select($this->column)->where('task_id', $this->task_id)->first();
        if (!empty($v)) {
            $chunk = $v->toArray();
            if ($chunk[$this->column] != null) {
                $extracted = (object)json_decode($chunk[$this->column]);
                return (array)$extracted->task_regular_amount;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function getMaterialData()
    {
        $v = TaskRequisitionBill::select($this->column)->where('task_id', $this->task_id)->first();
        if (!empty($v)) {
            $chunk = $v->toArray();
            if ($chunk[$this->column] != null) {
                $extracted = (object)json_decode($chunk[$this->column]);
                return $extracted->task_material;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    public function getTransportData()
    {
        $v = TaskRequisitionBill::select($this->column)->where('task_id', $this->task_id)->first();
        if (!empty($v)) {
            $chunk = $v->toArray();
            //dd($chunk);
            if ($chunk[$this->column] != null) {
                $extracted = (object)json_decode($chunk[$this->column]);
                //dd($extracted);
                return $extracted->task_transport_breakdown;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function getPurchaseData()
    {
        $v = TaskRequisitionBill::select($this->column)->where('task_id', $this->task_id)->first();
        if (!empty($v)) {
            $chunk = $v->toArray();
            if ($chunk[$this->column] != null) {
                $extracted = (object)json_decode($chunk[$this->column]);
                return $extracted->task_purchase_breakdown;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


//array_sum(array_map(function($item) {
//    return $item['f_count'];
//}, $arr));
}
