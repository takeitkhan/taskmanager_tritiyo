<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskRequisitionBill extends Model
{
    use HasFactory;

    protected $table = 'tasks_requisition_bill';
    protected $fillable = [
        'task_id',
        'requisition_prepared_by_manager',
        'rpbm_amount',
        'requisition_submitted_by_manager',
        'requisition_edited_by_cfo',
        'rebc_amount',
        'requisition_approved_by_cfo',
        'requisition_edited_by_accountant',
        'reba_amount',
        'requisition_approve_amount_accountant',
        'requisition_approved_by_accountant',
        'bill_prepared_by_resource',
        'bpbr_amount',
        'bill_submitted_by_resource',
        'bill_edited_by_manager',
        'bebm_amount',
        'bill_approved_by_manager',
        'bill_edited_by_cfo',
        'bebc_amount',
        'bill_approved_by_cfo',
        'bill_edited_by_accountant',
        'beba_amount',
        'bill_approved_by_accountant'

    ];
}

