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
        'requisition_submitted_by_manager',
        'requisition_edited_by_cfo',
        'requisition_approved_by_cfo',
        'requisition_edited_by_accountant',
        'requisition_approved_by_accountant',
        'bill_prepared_by_resource',
        'bill_submitted_by_resource',
        'bill_edited_by_manager',
        'bill_approved_by_manager',
        'bill_edited_by_cfo',
        'bill_approved_by_cfo',
        'bill_edited_by_accountant',
        'bill_approved_by_accountant'

    ];
}

