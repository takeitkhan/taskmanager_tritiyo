<?php

namespace Tritiyo\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBudget extends Model
{
    use HasFactory;

    protected $table = 'project_budgets';

    protected $fillable = [
        'id', 'project_id', 'current_range_id', 'budget_amount', 'site_id', 'is_active', 'created_at', 'updated_at'
    ];
}
