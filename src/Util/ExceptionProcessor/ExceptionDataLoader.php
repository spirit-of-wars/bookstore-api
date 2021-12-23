<?php

namespace App\Util\ExceptionProcessor;

use App\Mif;

/**
 * Class ExceptionDataLoader
 * @package App\Util\ExceptionProcessor
 */
class ExceptionDataLoader
{
    /**
     * @param string $code
     * @param string $date
     * @return array
     */
    public function load($code, $date)
    {
        if (!file_exists($this->getExceptionsPath())) {
            return [];
        }

        $text = $this->getExceptionText($code, $date);
        if ($text === null) {
            return [];
        }

        $reg = '/- (date|requestUri|requestParams|file|line|message|trace):(?: |\r\n|\r|\n)?([\w\W]+?)(?:(?:\r\n|\r|\n)-|$)/';
        preg_match_all($reg, $text, $matches);

        $result = [
            'code' => $code,
        ];
        for ($i= 0, $l=count($matches[0]); $i<$l; $i++) {
            $result[$matches[1][$i]] = $matches[2][$i];
        }

        $result['trace'] = preg_split('/(?:\r\n|\r|\n)/', $result['trace']);
        return $result;
    }

    /**
     * @param string $code
     * @param string $date
     * @return string|null
     */
    private function getExceptionText($code, $date)
    {
        $dates = $date ? [$date] : $this->getAllDates();
        $path = $this->getExceptionsPath();

        foreach ($dates as $iDate) {
            $files = array_diff(scandir($path . '/' . $iDate), ['.', '..']);
            usort($files, function ($a, $b) {
                if ($a > $b) return -1;
                if ($a < $b) return 1;
                return 0;
            });

            foreach ($files as $file) {
                $filePath = $path . '/' . $iDate . '/' . $file;
                $content = file_get_contents($filePath);
                $reg = '/###BEGIN:' . $code . '(?:\r\n|\r|\n)([\w\W]+?)(?:\r\n|\r|\n)###END:' . $code . '/';
                preg_match($reg, $content, $matches);
                if (!empty($matches)) {
                    return $matches[1];
                }
            }
        }

        return null;
    }


    /**
     * @return array
     */
    private function getAllDates()
    {
        $path = $this->getExceptionsPath();
        $dates = array_diff(scandir($path), ['.', '..']);
        usort($dates, function ($a, $b) {
            if ($a > $b) return -1;
            if ($a < $b) return 1;
            return 0;
        });
        return $dates;
    }

    /**
     * @return string
     */
    private function getExceptionsPath()
    {
        return Mif::getProjectDir() . '/var/log/exceptions';
    }
}
