#include <WiFi.h>
#include <PubSubClient.h>
#include <DHT.h>
#include <ArduinoJson.h>
#include <Wire.h>
#include <U8g2lib.h>

#define DHTPIN 4
#define DHTTYPE DHT22
DHT dht(DHTPIN, DHTTYPE);

const int mq2Pin = 34;
const int ledMerah = 5;
const int ledKuning = 18;
const int ledHijau = 19;

const char* ssid = "Galaxy";
const char* password = "ranin124";
const char* mqtt_server = "broker.emqx.io";
const char* mqtt_topic = "ranindya/iot/sensor/data";

WiFiClient espClient;
PubSubClient client(espClient);

U8G2_SH1106_128X64_NONAME_F_HW_I2C display(U8G2_R0, /* reset=*/ U8X8_PIN_NONE);

void setup_wifi();
String determineStatus(float suhu, float kelembapan, int gas);
void updateLEDs(String status);
String createPayload(float suhu, float kelembapan, int gas, String status);
void reconnect();

void setup() {
  Serial.begin(115200);
  dht.begin();

  pinMode(mq2Pin, INPUT);
  pinMode(ledMerah, OUTPUT);
  pinMode(ledKuning, OUTPUT);
  pinMode(ledHijau, OUTPUT);

  digitalWrite(ledMerah, LOW);
  digitalWrite(ledKuning, LOW);
  digitalWrite(ledHijau, LOW);

  Wire.begin(21, 22); // SCL, SDA
  display.begin();
  display.clearBuffer();
  display.setFont(u8g2_font_6x10_tf);
  display.drawStr(0, 10, "Monitoring Start");
  display.sendBuffer();

  setup_wifi();

  client.setServer(mqtt_server, 1883);
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();

  float suhu = dht.readTemperature();
  float kelembapan = dht.readHumidity();
  int gasValue = analogRead(mq2Pin);

  if (isnan(suhu) || isnan(kelembapan)) {
    Serial.println("Failed to read from DHT sensor!");
    return;
  }

  String status = determineStatus(suhu, kelembapan, gasValue);
  updateLEDs(status);

  String payload = createPayload(suhu, kelembapan, gasValue, status);
  client.publish(mqtt_topic, payload.c_str());

  Serial.println(payload);

  // OLED Display
  display.clearBuffer();
  display.setFont(u8g2_font_6x10_tf);
  display.setCursor(0, 10); display.print("Data Sensor");
  display.setCursor(0, 22); display.print("Status: "); display.print(status);
  display.setCursor(0, 34); display.print("Suhu  : "); display.print(suhu, 1); display.print(" C");
  display.setCursor(0, 46); display.print("Hum   : "); display.print((int)kelembapan); display.print(" %");
  display.setCursor(0, 58); display.print("Gas   : "); display.print(gasValue);
  display.sendBuffer();

  delay(5000); // jeda 5 detik
}

void setup_wifi() {
  delay(10);
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("WiFi connected");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}

void reconnect() {
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    if (client.connect("ESP32Client")) {
      Serial.println("connected");
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      Serial.println(" try again in 5 seconds");
      delay(5000);
    }
  }
}

String determineStatus(float suhu, float kelembapan, int gas) {
  String status = "";
  if (gas > 1000) status += "MERAH ";
  if (suhu > 23) status += "KUNING ";
  if (kelembapan > 80) status += "HIJAU ";
  if (status == "") status = "NORMAL";
  return status;
}

void updateLEDs(String status) {
  digitalWrite(ledMerah, status.indexOf("MERAH") >= 0 ? HIGH : LOW);
  digitalWrite(ledKuning, status.indexOf("KUNING") >= 0 ? HIGH : LOW);
  digitalWrite(ledHijau, status.indexOf("HIJAU") >= 0 ? HIGH : LOW);
}

String createPayload(float suhu, float kelembapan, int gas, String status) {
  ArduinoJson::StaticJsonDocument<256> doc;
  doc["suhu"] = suhu;
  doc["kelembapan"] = kelembapan;
  doc["gas"] = gas;
  doc["status"] = status;

  String payload;
  ArduinoJson::serializeJson(doc, payload);
  return payload;
}