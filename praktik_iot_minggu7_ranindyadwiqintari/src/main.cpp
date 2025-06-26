#include <WiFi.h>
#include <PubSubClient.h>
#include <DHTesp.h>

const int LED_RED = 2;      // Pin LED (PWM)
const int DHT_PIN = 15;     // Pin DHT22
DHTesp dht;

const char* ssid = "Wokwi-GUEST";
const char* password = "";
const char* mqtt_server = "broker.emqx.io";

WiFiClient espClient;
PubSubClient client(espClient);

unsigned long lastMsg = 0;
int pwmValue = 0;

const int pwmChannel = 0;
const int freq = 5000;
const int resolution = 8;

void setup_wifi() {
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(300);
    Serial.print(".");
  }
  Serial.println("\nWiFi connected!");
}

void callback(char* topic, byte* payload, unsigned int length) {
  Serial.print("Message arrived on topic: ");
  Serial.println(topic);

  String msg;
  for (unsigned int i = 0; i < length; i++) {
    msg += (char)payload[i];
  }
  Serial.print("Payload: ");
  Serial.println(msg);

  if (String(topic) == "IOT/mqtt") {
    pwmValue = msg.toInt();                 // Ubah ke integer
    pwmValue = constrain(pwmValue, 0, 255); // Pastikan dalam range

    ledcWrite(pwmChannel, 255 - pwmValue);

    if (pwmValue > 128) {
      Serial.println("LED: ON (PWM tinggi)");
    } else {
      Serial.println("LED: OFF (PWM rendah)");
    }

    Serial.print("PWM Value: ");
    Serial.println(pwmValue);
  }
}

void reconnect() {
  while (!client.connected()) {
    Serial.print("Attempting MQTT connection...");
    String clientId = "esp32-client-" + String(random(0xffff), HEX);
    if (client.connect(clientId.c_str())) {
      Serial.println("Connected to MQTT broker");
      client.subscribe("IOT/mqtt");  // Topic kontrol LED
    } else {
      Serial.print("failed, rc=");
      Serial.print(client.state());
      delay(2000);
    }
  }
}

void setup() {
  Serial.begin(115200);
  setup_wifi();

  // Setup PWM untuk LED
  ledcSetup(pwmChannel, freq, resolution);
  ledcAttachPin(LED_RED, pwmChannel);

  // Inisialisasi sensor DHT
  dht.setup(DHT_PIN, DHTesp::DHT22);

  // Setup MQTT
  client.setServer(mqtt_server, 1883);
  client.setCallback(callback);
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();

  unsigned long now = millis();
  if (now - lastMsg > 3000) {
    lastMsg = now;

    TempAndHumidity data = dht.getTempAndHumidity();

    String tempStr = String(data.temperature, 2);
    String humStr = String(data.humidity, 1);

    client.publish("IOT/temp", tempStr.c_str());
    client.publish("IOT/hum", humStr.c_str());

    Serial.println("Data sent:");
    Serial.println("Temp: " + tempStr);
    Serial.println("Hum: " + humStr);
  }
}
