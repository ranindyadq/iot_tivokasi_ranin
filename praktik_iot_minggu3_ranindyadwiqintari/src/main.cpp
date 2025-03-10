#include <Wire.h>                // Library untuk komunikasi I2C
#include <LiquidCrystal_I2C.h>   // Library LCD I2C
#include <DHT.h>                 // Library untuk sensor DHT22

// Definisi pin berdasarkan diagram.json
#define DHTPIN 4         // Pin untuk sensor DHT22
#define DHTTYPE DHT22    // Tipe sensor DHT22
#define LDRPIN 32        // Pin untuk sensor LDR

DHT dht(DHTPIN, DHTTYPE);
LiquidCrystal_I2C lcd(0x27, 20, 4);  // Alamat I2C LCD (coba ubah ke 0x3F jika tidak berfungsi)

void setup() {
    Serial.begin(9600);  // Inisialisasi komunikasi serial
    dht.begin();           // Mulai sensor DHT22
    lcd.init();            // Inisialisasi LCD
    lcd.backlight();       // Aktifkan lampu latar LCD
}

void loop() {
    float temperature = dht.readTemperature();  // Membaca suhu dalam Celsius
    float humidity = dht.readHumidity();        // Membaca kelembapan
    int lightValue = analogRead(LDRPIN);        // Membaca nilai dari LDR
    float lightIntensity = map(lightValue, 0, 4095, 0, 100);  // Konversi ke persen

    // Tampilkan di Serial Monitor
    Serial.print("Suhu: "); Serial.print(temperature); Serial.println(" C");
    Serial.print("Kelembapan: "); Serial.print(humidity); Serial.println(" %");
    Serial.print("Cahaya: "); Serial.print(lightIntensity); Serial.println(" %");

    // Tampilkan di LCD
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("Suhu : "); lcd.print(temperature, 1); lcd.print(" C");

    lcd.setCursor(0, 1);
    lcd.print("Kelembapan : "); lcd.print(humidity, 1); lcd.print(" %");

    lcd.setCursor(0, 2);
    lcd.print("Intensitas");
    lcd.setCursor(0, 3);
    lcd.print("Cahaya : "); lcd.print(lightIntensity, 1); lcd.print(" %");

    delay(1000);  // Update setiap 1 detik
}

