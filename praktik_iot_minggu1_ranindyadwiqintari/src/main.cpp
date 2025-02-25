#include <Arduino.h>

// put function declarations here:
int ledm = 18;
int ledk = 16;
int ledh = 17;


void setup() {
  // put your setup code here, to run once:
  Serial.begin(115200);
  pinMode(ledm, OUTPUT);
  pinMode(ledk, OUTPUT);
  pinMode(ledh, OUTPUT);
}

void loop() {
  // put your main code here, to run repeatedly:
digitalWrite(ledm, HIGH);
digitalWrite(ledk, LOW);
digitalWrite(ledh, LOW);
Serial.println("Merah Menyala");
delay(30000);

digitalWrite(ledm, LOW);
digitalWrite(ledk, HIGH);
digitalWrite(ledh, LOW);
Serial.println("Kuning Menyala");
delay(5000);

digitalWrite(ledm, LOW);
digitalWrite(ledk, LOW);
digitalWrite(ledh, HIGH);
Serial.println("Hijau Menyala");
delay(20000);
}