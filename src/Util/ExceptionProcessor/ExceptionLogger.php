<?php

namespace App\Util\ExceptionProcessor;

use App\Mif;

/**
 * Class ExceptionLogger
 * @package App\Util\ExceptionProcessor
 */
class ExceptionLogger
{
    const FILE_SIZE_LIMIT = 2000000;

    /**
     * @param ExceptionData $data
     */
    public function log($data)
    {
        $date = $data->getDate();
        $filePath = $this->defineFilePath($date);
        $message = $this->prepareMessage($data);
        $this->putMessage($filePath, $message);
    }

    /**
     * @param ExceptionData $data
     * @return string
     */
    private function prepareMessage($data)
    {
        $message = '###BEGIN:' . $data->getCode() . PHP_EOL;

        $message .= '-- date: ' . $data->getDate() . PHP_EOL;
        $message .= '-- requestUri: ' . $data->getRequestUri() . PHP_EOL;
        $message .= '-- requestParams: ' . json_encode($data->getRequestParams(), JSON_UNESCAPED_UNICODE) . PHP_EOL;
        $message .= '-- file: ' . $data->getFileName() . PHP_EOL;
        $message .= '-- line: ' . $data->getFileLine() . PHP_EOL;
        $message .= '-- message: ' . $data->getMessage() . PHP_EOL;
        $message .= '-- trace: ' . PHP_EOL . $data->getTrace() . PHP_EOL;

        $message .= '###END:' . $data->getCode() . PHP_EOL . PHP_EOL;

        return $message;
    }

    /**
     * @param string $date
     * @return string
     */
    private function defineFilePath($date)
    {
        $dirPath = Mif::getProjectDir() . '/var/log/exceptions/' . $date;
        if (!file_exists($dirPath)) {
            return $dirPath . '/0';
        }

        $fileMaxName = 0;
        $fileNames = scandir($dirPath);
        foreach ($fileNames as $fileName) {
            if ($fileName == '.' || $fileName == '..') {
                continue;
            }

            if ((int)$fileName > $fileMaxName) {
                $fileMaxName = $fileName;
            }
        }

        $fileName = $dirPath . '/' . $fileMaxName;
        if (!file_exists($fileName)) {
            return $fileName;
        }

        if (filesize($fileName) >= self::FILE_SIZE_LIMIT) {
            $fileName = $dirPath . '/' . ($fileMaxName + 1);
        }

        return $fileName;
    }

    /**
     * @param string $filePath
     * @param string $message
     */
    private function putMessage($filePath, $message)
    {
        $dirPath = dirname($filePath);
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        file_put_contents($filePath, $message, FILE_APPEND);
    }
}
