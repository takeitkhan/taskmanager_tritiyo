<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDecline extends Model
{
    use HasFactory;
    protected $table = 'tasks_decline';
    protected $fillable = [
      'code', 'task_id', 'decline_reason'
    ];
}
