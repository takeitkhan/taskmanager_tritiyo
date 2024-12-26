<?php

namespace Tritiyo\Site\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'user_id', 'pm', 'location', 'site_code', 'material', 'site_head', 'budget', 'completion_status', 'completion_status', 'pending_note', 'activity_details', 'site_type', 'range_ids' , 'task_limit'
    ];
}
