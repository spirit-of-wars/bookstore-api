<?php

namespace App\Util\ConsoleCommandExecutor\Swagger;

use App\Mif;
use App\Util\Task\TaskExecutor;

/**
 * Class SwaggerExecutor
 * @package App\Util\ConsoleCommandExecutor\Swagger
 */
class SwaggerExecutor extends TaskExecutor
{
    /**
     * @return bool
     */
    public function renewHeader()
    {
        $data = [
            'openapi' => SwaggerConstants::VERSION,
            'info' => [
                'title' => SwaggerConstants::TITLE,
                'version' => Mif::getEnvConfig('APP_SWAGGER_VERSION'),
                'description' => SwaggerConstants::getDescription(),
            ],
            'servers' => Mif::getEnvConfig('APP_SWAGGER_SERVERS'),
        ];

        $swaggerHeaderDirPath = $this->getSwaggerHeaderDirPath();
        if (!file_exists($swaggerHeaderDirPath)) {
            mkdir($swaggerHeaderDirPath, 0777, true);
        }

        $fileName = $swaggerHeaderDirPath . '/' . SwaggerConstants::HEADER_FILE_NAME;
        file_put_contents($fileName, json_encode($data, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * @return \Generator
     */
    public function renew()
    {
        $this->taskChain->init([
            'renew' => new RenewDocTask()
        ]);
        return $this->run();
    }

    /**
     * @return string
     */
    private function getSwaggerHeaderDirPath()
    {
        return Mif::getProjectDir() . SwaggerConstants::HEADER_DIR_PATH;
    }
}
