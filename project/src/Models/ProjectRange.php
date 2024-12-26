<?php

namespace Tritiyo\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRange extends Model
{
    use HasFactory;

    protected $table = 'project_ranges';

    protected $fillable = [
        'id', 'project_id', 'status_update_date', 'project_status', 'status_key', 'is_active', 'created_at', 'updated_at'
    ];
}
