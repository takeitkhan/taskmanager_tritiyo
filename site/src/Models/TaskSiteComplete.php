<?php

namespace Tritiyo\Site\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSiteComplete extends Model
{
    use HasFactory;
    protected $table = 'task_site_complete';
    protected $fillable = [
        'user_id', 'task_id', 'site_id', 'task_for', 'status'
    ];
}
