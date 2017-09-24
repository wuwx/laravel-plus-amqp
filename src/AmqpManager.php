<?php

namespace Wuwx\LaravelPlusAmqp;

use AMQPConnection;
use AMQPChannel;
use AMQPExchange;
use AMQPQueue;

class AmqpManager
{
    private $_config = [];
    private $_connections = [];
    private $_exchanges = [];
    private $_queues = [];

    public function __construct($config = [])
    {
        $this->_config = $config;
    }

    public function connection($name = '')
    {
        $name = $name ?: array_get($this->_config, 'defaults.connection');
        if (!isset($this->_connections[$name])) {
            $connection = new AMQPConnection(array_get($this->_config, "connections.$name"));
            $connection->connect();
            $this->_connections[$name] = $connection;
        }
        return $this->_connections[$name];
    }

    public function exchange($name = '')
    {
        $name = $name ?: array_get($this->_config, 'defaults.exchange');
        if (!isset($this->_exchanges[$name])) {
            $connection = $this->connection(array_get($this->_config, "exchanges.$name.connection"));
            $channel = new AMQPChannel($connection);
            $exchange = new AMQPExchange($channel);
            $exchange->setName(array_get($this->_config, "exchanges.$name.name"));
            $exchange->setType(array_get($this->_config, "exchanges.$name.type"));
            $exchange->setFlags(array_get($this->_config, "exchanges.$name.flags"));
            $exchange->declareExchange();
            $this->_exchanges[$name] = $exchange;
        }
        return $this->_exchanges[$name];
    }

    public function queue($name = '')
    {
        $name = $name ?: array_get($this->_config, 'defaults.queue');
        if (!isset($this->_queues[$name])) {
            $connection = $this->connection(array_get($this->_config, "queues.$name.connection"));
            $channel = new AMQPChannel($connection);
            $queue = new AMQPQueue($channel);
            $queue->declareQueue();
            foreach(array_get($this->_config, "queues.$name.bindings") as $binding) {
                call_user_func_array([$queue, "bind"], $binding);
            }
            $this->_queues[$name] = $queue;
        }
        return $this->_queues[$name];
    }

    public function publish()
    {
        $exchange = $this->exchange();
        call_user_func_array([$exchange, "publish"], func_get_args());
    }

    public function consume()
    {
        $queue = $this->queue();
        call_user_func_array([$queue, "consume"], func_get_args());
    }
}
