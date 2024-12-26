<?php

namespace Tritiyo\Site\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteInvoice extends Model
{
    use HasFactory;

    protected $table = 'site_invoices';

    protected $fillable = [
        'user_id', 'site_id', 'project_id', 'range_id', 'range_status_key', 'invoice_no', 'invoice_amount', 'invoice_date', 'invoice_type', 'status_key', 'is_verified'
    ];
}


/**
 * ALTER TABLE `site_invoices` ADD `range_id` INT NULL AFTER `project_id`;
 * ALTER TABLE `site_invoices` ADD `status_key` VARCHAR(255) NULL AFTER `invoice_type`;
 */
