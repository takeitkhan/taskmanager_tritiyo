<?php

namespace Tritiyo\Site\Repositories;

use Tritiyo\Site\Models\Site;
use Illuminate\Pagination\Paginator;
use DB;

class SiteEloquent implements SiteInterface
{
    private $model;

    /**
     * SiteEloquent constructor.
     * @param SiteInterface $model
     */
    public function __construct(Site $model)
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
            ->paginate(30);
    }

    public function getDataByFilter(array $options = [])
    {
        $default = [
            'search_key' => null,
            'limit' => 10,
            'offset' => 0
        ];
        $no = array_merge($default, $options);
        //dd($no);

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
        if (auth()->user()->isManager(auth()->user()->id)) {
            $m = " WHERE mm.manager = " . auth()->user()->id;
        } else {
            $m = " ";
        }

        if (!empty($no['search_key'])) {
            //Nipun
            $key = $no['search_key'];
            $query = DB::select("SELECT * FROM (
                  select `sites`.*, `projects`.`name`, `projects`.`code`, `projects`.`type`, `projects`.`customer`, `users`.`name` as pm_name, `projects`.`manager`
                  from `sites`
                  left join `projects` on `projects`.`id` = `sites`.`project_id`
                  left join `users` on `users`.`id` = `projects`.`manager`
                  where `sites`.`project_id` LIKE '%completed%'

                  or `sites`.`location` LIKE '%$key%'
                  or `sites`.`site_code` LIKE '%$key%'
                  or `sites`.`material` LIKE '%$key%'
                  or `sites`.`site_head` LIKE '%$key%'
                  or `sites`.`budget` LIKE '%$key%'
                  or `sites`.`completion_status` LIKE '%$key%'
                  or `projects`.`name` LIKE '%$key%'
                  or `projects`.`code` LIKE '%$key%'
                  or `projects`.`type` LIKE '%$key%'
                  or `projects`.`customer` LIKE '%$key%'
                  or `users`.`name` LIKE '%$key%'
                ) AS mm $m ");
            $maxPage = 48;
            $sites = new Paginator($query, $maxPage);
        } else {
            $sites = [];
        }


        //dd($sites);
        return $sites;
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

    /**
     * @param $column
     * @param $value
     */
    public function getByAnyWithPaginate($column, $value)
    {
        return $this->model->where($column, $value)->paginate(20);
    }
}
