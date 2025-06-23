<?php

namespace App\Services;

use Bluerhinos\phpMQTT;
use Exception;

class MqttSubscriber
{
    private $mqtt;

    public function __construct()
    {
        $this->mqtt = new phpMQTT(
            env('MQTT_HOST', 'broker.emqx.io'),
            env('MQTT_PORT', 1883),
            env('MQTT_CLIENT_ID', 'laravel_sub_'.rand(1000,9999))
        );
    }

    public function subscribe(callable $callback)
    {
        if ($this->mqtt->connect()) {
            // Format yang benar untuk subscribe:
            $topics = [
                'ranindya/iot/sensor/data' => [
                    'qos' => 0,
                    'function' => $callback
                ]
            ];

            $this->mqtt->subscribe($topics, 0);

            $this->runMessageLoop();
        } else {
            throw new Exception("Failed to connect to MQTT broker");
        }
    }

    protected function runMessageLoop()
    {
        while ($this->mqtt->proc()) {
            // Tambahkan delay untuk mengurangi CPU usage
            usleep(100000); // 100ms
        }

        $this->mqtt->close();
    }
}
