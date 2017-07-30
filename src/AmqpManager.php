<?php

namespace Wuwx\LaravelPlusAmqp;

use AMQPConnection;
use AMQPChannel;
use AMQPExchange;
use AMQPQueue;

class AmqpManager
{
    public static function exchange($name)
    {
        $connection = new AMQPConnection(config('amqp.connections.rabbitmq'));
        $connection->connect();

        $channel = new AMQPChannel($connection);

        $exchange = new AMQPExchange($channel);
        $exchange->setName(config('amqp.exchanges')[$name]['name']);
        $exchange->setType(config('amqp.exchanges')[$name]['type']);
        $exchange->declareExchange();

        return $exchange;
    }
    
    public static function queue($name)
    {
        $connection = new AMQPConnection(config('amqp.connections.rabbitmq'));
        $connection->connect();

        $channel = new AMQPChannel($connection);
 
        $queue = new AMQPQueue($channel);
        $queue->declareQueue();
        $queue->bind('crab.topic', 'devices.*');

        return $queue;
    }

    public static function publish()
    {
        $exchange = self::exchange(config('amqp.defaults.exchange'));
        call_user_func_array([$exchange, "publish"], func_get_args());
    }
    
    public static function consume()
    {
        $queue = self::queue(config('amqp.defaults.queue'));
        call_user_func_array([$queue, "consume"], func_get_args());
    }
}
