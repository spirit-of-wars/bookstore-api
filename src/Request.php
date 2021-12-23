<?php

namespace App;

use App\Exception\DataFormatException;
use App\Util\Undefined;

/**
 * Class Request
 * @package App
 */
class Request extends \Symfony\Component\HttpFoundation\Request
{
    /** @var bool */
    private $validated;

    /** @var array */
    private $data;

    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    )
    {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
        $this->validated = false;
        $this->data = [];
    }

    /**
     * @param array $data
     */
    public function setValidated($data)
    {
        $this->validated = true;
        $this->data = $data;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        if ($this->validated) {
            return array_key_exists($key, $this->data);
        }

        $value = $this->get($key, new Undefined());
        if ($value instanceof Undefined) {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if ($this->validated) {
            return $this->data[$key] ?? $default;
        }

        return parent::get($key, $default);
    }

    /**
     * @return array
     */
    public function all()
    {
        if ($this->validated) {
            return $this->data;
        }

        return $this->request->all();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public static function createFromGlobals()
    {           
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        switch ($method) {
            case 'GET':
                self::properParseGetParams();
                break;

            case 'POST':
            case 'PATCH':
                self::parseExtension();
                break;
        }

        return parent::createFromGlobals();
    }

    /**
     * Try to receive POST parameters if method is POST and super-global array $_POST is empty
     */
    private static function parseExtension()
    {
        if (!empty($_POST)) {
            return;
        }

        $input = file_get_contents('php://input');
        if (!$input) {
            return;
        }

        if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
            $params = json_decode($input, true);
            if ($params === null) {
                throw new DataFormatException();
            }

            $_POST = $params;
        }
    }

    /**
     * PHP не умеет корректно парсить запросы типа "/list?type=paper_book&type=e_book"
     * приходится дорабатывать
     */
    private static function properParseGetParams() {
        $uri = urldecode($_SERVER['REQUEST_URI']);
        $paramsStr = (explode('?', $uri, 2))[1] ?? null;
        if (!$paramsStr) {
            return;
        }

        $pairs = explode('&', $paramsStr);
        $params = [];
        foreach ($pairs as $pare) {
            list($name, $value) = explode('=', $pare, 2);

            if (isset($params[$name])) {
                if (is_array($params[$name])) {
                    $params[$name][] = $value;
                } else {
                    $params[$name] = [$params[$name], $value];
                }
            } else {
                $params[$name] = $value;
            }
        }

        $_GET = $params;
    }
}
