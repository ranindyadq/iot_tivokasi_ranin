<?php

namespace App\Http\Controllers;

use App\Events\SensorUpdated;
use App\Models\Log;
use Salman\Mqtt\MqttClass\Mqtt;
use Illuminate\Support\Facades\Log as Logger;

class MqttController extends Controller
{
    public function publishTest()
    {
        $mqtt = new Mqtt();
        $topic = 'ranindya/iot/sensor/data';

        // Generate data sensor acak
        $sensorData = [
            'suhu' => rand(20, 40),
            'kelembapan' => rand(30, 90),
            'gas' => rand(0, 2500),
            'timestamp' => now()->toDateTimeString() // Tambahkan timestamp
        ];

        $message = json_encode($sensorData);

        try {
            // Publish ke broker MQTT
            $mqtt->ConnectAndPublish($topic, $message);

            // Simpan ke database
            $log = Log::create([
                'suhu' => $sensorData['suhu'],
                'kelembapan' => $sensorData['kelembapan'],
                'gas' => $sensorData['gas'],
                'status' => $this->determineStatus($sensorData)
            ]);

            // Trigger event real-time
            event(new SensorUpdated([
                'suhu' => $sensorData['suhu'],
                'kelembapan' => $sensorData['kelembapan'],
                'gas' => $sensorData['gas'],
                'status' => $log->status,
                'timestamp' => $sensorData['timestamp']
            ]));

            Logger::info('MQTT Message Published', [
                'topic' => $topic,
                'data' => $sensorData
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data published and saved',
                'data' => $sensorData
            ]);
        } catch (\Exception $e) {
            Logger::error('MQTT Publish Failed', [
                'error' => $e->getMessage(),
                'topic' => $topic
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Publish failed: ' . $e->getMessage()
            ], 500);
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


    public function subscribeToTopic()
    {
        $mqtt = new Mqtt();
        $topic = 'ranindya/iot/sensor/data';

        $mqtt->ConnectAndSubscribe($topic, function ($topic, $message) {
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

                // Trigger event real-time
                event(new SensorUpdated([
                    'suhu' => $data['suhu'],
                    'kelembapan' => $data['kelembapan'],
                    'gas' => $data['gas'],
                    'status' => $log->status,
                    'timestamp' => now()->toDateTimeString()
                ]));

                Logger::info('MQTT Message Received', [
                    'topic' => $topic,
                    'data' => $data
                ]);
            } catch (\Exception $e) {
                Logger::error('MQTT Processing Error', [
                    'topic' => $topic,
                    'error' => $e->getMessage(),
                    'message' => $message
                ]);
            }
        });

        return response()->json(['status' => 'Subscribed to topic']);
    }
}
