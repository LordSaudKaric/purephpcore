<?php declare(strict_types=1);

namespace Lordsaudkaric\Purephp\Validation;

use Lordsaudkaric\Purephp\Request\Request;
use Lordsaudkaric\Purephp\Session\Session;
use Lordsaudkaric\Purephp\Url\Url;
use Lordsaudkaric\Purephp\Validation\Rules\Unique;
use Rakit\Validation\Validator;

class Validate
{
    public function __construct()
    {
    }

    public static function validate(array $rules, bool $json)
    {
        $validator = new Validator();
        $validator->addValidator('unique', new Unique());
        $validation = $validator->validate($_POST + $_FILES, $rules);
        $errors = $validation->errors();

        if ($validation->fails()) {

            if ($json) {
                return ['errors' => $errors->firstOfAll()];
            } else {
                Session::set('errors', $errors);
                Session::set('old', Request::all());

                return Url::redirect(Url::previous());
            }
        }
    }
}