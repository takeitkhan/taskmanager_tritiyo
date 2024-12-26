<?php
namespace Tritiyo\Task\Repositories;

use Tritiyo\Task\Models\Task;
use DB;

class TaskEloquent implements TaskInterface
{
    private $model;

    /**
     * TaskEloquent constructor.
     * @param TaskInterface $model
     */
    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    /**
     *
     */
    public function getAll()
    {
        return $this->model
               ->orderBy('id', 'desc')
               //->take(100)
               ->paginate('18');
    }

    public function getDataByFilter(array $options = [])
    {
        $default = [
            'search_key' => null,
            'column' => !empty($field) ? $field : null,
            'sort_type' => !empty($type) ? $type : null,
            'limit' => 10,
            'offset' => 0
        ];
        $no = array_merge($default, $options);

        if (!empty($no['limit'])) {
            $limit = $no['limit'];
        } else {
            $limit = 10;
        }

        if (!empty($no['offset'])) {
            $offset = $no['offset'];
        } else {
            $offset = 0;
        }

        if (!empty($no['sort_type'])) {
            $orderBy = $no['column'] . ' ' . $no['sort_type'];
        } else {
            $orderBy = 'id desc';
        }

        if (!empty($no['search_key']) && $no['search_key'] != 'undefined') {
            if ($totalrowcount == true) {
                return $this->model
                    ->orWhere('name', 'like', "%{$no['search_key']}%")
                    ->paginate($limit)
                    ->get()->count();
            } else {
                return $this->model
                    //->leftJoin('productcategories', function ($join) {
                    //    $join->on('products.id', '=', 'productcategories.main_pid');
                    //})
                    ->paginate($limit)
                    //->toSql();
                    ->get();
            }
        } else {
            if ($totalrowcount == true) {
                return $this->model
                    ->whereRaw('parent_id IS NULL')
                    //->whereRaw('FIND_IN_SET(' . implode(',', $categories) . ', categories)')
                    //->whereRaw($price_btw)
                    //->orderByRaw($orderBy)
                    ->get()->count();
            } else {
                return $this->model
                    ->leftJoin('productcategories AS pc', function ($join) {
                        $join->on('products.id', '=', 'pc.main_pid');
                    })
                    //->whereIn('pc.term_id', $no['category'])
                    //->whereRaw('parent_id IS NULL')
                    //->whereRaw($price_btw)
                    //->orderByRaw($orderBy)
                    //->offset($offset)->limit($limit)
                    //->toSql();
                    //->select(['products.*', 'pc.*', 'products.id AS proid'])
                    //->orderBy('products.id', 'desc')
                    ->paginate(5);
            }
        }
    }


    /**
     * @param $id
     */
    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
    * @param $column
    * @param $value
    */
    public function getByAny($column, $value)
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * @param array $att
     */
    public function create(array $att)
    {
        return $this->model->create($att);
    }

    /**
     * @param $id
     * @param array $att
     */
    public function update($id, array $att)
    {
        $todo = $this->getById($id);
        $todo->update($att);
        return $todo;
    }

    public function delete($id)
    {
        $this->getById($id)->delete();
        return true;
    }



    public function advanced_search(array $options) {
        $default = [
            'q' => null,
            'task_type' => null,
            'bill_status' => null,
            'project_id' => null,
            'site_head_id' => null,
            'start' => null,
            'end' => null,
        ];
        $mo = (object) array_merge($default, $options);
        //dd($mo);


        $search = $mo->q;

        if($mo->project_id != NULL) {
            $project_id = " AND tasks_datas.project_id = $mo->project_id";
            //dd($project_id);
        } else {
            $project_id = " ";
        }

        if($mo->site_head_id != NULL) {
            $site_head = " AND tasks_datas.site_head = $mo->site_head_id";
        } else {
            $site_head = " ";
        }

        if($mo->task_type != NULL) {
            $task_type = " AND task_type = '$mo->task_type'";
        } else {
            $task_type = " ";
        }


        if($mo->start != NULL && $mo->end != NULL) {
            $daterange = " AND task_for BETWEEN '$mo->start' AND '$mo->end'";
        } else {
            $daterange = " ";
        }

        $getResult = DB::select("SELECT
                    tasks_datas.id AS main_task_id,
                    tasks_datas.*,
                    creator.*,
                    site_head.*,
                    projects_datas.*,
                    requisition_datas.*,
                    tasks_site_datas.*,
                    tasks_vehicle_datas.*,
                    tasks_material_datas.*
            FROM `tasks_datas`
            LEFT JOIN users_datas AS creator ON creator.id = tasks_datas.user_id
            LEFT JOIN users_datas AS site_head ON site_head.id = tasks_datas.site_head
            LEFT JOIN projects_datas ON projects_datas.id = tasks_datas.project_id
            LEFT JOIN requisition_datas ON requisition_datas.task_id = tasks_datas.id
            LEFT JOIN tasks_site_datas ON tasks_site_datas.task_id = tasks_datas.id
            LEFT JOIN tasks_vehicle_datas ON tasks_vehicle_datas.task_id = tasks_datas.id
            LEFT JOIN tasks_material_datas ON tasks_material_datas.task_id = tasks_datas.id

            WHERE (
                task_name LIKE '%$search%' OR task_type LIKE '%$search%' OR task_code LIKE '%$search%' OR project_id LIKE '%$search%'
                OR site_head LIKE '%$search%' OR task_for LIKE '%$search%' OR all_tasks_datas LIKE '%$search%' OR creator.name LIKE '%$search%'
                OR creator.email LIKE '%$search%' OR creator.username LIKE '%$search%' OR creator.phone LIKE '%$search%'
                OR creator.all_users_datas LIKE '%$search%'

                OR site_head.name LIKE '%$search%' OR site_head.email LIKE '%$search%' OR site_head.username LIKE '%$search%' OR site_head.phone LIKE '%$search%'
                OR site_head.all_users_datas LIKE '%$search%'

                OR code LIKE '%$search%' OR type LIKE '%$search%' OR manager LIKE '%$search%' OR all_projects_datas LIKE '%$search%'
                OR all_requisition_datas LIKE '%$search%'

                OR site_id LIKE '%$search%' OR resource_id LIKE '%$search%' OR all_tasks_sites_datas LIKE '%$search%'

                OR tasks_vehicle_datas LIKE '%$search%'
                OR tasks_material_datas LIKE '%$search%'
            )

            $task_type

            $daterange

            $project_id

            $site_head


            GROUP BY tasks_datas.id");

        //dd($getResult);

        return $getResult;
    }
}
