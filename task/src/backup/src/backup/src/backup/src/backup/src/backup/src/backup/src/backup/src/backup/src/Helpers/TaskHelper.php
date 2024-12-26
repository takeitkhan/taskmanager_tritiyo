<?php

namespace Tritiyo\Task\Helpers;

use Tritiyo\Task\Models\Task;
use Tritiyo\Task\Models\TaskStatus;

class TaskHelper
{

    public static function taskMessageHandler()
    {
        $task_statuses = array(
            'task_created' => array(
                'key' => 'task_created',
                'message' => 'Task created by manager'
            ),
            'task_assigned_to_head' => array(
                'key' => 'task_assigned_to_head',
                'message' => 'Task assigned to head'
            ),
            'head_accepted' => array(
                'key' => 'head_accepted',
                'message' => 'Task accepted by site head'
            ),
            'head_declined' => array(
                'key' => 'head_declined',
                'message' => 'Declined by site head'
            ),
            'proof_given' => array(
                'key' => 'proof_given',
                'message' => 'Task proof given by site head'
            ),
            'task_approver_edited' => array(
                'key' => 'task_approver_edited',
                'message' => 'Task edited by approver'
            ),
            'approver_approved' => array(
                'key' => 'approver_approved',
                'message' => 'Task approved by approver'
            ),
            'approver_declined' => array(
                'key' => 'approver_declined',
                'message' => 'Declined by approver'
            ),
            'task_override_data' => array(
                'key' => 'task_override_data',
                'message' => 'Task data override by manager'
            ),

            // Newly Added... Not yet used
            'requisition_prepared_by_manager' => array(
                'key' => 'requisition_prepared_by_manager',
                'message' => 'Requisition prepared by manager',
            ),
            'requisition_submitted_by_manager' => array(
                'key' => 'requisition_submitted_by_manager',
                'message' => 'Requisition submitted by manager',
            ),
            'requisition_edited_by_cfo' => array(
                'key' => 'requisition_edited_by_cfo',
                'message' => 'Requisition edited by CFO',
            ),
            'requisition_approved_by_cfo' => array(
                'key' => 'requisition_approved_by_cfo',
                'message' => 'Requisition approved by CFO',
            ),
            'requisition_declined_by_cfo' => array(
                'key' => 'requisition_declined_by_cfo',
                'message' => 'Requisition declined by CFO',
            ),

            'requisition_edited_by_accountant' => array(
                'key' => 'requisition_edited_by_accountant',
                'message' => 'Requisition edited by accountant',
            ),
            'requisition_approved_by_accountant' => array(
                'key' => 'requisition_approved_by_accountant',
                'message' => 'Requisition approved by accountant',
            ),
            'requisition_declined_by_accountant' => array(
                'key' => 'requisition_declined_by_accountant',
                'message' => 'Requisition declined by accountant',
            ),
            // Bill status... not yet used
            'bill_prepared_by_resource' => array(
                'key' => 'bill_prepared_by_resource',
                'message' => 'Bill prepared by resource',
            ),
            'bill_submitted_by_resource' => array(
                'key' => 'bill_submitted_by_resource',
                'message' => 'Bill submitted by resource',
            ),
            'bill_edited_by_manager' => array(
                'key' => 'bill_edited_by_manager',
                'message' => 'Bill edited by manager',
            ),
            'bill_approved_by_manager' => array(
                'key' => 'bill_approved_by_manager',
                'message' => 'Bill approved by manager',
            ),

            'bill_edited_by_cfo' => array(
                'key' => 'bill_edited_by_cfo',
                'message' => 'Bill edited by CFO',
            ),
            'bill_approved_by_cfo' => array(
                'key' => 'bill_approved_by_cfo',
                'message' => 'Bill approved by CFO',
            ),

            'bill_edited_by_accountant' => array(
                'key' => 'bill_edited_by_accountant',
                'message' => 'Bill edited by accountant',
            ),
            'bill_approved_by_accountant' => array(
                'key' => 'bill_approved_by_accountant',
                'message' => 'Bill approved by accountant',
            )
        );

        return $task_statuses;
    }

    public static function getStatusKey($arg)
    {
        if (isset($arg)) {
            foreach (self::taskMessageHandler() as $key => $val) {
                if ($key == $arg) {
                    return $val['key'];
                }
            }
        }
    }

    public static function getStatusMessage($arg)
    {
        if (isset($arg)) {
            foreach (self::taskMessageHandler() as $key => $val) {
                if ($key == $arg) {
                    return $val['message'];
                }
            }
        }
    }


    public static function statusUpdate(array $options = array())
    {
        $default = [
            'code' => null,
            'task_id' => null,
            'action_performed_by' => null,
            'performed_for' => null,
            'requisition_id' => null,
            'message' => null
        ];

        $new = (object)array_merge($default, $options);

        $status_attributes = [
            'code' => $new->code,
            'task_id' => $new->task_id,
            'action_performed_by' => $new->action_performed_by,
            'performed_for' => $new->performed_for,
            'requisition_id' => $new->requisition_id,
            'message' => $new->message
        ];

        $taskstatus = TaskStatus::create($status_attributes);
        return $taskstatus;
    }


