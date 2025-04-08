#define BLYNK_PRINT Serial

#define BLYNK_TEMPLATE_ID "TMPL6LeBABv0_"
#define BLYNK_TEMPLATE_NAME "ESP32 IoT"
#define BLYNK_AUTH_TOKEN "eiXDRbUq7bEEADp1V1yjinV619U9yTJm"

#include <WiFi.h>
#include <BlynkSimpleEsp32.h>
#include <DHTesp.h>

char auth[] = BLYNK_AUTH_TOKEN;
char ssid[] = "Wokwi-GUEST";
char pass[] = "";

const int DHT_PIN = 15;
const byte LED_R = 26;

DHTesp dht;
BlynkTimer timer;

// Kirim data suhu & kelembapan ke Blynk
void sendSensor() {
  TempAndHumidity data = dht.getTempAndHumidity();

  Serial.print("Suhu: ");
  Serial.print(data.temperature);
  Serial.println(" °C");

  Serial.print("Kelembapan: ");
  Serial.print(data.humidity);
  Serial.println(" %");

  Blynk.virtualWrite(V0, data.temperature);
  Blynk.virtualWrite(V1, data.humidity);

  // Notifikasi jika suhu terlalu tinggi
  if (data.temperature > 40) {
    Blynk.logEvent("suhu_tinggi", "Suhu melebihi 40°C!");
  }
}

// Kontrol LED Merah dari Blynk
BLYNK_WRITE(V2) {
  int nilai = param.asInt();
  digitalWrite(LED_R, nilai);
  Blynk.virtualWrite(V3, nilai); // Kirim status LED ke V3
}

void setup() {
  Serial.begin(115200);
  dht.setup(DHT_PIN, DHTesp::DHT22);

  pinMode(LED_R, OUTPUT);

  Blynk.begin(auth, ssid, pass);
  timer.setInterval(1000L, sendSensor); // Update setiap 1 detik
}

void loop() {
  Blynk.run();
  timer.run();
}
