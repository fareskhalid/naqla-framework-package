<?php

namespace NaqlaSehia\Database;

use NaqlaSehia\Database\Concerns\ConnectsTo;
use NaqlaSehia\Database\Managers\Contracts\DatabaseManager;

class DB
{
    protected DatabaseManager $manager;

    public function __construct(DatabaseManager $manager)
    {
        $this->manager = $manager;
    }

    public function init()
    {
        ConnectsTo::connect($this->manager);
    }

    public function raw(string $query, $value = [])
    {
        return $this->manager->query($query, $value);
    }

    public function create(array $data)
    {
        return $this->manager->create($data);
    }

    public function delete($id)
    {
        return $this->manager->delete($id);
    }

    public function update($id, array $attributes)
    {
        return $this->manager->update($id, $attributes);
    }

    public function read($columns = '*', $filter = null)
    {
        return $this->manager->read($columns, $filter);
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return call_user_func_array([$this, $name], $arguments);
        }
    }
}
