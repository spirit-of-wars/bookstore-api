<?php

class CurlRequest
{
    private $handle = null;

    /**
     * CurlRequest constructor.
     * @param $url
     */
    public function __construct($url)
    {
        $this->handle = curl_init($url);
    }

    /**
     * @param $name
     * @param $value
     */
    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
    }

    /**
     * @return bool|string
     */
    public function execute()
    {
        return curl_exec($this->handle);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    /**
     * close the curl connection
     */
    public function close()
    {
        curl_close($this->handle);
    }

    /**
     * @return string
     */
    public function echoOk()
    {
        return "\n \e[32mOK \n";
    }

    /**
     * @return string
     */
    public function echoError()
    {
        return "\n \e[31mError! \n";
    }
}
