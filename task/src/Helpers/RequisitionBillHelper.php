<?php


namespace Tritiyo\Task\Helpers;


use Tritiyo\Task\Models\TaskRequisitionBill;

class RequisitionBillHelper
{


    public static function requisitionBillActionHelper(array $options = array())
    {
        $default = [
            'approve_code' => null,
            'decline_code' => null,
            'task_id' => null,
            'action_performed_by' => null,
            'performed_for' => null,
            'requisition_id' => null,
            'message' => null,
            'buttonValue' => null,
            'showOrNot' => false
        ];

        $new = (object)array_merge($default, $options);

        //$approve_key, $decline_key = false, $showOrNot = false
        $check = TaskRequisitionBill::select($new->approve_code)->where('id', $new->requisition_id)->where('task_id', $new->task_id)->first();

        if (isset($check) && ($check[$new->approve_code] == 'Yes' || $check[$new->approve_code] == 'No') || $new->requisition_id == null) {
            // This Js used all form input
            $html = '<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>';
            $html .= "<script>";
            $html .= "$('form#requisition_form input').attr('disabled', true);";
            $html .= "$('form#requisition_form button').addClass('is-hidden');";
            $html .= "$('form#add_route button').addClass('is-hidden');";
            $html .= "$('form#add_route input').attr('disabled', true);";
            $html .= "$('form#add_route textarea').attr('disabled', true);";
            $html .= "</script>";
        } else {
            $html = '<form action="' . route('taskstatus.store') . '" method="post">';
            $html .= csrf_field();
            $html .= '<input type="hidden" name="task_id" value="' . $new->task_id . '"/>';
            $html .= '<input type="hidden" name="requisition_id" value="' . (!empty($new->requisition_id) ? $new->requisition_id : null) . '"/>';
            $html .= '<input type="hidden" name="action_performed_by" value="' . (!empty($new->action_performed_by) ? $new->action_performed_by : null) . '"/>';
			

            // Approve/Accept/Submit to
            $html .= '<input type="hidden" name="accept[approve_code]" value="' . $new->approve_code . '"/>';
            $html .= '<input type="hidden" name="accept[approve]" value="Approve" class="button is-success"/>';
            $html .= '<input type="hidden" name="accept[status]" value="Yes" class="button is-success"/>';
            $html .= '<input type="submit" name="accept[submit]" value="' . $new->buttonValue . '" class="button is-success"/>';

            // Decline
            if ($new->showOrNot == true) {
                $html .= '<input type="hidden" name="decline[decline_code]" value="' . $new->decline_code . '"/>';
                $html .= '<input type="hidden" name="decline[decline]" value="Decline"/>';
                $html .= '<input type="hidden" name="decline[status]" value="No"/>';
                $html .= '<input id="declineBtn" type="submit" name="decline[submit]" value="Decline" class="button is-danger"/>';
              
                  if(auth()->user()->isCFO(auth()->user()->id)) {
                         $html .= '<div id="declineReasonField"></div>';
                        $html .= '<script>$(\'#declineBtn\').click(function(e){e.preventDefault();  let textArea = "<label class=\" \">Reason of decline<label><input required type=\"text\" name=\"decline_reason\" class=\"input\" />  <input type=\"submit\" name=\"decline[submit]\" value=\"Decline\" class=\"button is-danger is-small mt-2\"/>";';
                        $html .= 'jQuery("#declineReasonField").empty().append(textArea)';
                        $html .= '})</script>';
                  }
              
            }
            $html .= '</form>';
        }
        return $html;
    }


    public static function RequisitionBillstatusUpdateOrInsert(array $options = array())
    {

        //dd($options);

        $default = [
            'code' => null,
            'columnName' => null,
            'task_id' => null,
            'requisition_id' => null,
            'message' => null
        ];

        $new = (object)array_merge($default, $options);
        //$taskstatus = TaskStatus::create($status_attributes);

        $taskRequisitionBill = TaskRequisitionBill::where('id', $new->requisition_id)->where('task_id', $new->task_id)->first();
        $taskRequisitionBill[$new->columnName] = $new->message;
        $taskRequisitionBill->save();

    }

}
