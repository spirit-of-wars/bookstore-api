<?php

use App\EntitySupport\OrmType\DictionaryType;
use App\EntitySupport\OrmType\ListType;
use App\Validation\ConstraintCore;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Dotenv\Dotenv;
use Doctrine\DBAL\Types\Type;

require dirname(__DIR__).'/vendor/autoload.php';

if (is_array($env = @include dirname(__DIR__).'/.env.php')) {
    foreach ($env as $k => $v) {
        $_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
    }
}

if (is_array($env = @include dirname(__DIR__).'/.env.local.php')
    && (!isset($env['APP_ENV']) || ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? $env['APP_ENV']) === $env['APP_ENV'])
) {
    foreach ($env as $k => $v) {
        $_ENV[$k] = $_ENV[$k] ?? (isset($_SERVER[$k]) && 0 !== strpos($k, 'HTTP_') ? $_SERVER[$k] : $v);
    }
} elseif (!class_exists(Dotenv::class)) {
    throw new RuntimeException(
        'Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.'
    );
} else {
    // load all the .env files
    (new Dotenv(false))->loadEnv(dirname(__DIR__).'/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] =
    (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';

try {
    Type::addType('list', ListType::class);
    Type::addType('dict', DictionaryType::class);
} catch (DBALException $exception) {
    throw new RuntimeException('Please fix custom types');
}

ConstraintCore::init();
