<?php
namespace Tritiyo\Vehicle\Repositories;

use Tritiyo\Vehicle\Models\Vehicle;

class VehicleEloquent implements VehicleInterface
{
    private $model;

    /**
     * VehicleEloquent constructor.
     * @param VehicleInterface $model
     */
    public function __construct(Vehicle $model)
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
               ->paginate(10);
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
            $vehicles = $this->model
            ->where('name', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('size', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('probably_cost', 'LIKE', '%'.$no['search_key'].'%')
            ->paginate('48');


        } else {
            $vehicles = [];
        }

        return $vehicles;
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
