<?php

namespace Tritiyo\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'name', 'code', 'type', 'manager', 'customer', 'address', 'vendor',
        'supplier', 'location', 'office', 'start', 'end', 'budget', 'summary', 'budget_history', 'is_active', 'created_at', 'updated_at'
    ];

    public function userdata()
    {
        return $this->belongsTo('User')->select(array('id as userid', 'name as username'));
    }
}
