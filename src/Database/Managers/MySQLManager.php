<?php

namespace NaqlaSehia\Database\Managers;

use App\Models\Model;
use NaqlaSehia\Database\Grammars\MySQLGrammar;
use NaqlaSehia\Database\Managers\Contracts\DatabaseManager;
use PDOException;

class MySQLManager implements DatabaseManager
{
    protected static $instance;

    public function connect(): \PDO
    {
        if (!self::$instance) {
            try {
                $dsn = 'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE');
                self::$instance = new \PDO(
                    $dsn,
                    env('DB_USERNAME'),
                    env('DB_PASSWORD'),
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                throw new \Exception('Database connection failed: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public function query(string $query, $values = [])
    {
        $stmt = $this->connect()->prepare($query);

        for ($i = 1; $i <= count($values); $i++) {
            $stmt->bindValue($i, $values[$i - 1]);
        }

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function read($columns = '*', $filter = null)
    {
        $query = MySQLGrammar::buildSelectQuery($columns, $filter);

        $stmt = $this->connect()->prepare($query);

        if ($filter) {
            $stmt->bindValue(1, $filter[2]);
        }

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, Model::getModel());
    }

    public function delete($id)
    {
        $query = MySQLGrammar::buildDeleteQuery();

        $stmt = $this->connect()->prepare($query);

        $stmt->bindValue(1, $id);

        return $stmt->execute();
    }

    public function update($id, $attributes)
    {
        $query = MySQLGrammar::buildUpdateQuery(array_keys($attributes));

        $stmt = $this->connect()->prepare($query);

        $values = array_values($attributes);
        for ($i = 1; $i <= count($values); $i++) {
            $stmt->bindValue($i, $values[$i - 1]);
            if ($i == count($values)) {
                $stmt->bindValue($i + 1, $id);
            }
        }

        return $stmt->execute();
    }

    public function create($data)
    {
        $query = MySQLGrammar::buildInsertQuery(array_keys($data));

        $stmt = $this->connect()->prepare($query);

        $values = array_values($data);
        for ($i = 1; $i <= count($values); $i++) {
            $stmt->bindValue($i, $values[$i - 1]);
        }

        return $stmt->execute();
    }
}
