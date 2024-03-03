<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Database;

class DbModel extends Database
{
    public static function store(array $data)
    {
        return self::table(static::$table)->insert($data);
    }

    public static function edit(array $data, mixed $value, string $column = 'id', string $operator = '=')
    {
        return self::table(static::$table)
            ->where($column, $operator, $value)
            ->update($data);
    }

    public static function destroy(mixed $value, string $column = 'id', string $operator = '=')
    {
        return self::table(static::$table)
            ->where($column, $operator, $value)
            ->delete();
    }

    public static function getAll()
    {
        return self::table(static::$table)->get();
    }

    public static function getOne(mixed $value, string $column = 'id', string $operator = '=')
    {
        return self::table(static::$table)
            ->where($column, $operator, $value)
            ->first();
    }

}