    public static function statusUpdateOrInsert(array $options = array())
    {

        //dd($options);

        $default = [
            'code' => null,
            'task_id' => null,
            'action_performed_by' => null,
            'performed_for' => null,
            'requisition_id' => null,
            'message' => null
        ];

        $new = (object)array_merge($default, $options);

        $status_attributes = [
            'code' => $new->code,
            'task_id' => $new->task_id,
            'action_performed_by' => $new->action_performed_by,
            'performed_for' => $new->performed_for,
            'requisition_id' => $new->requisition_id,
            'message' => $new->message
        ];
        //$taskstatus = TaskStatus::create($status_attributes);

        $taskstatus = TaskStatus::updateOrCreate(
            $status_attributes
        );
        return $taskstatus;
    }


    public static function buttonInputApproveDecline($approve, $decline)
    {

        $html = '<input type="hidden" name="accept[approve_code]" value="' . $approve . '" class="button is-success"/>';
        $html .= '<input type="submit" name="accept[approve]" value="Approve" class="button is-success is-small"/>';
        $html .= '<input type="hidden" name="decline[decline_code]" value="' . $decline . '" class="button is-danger"/>';
        $html .= '<input type="submit" name="decline[decline]" value="Decline" class="button is-danger is-small ml-2"/>';
        return $html;
    }

    public static function arrayExist($arr, $name, $value)
    {
        $collection = $arr->contains($name, $value);
        return $collection;
    }


    /**
     * Modal Helper
     */
    public static function modalImage($id, $link)
    {
        $html = '<div id="'.$id.'" class="modal">';
        $html .= '<div class="modal-background"></div>';
        $html .= '<div class="modal-content">';
        $html .= '<p class="image is-4by3">';
        $html .= '<img src="'. $link .'"  width="auto"/>';
        $html .= '</p>';
        $html .= '</div>';
        $html .= '<button class="modal-close is-large" aria-label="close"></button>';
        $html .= '</div>';
        return $html;
    }

    /************* Requisition and Bill Part Helping Methods ****************/

    /**
     * @param array $options
     * @return string
     */



    public static function ManagerOverrideData($task_id){
        /**
         * if manager edited any data during requisition after approver data
         * action delete this approver approved status from tasksstatus table
         */
        //dd('ok')
        $approved_task_status = TaskStatus::where('task_id', $task_id)->where('code', 'approver_approved')->first();
        $taskInfo = Task::where('id', $task_id)->first();
        if (auth()->user()->isManager(auth()->user()->id) && $taskInfo->override_status == 'No'|| $approved_task_status != null) {
            //Old data enter store in to manager_override_chunk
            $chunck = [
                'task' => \Tritiyo\Task\Models\Task::select('id', 'user_id', 'task_name', 'task_code', 'task_type', 'project_id', 'site_head', 'task_details', 'anonymous_proof_details', 'task_assigned_to_head', 'task_for', 'override_status', 'is_active', 'created_at', 'updated_at')->where('id', $task_id)->get()->toArray(),
                'task_site' => \Tritiyo\Task\Models\TaskSite::where('task_id', $task_id)->get()->toArray(),
                'task_vehicle' => \Tritiyo\Task\Models\TaskVehicle::where('task_id', $task_id)->get()->toArray(),
                'task_material' => \Tritiyo\Task\Models\TaskMaterial::where('task_id', $task_id)->get()->toArray(),
            ];
            $put = Task::find($task_id);
            $put->manager_override_chunck = $chunck;
            $put->override_status = 'No';
            //dd($put);
            $put->save();
            //delete approver staatus
            if (!empty($approved_task_status->id)){
                $data = TaskStatus::find($approved_task_status->id);
                $data->delete();
            }
            //override data status
            TaskHelper::statusUpdateOrInsert([
                'code' => TaskHelper::getStatusKey('task_override_data'),
                'task_id' => $task_id,
                'action_performed_by' => auth()->user()->id,
                'performed_for' => null,
                'requisition_id' => null,
                'message' => TaskHelper::getStatusMessage('task_override_data')
            ]);
        }

    }


    public static function hiddenInput(){
        $html = '<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>';
        $html .= '<script>';
        //$html .= // $('form#requisition_form input').attr('disabled', true);
        //$html .= // $('form#requisition_form button').addClass('is-hidden');
        $html .= "$('form#add_route button').addClass('is-hidden')";
        $html .= "$('form#add_route input').attr('disabled', true)";
        $html .= "$('form#add_route textarea').attr('disabled', true)";
        $html .=   "$('form#add_route select').attr('disabled', true)";
        $html .= '</script>';
        return $html;
    }


    public static function getPendingBillCountStatus($resource_id) {
        $bill_not_submitted = \Tritiyo\Task\Models\Task::select('tasks.id', 'tasks.site_head', 'trb.*')
                                ->leftJoin('tasks_requisition_bill AS trb', 'trb.task_id', 'tasks.id')
                                ->where('trb.requisition_approved_by_accountant', '=', 'Yes')
                                ->whereRaw('trb.bill_submitted_by_resource IS NULL')
                                ->where('tasks.site_head', $resource_id)->get();
        $total_blank = $bill_not_submitted->count();
        //dd($total_blank);
        if($total_blank > 1) {
            return 'Yes';
        } else {
            return 'No';
        }
    }


}


