<?php

namespace Tritiyo\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetProjectKpi extends Model
{
    use HasFactory;

    protected $table = 'target_project_kpi';

    protected $fillable = [
        'project_id',
        'manager',
        'target_range',
        'project_range',
        'target_range_date',
        'year',
        'meta_key',
        'meta_value',
        'status_key',
        'mark',
        'counting_type'
    ];
}
