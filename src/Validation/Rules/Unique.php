<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Validation\Rules;

use Lordsaudkaric\Purephp\Database\Database;
use Rakit\Validation\Rule;

class Unique extends Rule
{
    protected $message = ':attribute :value has been used';

    protected $fillableParams = ['table', 'column', 'except'];

    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);

        // getting parameters
        $column = $this->parameter('column');
        $table = $this->parameter('table');
        $except = $this->parameter('except');

        if ($except and $except == $value) {
            return true;
        }

        // do query
        $data = Database::table($table)->where($column, '=', $value)->first();

        // true for valid, false for invalid
        return $data ? false : true;
    }
}