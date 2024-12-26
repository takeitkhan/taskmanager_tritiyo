<?php
namespace Tritiyo\Project\Repositories\Project;

use Tritiyo\Project\Models\Project;

class ProjectEloquent implements ProjectInterface
{
    private $model;

    /**
     * ProjectEloquent constructor.
     * @param ProjectInterface $model
     */
    public function __construct(Project $model)
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

        if (!empty($no['search_key'])) {
            $projects = $this->model
            ->where('name', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('code', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('type', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('customer', 'LIKE', '%'.$no['search_key'].'%')
            ->paginate('30');

            //dd($sites);
        } else {
            $projects = [];
        }

        //dd($projects);
        return $projects;
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
}
