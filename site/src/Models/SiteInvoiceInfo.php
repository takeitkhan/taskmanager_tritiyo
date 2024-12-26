<?php

namespace Tritiyo\Site\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteInvoiceInfo extends Model
{
    use HasFactory;

    protected $table = 'site_invoice_info';

    protected $fillable = [
         'action_performed_by', 'project_id', 'range_id', 'range_status_key', 'invoice_info_no', 'invoice_total_amount', 'invoice_date', 'invoice_powo', 'completion_status'
    ];
}


/**
 * ALTER TABLE `site_invoices` ADD `range_id` INT NULL AFTER `project_id`;
 * ALTER TABLE `site_invoices` ADD `status_key` VARCHAR(255) NULL AFTER `invoice_type`;
 */
