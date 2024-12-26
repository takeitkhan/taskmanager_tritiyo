<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    protected $table = 'tasks';
    protected $fillable = [
        'user_id', 'task_name', 'task_code', 'task_type', 'project_id', 'site_head', 'task_details', 'anonymous_proof_details', 'task_assigned_to_head', 'task_for', 'is_active'
    ];
}
