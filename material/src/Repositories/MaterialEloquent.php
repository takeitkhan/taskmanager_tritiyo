<?php
namespace Tritiyo\Material\Repositories;

use Tritiyo\Material\Models\Material;

class MaterialEloquent implements MaterialInterface
{
    private $model;

    /**
     * MaterialEloquent constructor.
     * @param MaterialInterface $model
     */
    public function __construct(Material $model)
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
               ->paginate(60);
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
            $materials = $this->model
            ->where('name', 'LIKE', '%'.$no['search_key'].'%')
            ->paginate('48');

            //dd($sites);
        } else {
            $materials = [];
        }

        //dd($sites);
        return $materials;
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
