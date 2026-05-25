<?php

namespace NaqlaSehia\Validation\Rules;

use NaqlaSehia\Validation\Rules\Contract\Rule;

class UniqueRule implements Rule
{
    protected $table;

    protected $column;

    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
    }

    public function apply($field, $value, $data =[])
    {
        $result = app()->db->raw(
            "SELECT * FROM {$this->table} WHERE {$this->column} = ?",
            [$value]
        );
        return empty($result);
    }

    public function __toString()
    {
        return 'This %s is already taken';
    }
}
