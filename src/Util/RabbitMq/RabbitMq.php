<?php

namespace App\Util\RabbitMq;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use App\Mif;
use Exception;

class RabbitMq
{
    private static ?AMQPStreamConnection $amqpConnection = null;

    private static array $instances = [];

    protected function __construct() { }

    protected function __clone() { }

    public function __wakeup()
    {
        throw new Exception('error');
    }

    /**
     * @return RabbitMq
     */
    public static function getInstances() : RabbitMq
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
            self::connectionAmqp();
        }

        return self::$instances[$cls];
    }

    /**
     * @return AMQPStreamConnection
     */
    public static function connectionAmqp() : AMQPStreamConnection
    {
        if (is_null(self::$amqpConnection)) {
            self::$amqpConnection = new AMQPStreamConnection(
                Mif::getEnvConfig('AMQP_HOST'),
                Mif::getEnvConfig('AMQP_PORT'),
                Mif::getEnvConfig('AMQP_USER'),
                Mif::getEnvConfig('AMQP_PASS')
            );
        }

        return self::$amqpConnection;
    }

    /**
     * @param string $queueName
     * @param string $message
     * @return bool
     */
    public function setMessage(string $queueName, string $message) : bool
    {
        $status = false;
        $listChannel = Mif::getEnvConfig('AMQP_CHANNEL');
        $channel = self::$amqpConnection->channel();
        if (isset($listChannel[$queueName])) {
            $channel->queue_declare(
                $listChannel[$queueName],
                false,
                false,
                false,
                false
            );

            $msg = new AMQPMessage($message, ['delivery_mode' => 2]);
            $channel->basic_publish($msg, '', $listChannel[$queueName]);
            $channel->close();
            $status = true;
        }

        return $status;
    }
}
