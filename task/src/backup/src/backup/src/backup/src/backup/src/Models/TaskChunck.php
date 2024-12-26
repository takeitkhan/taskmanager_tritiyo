<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskChunck extends Model
{
    use HasFactory;

    protected $table = 'tasks_chunck';
    protected $fillable = [
        'task_id', 'manager_data'
    ];
}
