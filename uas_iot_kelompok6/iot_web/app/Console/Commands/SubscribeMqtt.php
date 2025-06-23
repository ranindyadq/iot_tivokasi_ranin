<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Events\SensorUpdated;
use PhpMqtt\Client\MqttClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log as Logger;
use PhpMqtt\Client\Exceptions\MqttClientException;

class SubscribeMqtt extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT topics';

    public function handle()
    {
        $this->info('MQTT Subscriber started. Press Ctrl+C to stop.');

        try {
            $mqtt = new MqttClient('broker.emqx.io', 1883);
            $mqtt->connect(null, true);

            $mqtt->subscribe('ranindya/iot/sensor/data', function ($topic, $message) {
                try {
                    $data = json_decode($message, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new \Exception('Invalid JSON format');
                    }

                    // Simpan ke database
                    $log = Log::create([
                        'suhu' => $data['suhu'],
                        'kelembapan' => $data['kelembapan'],
                        'gas' => $data['gas'],
                        'status' => $this->determineStatus($data)
                    ]);

                    Logger::info('MQTT Message Received', [
                        'topic' => $topic,
                        'data' => $data
                    ]);

                    $this->info("Received: " . json_encode($data));

                    // Trigger event real-time
                    event(new SensorUpdated($log));
                } catch (\Exception $e) {
                    Logger::error('MQTT Processing Error', [
                        'error' => $e->getMessage(),
                        'message' => $message
                    ]);
                    $this->error("Error: " . $e->getMessage());
                }
            }, 0); // QoS level 0

            $mqtt->loop(true);
        } catch (MqttClientException $e) {
            $this->error("MQTT Connection Error: " . $e->getMessage());
        }
    }

    private function determineStatus($data)
    {
        $status = [];

        if ($data['gas'] > 1000) $status[] = 'MERAH';
        if ($data['suhu'] > 27) $status[] = 'KUNING';
        if ($data['kelembapan'] > 80) $status[] = 'HIJAU';

        return count($status) > 0 ? implode(' ', $status) : 'NORMAL';
    }
}
