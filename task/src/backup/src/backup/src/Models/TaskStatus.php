<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    use HasFactory;
    protected $table = 'tasks_status';
    protected $fillable = [
      'code', 'task_id', 'action_performed_by', 'performed_for', 'requisition_id', 'message'
    ];
}
