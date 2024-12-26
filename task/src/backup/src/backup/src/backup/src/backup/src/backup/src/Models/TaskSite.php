<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSite extends Model
{
    use HasFactory;

    protected $table = 'tasks_site';
    public $timestamps = true;
    protected $fillable = [
        'task_id', 'site_id', 'resource_id'
    ];
}
