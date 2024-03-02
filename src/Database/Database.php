<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Database;

use Exception;
use PDO;
use PDOException;

class Database
{
    protected static $instance;
    protected static $connection;
    protected static $table;
    protected static $select;
    protected static $join;
    protected static $where;
    protected static $group_by;
    protected static $having;
    protected static $order_by;
    protected static $limit;
    protected static $offset;
    protected static $query;
    protected static $binding = [];
    protected static $where_binding = [];
    protected static $having_binding = [];
    protected static $setter;

    private function __construct()
    {
    }

    private static function connect()
    {
        if (!self::$connection) {


            $dsn = 'mysql:host=' .
                getenv('DB_HOST') . ';dbname=' .
                getenv('DB_NAME');
            try {
                self::$connection = new PDO($dsn,
                    getenv('DB_USER'),
                    getenv('DB_PASS'), [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                        PDO::ATTR_PERSISTENT => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => ' set NAMES ' .
                            getenv('CHARSET') . ' COLLATE ' .
                            getenv('COLLATE')
                    ]);
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    private static function instance()
    {
        self::connect();
        if (!self::$instance) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public static function query(string $query = null)
    {
        self::instance();

        if (is_null($query)) {
            if (!self::$table) {
                throw new Exception('Table name unknown!');
            }

            $query = 'SELECT ';
            $query .= self::$select ?: '*';
            $query .= ' FROM ' . self::$table;
            $query .= ' ' . self::$join;
            $query .= ' ' . self::$where;
            $query .= ' ' . self::$group_by;
            $query .= ' ' . self::$having;
            $query .= ' ' . self::$order_by;
            $query .= ' ' . self::$limit;
            $query .= ' ' . self::$offset;
        }

        self::$query = $query;
        self::$binding = array_merge(self::$where_binding, self::$having_binding);

        return self::instance();
    }

    public static function select()
    {
        self::$select = implode(', ', func_get_args());

        return self::instance();
    }

    public static function table(string $name)
    {
        self::$table = $name;
        return self::instance();
    }

    public static function join(string $table, string $first, string $operator, $second, $type = 'INNER')
    {
        self::$join .= ' ' . $type . ' JOIN ' . $table . ' ON ' . $first . $operator . $second;
        return self::instance();
    }

    public function rightJoin(string $table, string $first, string $operator, $second)
    {
        self::join($table, $first, $operator, $second, 'RIGHT');
        return self::instance();
    }

    public function leftJoin(string $table, string $first, string $operator, $second)
    {
        self::join($table, $first, $operator, $second, 'LEFT');
        return self::instance();
    }

    public static function where(string $column, string $operator, mixed $value, string $type = null)
    {
        $where = '`' . $column . '` ' . $operator . ' ? ';
        if (!self::$where) {
            $stmt = ' WHERE ' . $where;
        } else {
            if ($type === null) {
                $stmt = ' AND ' . $where;
            } else {
                $stmt = ' ' . $type . ' ' . $where;
            }
        }

        self::$where .= $stmt;
        self::$where_binding[] = htmlspecialchars((string)$value);

        return self::instance();
    }

    public static function orWhere(string $column, string $operator, mixed $value)
    {
        self::where($column, $operator, $value, 'OR');
        return self::instance();
    }

    public static function groupBy()
    {
        self::$group_by = ' GROUP BY ' . implode(', ', func_get_args());
        return self::instance();
    }

    public static function having(string $column, string $operator, mixed $value)
    {
        $having = '`' . $column . '` ' . $operator . ' ? ';
        if (!self::$having) {
            $stmt = ' HAVING ' . $having;
        } else {
            $stmt = ' AND ' . $having;
        }

        self::$having .= $stmt;
        self::$having_binding[] = htmlspecialchars((string)$value);

        return self::instance();
    }

    public static function orderBy(string $column, string $type = null)
    {
        $sep = self::$order_by ? ', ' : ' ORDER BY ';

        $type = ($type != null && in_array(strtoupper($type), ['ASC', 'DESC'])) ? strtoupper($type) : 'ASC';

        $stmt = $sep . $column . ' ' . $type . '';

        self::$order_by .= $stmt;
        return self::instance();
    }

    public static function limit(int|string $limit)
    {
        self::$limit = ' LIMIT ' . $limit;
        return self::instance();
    }

    public static function offset(int|string $offset)
    {
        self::$offset = ' OFFSET ' . $offset;
        return self::instance();
    }

    public static function fetchExecute()
    {
        self::query();
        $query = trim(self::$query, ' ');
        $data = self::$connection->prepare($query);
        $data->execute(self::$binding);

        self::cleare();

        return $data;
    }

    public static function get()
    {
        $data = self::fetchExecute();
        $result = $data->fetchAll();

        return $result;
    }

    public static function first()
    {
        $data = self::fetchExecute();
        $result = $data->fetch();

        return $result;
    }

    public static function execute(array $data, string $query, bool $where = null)
    {
        self::instance();
        if (!self::$table) {
            throw new Exception('Table unknown!');
        }
        if ($data) {
            foreach ($data as $key => $value) {
                self::$setter .= '`' . $key . '` = ?,';

                self::$binding[] = filter_var($value, FILTER_SANITIZE_STRING);
            }
            self::$setter = trim(self::$setter, ', ');
        }

        $query .= self::$setter;
        $query .= $where != null ? self::$where . ' ' : '';

        self::$binding = $where != null ? array_merge(self::$binding, self::$where_binding) : self::$binding;

        $data = self::$connection->prepare($query);
        $data->execute(self::$binding);

        self::cleare();
    }

    public static function insert(array $data)
    {
        $table = self::$table;
        $query = 'INSERT INTO ' . $table . ' SET ';

        self::execute($data, $query);

        $id = self::$connection->lastInsertId();

        return self::table($table)->where('id', '=', $id)->first();
    }

    public static function update(array $data)
    {
        $query = 'UPDATE ' . self::$table . ' SET ';
        self::execute($data, $query, true);

        return true;
    }

    public static function delete()
    {
        $query = 'DELETE FROM ' . self::$table . ' ';
        self::execute([], $query, true);

        return true;
    }

    private static function cleare(): void
    {
        self::$setter = '';
        self::$select = '';
        self::$join = '';
        self::$where = '';
        self::$where_binding = [];
        self::$group_by = '';
        self::$having = '';
        self::$having_binding = [];
        self::$order_by = '';
        self::$limit = '';
        self::$offset = '';
        self::$binding = [];
        self::$query = '';
        self::$instance = '';
    }

}