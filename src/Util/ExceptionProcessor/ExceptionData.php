<?php

namespace App\Util\ExceptionProcessor;

/**
 * Class ExceptionData
 * @package App\Util\ExceptionProcessor
 */
class ExceptionData
{
    /** @var array */
    private $data;

    /**
     * ExceptionData constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        $this->data = $config;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->data['code'] ?? '';
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->data['date'] ?? '';
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->data['requestUri'] ?? '';
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return $this->data['requestParams'] ?? [];
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->data['file'] ?? '';
    }

    /**
     * @return string
     */
    public function getFileLine()
    {
        return $this->data['line'] ?? '';
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->data['message'] ?? '';
    }

    /**
     * @return string
     */
    public function getTrace()
    {
        return $this->data['trace'] ?? '';
    }
}
