<?php

/**
 * DEBUT D'UN ORM, CA MARCHE MAIS JSUIS MOYEN CHAUD POUR CONTINUER PSK FAIRE DE LHERITAGE
 * SUR DES METHODES STATIC YA BEAUCOUP DE MAUVAISE SURPRISE
 * GO QUERYBUILDER
 */

namespace App\Frameworks\Orm;

use App\Db\Db;
use PDOStatement;
use ReflectionClass;

abstract class Entity
{
    /**
     * Get columns of the table
     *
     * @return String
     *
     * @example "id,name,age"
     */
    private function getColumns(): string
    {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties();

        return implode(',', array_map(function ($prop) {
            return $prop->getName();
        }, $props));
    }

    /**
     * Get values of the columns
     *
     * @return String
     *
     * @example "1','fantin', '20'"
     */
    private function getValues(): string
    {
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties();

        // first and last quote will set in sql query
        return implode("','", array_map(function ($prop) {
            return $this->{$prop->getName()};
        }, $props));
    }

    /**
     * Bind params for SET SQL field
     *
     * @param array $params
     * @return String
     *
     * @example "id='1',name='fantin',age='20'"
     */
    private function bindParamsForSetField(array $params): string
    {
        return implode(",", array_map(function ($key, $value) {
            return "{$key}='{$value}'";
        }, array_keys($params), $params));
    }

    /**
     * Bind params for WHERE SQL field
     *
     * @param array $params
     * @return String
     *
     * @example "(id='1' AND name='fantin' AND age='23')"
     */
    private function bindParamsForWhereField(array $params): string
    {
        $binded = implode(" AND ", array_map(function ($key, $value) {
            return "{$key}='{$value}'";
        }, array_keys($params), $params));
        return "($binded)";
    }

    /**
     * Get table name in lower case
     *
     * @return String
     */
    private function getTableName()
    {
        $class = new ReflectionClass($this);
        return strtolower($class->getName());
    }

    /**
     * Execute sql query
     *
     * @param string $query
     * @return PDOStatement
     */
    private function exec(string $query): PDOStatement
    {
        return Db::getInstance()->query($query);
    }

    /**
     * Save Document
     *
     * @return PDOStatement
     */
    public function save(): PDOStatement
    {
        $tableName = $this->getTableName();

        if (!$this->id) {
            $sqlQuery = "INSERT INTO " . $tableName . " (" . $this->getColumns() . ") VALUES ('" . $this->getValues() . "')";
        }

        return $this->exec($sqlQuery);
    }

    /**
     * Find document by id
     *
     * @param integer $id
     * @return PDOStatement
     */
    public static function findById(int $id): PDOStatement
    {
        $tableName = strtolower(get_called_class());
        $sqlQuery = "SELECT * FROM $tableName WHERE id = $id";
        return self::exec($sqlQuery);
    }

    /**
     * Find documents
     *
     * @param array $params1
     * @param array ...$params2
     * @return PDOStatement
     *
     * @example Document::find(['name' => 'fantin', 'age' => 20]) equal to "Select * from document where (name='fantin' AND age='20')"
     * @example Document::find(['name' => 'fantin', 'age' => 20], ['id' => 1]) equal to "Select * from document where (name='fantin' AND age='20') OR (id='1')"
     */
    public static function find(array $params1, array ...$params2)
    {
        $tableName = strtolower(get_called_class());
        $setterForWhere = self::bindParamsForWhereField($params1);
        foreach ($params2 as $p) {
            $setterForWhere .= " OR " . self::bindParamsForWhereField($p);
        }
        $sqlQuery = "SELECT * FROM $tableName WHERE $setterForWhere";
        return self::exec($sqlQuery);
    }

    /**
     * Find one document
     *
     * @param array $params
     * @return PDOStatement
     *
     * @example Document::findOne(['name' => 'fantin', 'age' => 20]) equal to "Select * from document where (name='fantin' AND age='20')"
     */
    public static function findOne(array $params)
    {
        $tableName = strtolower(get_called_class());
        $setterForWhere = self::bindParamsForWhereField($params);
        $sqlQuery = "SELECT * FROM $tableName WHERE $setterForWhere LIMIT 1";
        return self::exec($sqlQuery);
    }

    /**
     * Update document by id
     *
     * @param integer $id
     * @param array $params
     * @return PDOStatement
     */
    public static function updateById(int $id, array $params)
    {
        $tableName = strtolower(get_called_class());
        $setterForSet = self::bindParamsForSetField($params);
        $sqlQuery = "UPDATE $tableName SET $setterForSet WHERE id = $id";
        return self::exec($sqlQuery);
    }

    /**
     * Update documents
     *
     * @param array $params
     * @param array $updates
     * @return PDOStatement
     *
     * @example Document::update(['name' => 'fantin'], ['name' => 'oliwier']) equal to "UPDATE usertest SET name='oliwier' WHERE (name='fantin')"
     */
    public static function update(array $params, array $updates)
    {
        $tableName = strtolower(get_called_class());
        $setterForWhere = self::bindParamsForWhereField($params);
        $setterForSet = self::bindParamsForSetField($updates);
        $sqlQuery = "UPDATE $tableName SET $setterForSet WHERE $setterForWhere";
        return self::exec($sqlQuery);
    }

    /**
     * Delete document by id
     *
     * @param integer $id
     * @return PDOStatement
     */
    public static function deleteById(int $id)
    {
        $tableName = strtolower(get_called_class());
        $sqlQuery = "DELETE FROM $tableName WHERE id = $id";
        return self::exec($sqlQuery);
    }

    public static function delete(array $params1, array ...$params2)
    {
        $tableName = strtolower(get_called_class());
        $setterForWhere = self::bindParamsForWhereField($params1);
        foreach ($params2 as $p) {
            $setterForWhere .= " OR " . self::bindParamsForWhereField($p);
        }
        $sqlQuery = "DELETE FROM $tableName WHERE $setterForWhere";
        return self::exec($sqlQuery);
    }
}

class Usertest extends Entity
{
    protected $id;
    protected $name;
    protected $age;

    public function __construct($id, $name, $age)
    {
        $this->id = $id;
        $this->name = $name;
        $this->age = $age;
    }
}

// $results = Usertest::updateById(1, ['name' => 'arthur']);
$res = Usertest::delete(['age' => 20], ['age' => 23]);
print_r($res);
