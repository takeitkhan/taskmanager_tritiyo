<?php


namespace Tritiyo\Project\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class MobileBillExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    //private $id;
    private $manager_id;
    //private $date;

    //public function __construct($id, $date, $project_id)
    public function __construct($date, $manager_id)
    {
        //$this->id = $id;
        $this->manager_id = $manager_id;
        $this->date = $date;
    }

    public function collection()
    {
        $dates = explode(' - ', $this->date);
        $start = $dates[0];
        $end = $dates[1];
		if(!empty($this->manager_id)){
        	$getData = \Tritiyo\Project\Models\MobileBill::where('manager_id', $this->manager_id)->whereBetween('received_date', [$start, $end])->get();//->whereBetween('task_for', [$start, $end])->get();
        }else {
        	$getData = \Tritiyo\Project\Models\MobileBill::whereBetween('received_date', [$start, $end])->get();//->whereBetween('task_for', [$start, $end])->get();
        }

        $v = [];
        foreach ($getData as $bill) {
            $manager= \App\Models\User::where('id', $bill->manager_id)->first()->name;
           	$project_name= \Tritiyo\Project\Models\Project::where('id', $bill->project_id)->first()->name;
            $mobile_number = $bill->mobile_number;
            $received_amount = $bill->received_amount;
            $received_date = Date::stringToExcel(date('d/m/Y', strtotime( $bill->received_date))) ?? NULL;
            //dd($invoice_type);

            $value = [];
            $v[] = [
                $value[] = $manager,
              	$value[] = $project_name,
                $value[] = $mobile_number,
                $value[] = $received_amount,
                $value[] = $received_date,
            ];
        }

        //dd($v);
        if (count($v) == count($getData)) {
            return collect([$v]);
        }
    }

    public function headings(): array
    {
        return [
            'Manager Name',
            'Project Name',
            'Mobile Number',
            'Recieved Amount',
            'Recieved Date'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
