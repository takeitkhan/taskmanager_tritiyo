<?php
namespace Tritiyo\Project\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileBill extends Model
{
    use HasFactory;
    protected $table = "mobile_bill";
    protected  $fillable = [
        'manager_id', 'project_id', 'range_id', 'mobile_number', 'received_amount', 'received_date'
    ];
}

/**
 * ALTER TABLE `mobile_bill` ADD `mobile_number` VARCHAR(255) NULL AFTER `range_id`;
 */
