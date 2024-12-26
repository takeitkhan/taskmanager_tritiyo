<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskVehicle extends Model
{
    use HasFactory;

    protected $table = 'tasks_vehicle';
    protected $fillable = [
        'task_id', 'vehicle_id', 'vehicle_rent', 'vehicle_note'
    ];
}
