<?php
namespace Tritiyo\Task\Repositories\TaskRequisitionBill;

interface TaskRequisitionBillInterface
{
    public function getAll();

    public function getById($id);

    public function getByAny($column, $value);

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function delete($id);
}
