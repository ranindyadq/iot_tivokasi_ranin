#define BLYNK_TEMPLATE_ID "TMPL6dvAB55MP"
#define BLYNK_TEMPLATE_NAME "ESP32 IoT UTS"
#define BLYNK_AUTH_TOKEN "MfMmBSlt1STlUFyuFwnffvrYtRKDs6Q8"

#include <WiFi.h>
#include <BlynkSimpleEsp32.h>
#include "DHT.h"

#define DHTPIN 15         
#define DHTTYPE DHT22     
#define MQ2PIN 34         

char ssid[] = "Wokwi-GUEST";
char pass[] = "";

DHT dht(DHTPIN, DHTTYPE);
BlynkTimer timer;

void sendSensorData() {
  float temp = dht.readTemperature();
  float hum = dht.readHumidity();
  int gasValue = analogRead(MQ2PIN);

  if (isnan(temp) || isnan(hum)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }

  // Kirim ke Blynk
  Blynk.virtualWrite(V0, temp);     // suhu
  Blynk.virtualWrite(V1, hum);      // kelembaban
  Blynk.virtualWrite(V2, gasValue); // gas/asap

  // Tampilkan ke Serial Monitor
  Serial.print("Suhu: ");
  Serial.print(temp);
  Serial.print(" °C | Kelembapan: ");
  Serial.print(hum);
  Serial.print(" % | Asap: ");
  Serial.println(gasValue);
}

void setup() {
  Serial.begin(115200);
  dht.begin();
  Blynk.begin(BLYNK_AUTH_TOKEN, ssid, pass);
  timer.setInterval(2000L, sendSensorData);
}

void loop() {
  Blynk.run();
  timer.run();
}