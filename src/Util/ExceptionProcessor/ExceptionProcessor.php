<?php

namespace App\Util\ExceptionProcessor;

use App\Mif;
use App\Request;
use Throwable;

/**
 * Class ExceptionProcessor
 * @package App\Util\ExceptionProcessor
 */
class ExceptionProcessor
{
    /** @var string */
    private $date;

    /** @var Request */
    private $request;

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @param Throwable $exception
     * @return string
     */
    public function process($exception)
    {
        $this->baseInit();
        $code = md5(microtime());

        $data = new ExceptionData([
            'code' => $code,
            'date' => $this->date,
            'requestUri' => $this->request->getUri(),
            'requestParams' => $this->request->request->all(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $logger = new ExceptionLogger();
        $logger->log($data);

        return $code;
    }

    /**
     * @return void
     */
    private function baseInit()
    {
        if (!$this->date) {
            $this->date = date('Y-m-d');
        }

        if (!$this->request) {
            $this->request = Mif::$app->getRequest();
        }
    }
}
