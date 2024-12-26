<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskMaterial extends Model
{
    use HasFactory;

    protected $table = 'tasks_material';
    protected $fillable = [
        'task_id', 'material_id', 'material_qty', 'material_amount', 'material_note'
    ];
}
