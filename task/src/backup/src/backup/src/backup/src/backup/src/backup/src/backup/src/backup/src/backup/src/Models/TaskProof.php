<?php

namespace Tritiyo\Task\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskProof extends Model
{
    use HasFactory;

    protected $table = 'tasks_proof';
    protected $fillable = [
        'task_id', 'proof_sent_by', 'resource_proof', 'vehicle_proof', 'material_proof', 'anonymous_proof', 'lat_proof', 'long_proof'
    ];
}
