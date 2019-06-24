<?php
/**
 * Created by PhpStorm.
 * User: bw
 * Date: 16.03.2019
 * Time: 20:33
 */

namespace core;

use core\interfaces\EntityInterface;
use core\interfaces\RepositoryInterface;

class Repository implements RepositoryInterface
{
    /** @var EntityInterface $_entityClass */
    protected $_entityClass;
    /** @var array */
    protected $_items = [];

    /**
     * Repository constructor.
     * @param $entityClass
     * @throws \ReflectionException
     */
    public function __construct($entityClass)
    {
        if ((new \ReflectionClass($entityClass))->implementsInterface(EntityInterface::class)) {
            $this->_entityClass = $entityClass;
        } else {
            throw new \Exception("Class $entityClass must implement EntityInterface");
        }
    }

    /**
     * @param \PDOStatement $statement
     */
    protected function _fetchWithMapping(\PDOStatement $statement)
    {
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $Entity = new $this->_entityClass($row);
            $this->_items[] = $Entity;
        }
    }

    /**
     * @param null $condition
     * @return mixed|void
     */
    public function find($condition = null)
    {
        //@TODO
    }

    /**
     * @param null $condition
     * @return array
     */
    public function findAll($condition = null): array
    {
        $query = 'SELECT * FROM ' . $this->_entityClass::tableName() . " $condition " . ' LIMIT 20';
        $statement = App::$instance->getDB()->prepare($query);
        $statement->execute();
        $this->_fetchWithMapping($statement);

        return $this->_items;
    }

    /**
     * @param EntityInterface $Entity
     * @return array
     */
    protected function _prepareAttributes(EntityInterface $Entity)
    {
        $mapping = $this->_entityClass::fieldsMapping();
        $columns = $values = $placeholders = [];
        foreach ($mapping as $column => $attributeName) {
            $getter = 'get' . ucfirst($attributeName);

            if (method_exists($Entity, $getter)) {
                $columns[$column] = $column;
                $values[$column] = empty($Entity->$getter()) ? null : $Entity->$getter();
                $placeholders[$column] = ':' . $column;
            }
        }

        return [
            $columns,
            $placeholders,
            $values,
        ];
    }

    /**
     * @param EntityInterface $Entity
     * @return bool
     */
    public function add(EntityInterface $Entity)
    {
        $tableName = $this->_entityClass::tableName();

        list($columns, $placeholders, $values) = $this->_prepareAttributes($Entity);

        $columnsString = implode(', ', $columns);
        $placeholdersString = implode(', ', $placeholders);
        $query = "INSERT INTO $tableName ($columnsString) VALUES ($placeholdersString)";
        $statement = App::$instance->getDB()->prepare($query);

        foreach ($values as $column => $value) {
            $statement->bindParam($placeholders[$column], $values[$column]);
        }

        if ($statement->execute($values)) {
            $this->_items[] = $Entity;

            return true;
        }

        return false;
    }

    /**
     * @param EntityInterface $Entity
     * @return bool
     */
    public function save(EntityInterface $Entity)
    {
        $tableName = $this->_entityClass::tableName();
        $pk = $this->_entityClass::primaryKey();

        list($columns, $placeholders, $values) = $this->_prepareAttributes($Entity);

        $query = "UPDATE $tableName ($columns) VALUES ($placeholders) WHERE $pk = {$values[$pk]}";

        if ($statement = App::$instance->getDB()->prepare($query)->execute($values)) {
            $this->_items[$Entity->getId()] = $Entity;

            return true;
        }

        return false;
    }

    /**
     * @param EntityInterface $Entity
     * @return bool
     */
    public function delete(EntityInterface $Entity): bool
    {
        if (isset($this->_items[$Entity->getId()])) {
            unset($this->_items[$Entity->getId()]);

            return true;
        }

        return false;
    }
}
