<?php

namespace Tritiyo\Task\Excel;

use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

use \Tritiyo\Task\Excel\ArchiveResourceUsageSheet;
use \Tritiyo\Task\Excel\ArchiveResourceNonUsageSheet;

class ArchiveResourceExport implements WithMultipleSheets
{
    use Exportable;
    private $id;
    private $date;

    public function __construct($date)
    {
        $this->date = $date;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
            $sheets[] = new ArchiveResourceUsageSheet($this->date, 'Used Employee');
            $sheets[] = new ArchiveResourceNonUsageSheet($this->date, 'Non Used');
            //dd($sheets);
        return $sheets;
    }
}



?>
