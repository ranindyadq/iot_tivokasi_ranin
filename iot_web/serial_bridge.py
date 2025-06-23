import serial
import requests
import json

# Ganti dengan port ESP32 kamu (cek di Device Manager, biasanya COM3 atau COM4)
port = "COM3"
baudrate = 115200
url = "http://192.168.18.48:8001/sensor-data"  # Ganti IP sesuai IP lokal kamu

ser = serial.Serial(port, baudrate, timeout=1)
print("Listening from serial...")

while True:
    line = ser.readline().decode('utf-8').strip()
    if line:
        try:
            data = json.loads(line)
            print("Mengirim:", data)
            response = requests.post(url, json=data)
            print("Respon:", response.status_code, response.text)
        except Exception as e:
            print("Error:", e)
