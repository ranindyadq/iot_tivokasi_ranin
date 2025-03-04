#include <Arduino.h>

// Pin tombol
const int tombol1 = 4;
const int tombol2 = 5;
const int tombol3 = 18;

// Pin LED
const int lampuMerah = 26;
const int lampuKuning = 33;
const int lampuHijau = 32;

void setup() {
  Serial.begin(115200);
  pinMode(tombol1, INPUT_PULLUP);
  pinMode(tombol2, INPUT_PULLUP);
  pinMode(tombol3, INPUT_PULLUP);
  pinMode(lampuMerah, OUTPUT);
  pinMode(lampuKuning, OUTPUT);
  pinMode(lampuHijau, OUTPUT);
}

void loop() {
  // Tombol 1 ditekan → Lampu merah kedip 5x
  if (digitalRead(tombol1) == LOW) {
    Serial.println("Tombol 1 ditekan - Lampu Merah Kedip 5x");
    for (int i = 0; i < 5; i++) {
      digitalWrite(lampuMerah, HIGH);
      delay(500);
      digitalWrite(lampuMerah, LOW);
      delay(500);
    }
  }

  // Tombol 2 ditekan → Lampu merah dan hijau kedip bergantian
  if (digitalRead(tombol2) == LOW) {
    Serial.println("Tombol 2 ditekan - Lampu Merah & Hijau Kedip Bergantian");
    for (int i = 0; i < 5; i++) {
      digitalWrite(lampuMerah, HIGH);
      digitalWrite(lampuHijau, LOW);
      delay(500);
      digitalWrite(lampuMerah, LOW);
      digitalWrite(lampuHijau, HIGH);
      delay(500);
    }
    digitalWrite(lampuHijau, LOW);
  }

  // Tombol 3 ditekan → Lampu merah, kuning, hijau kedip bergantian
  if (digitalRead(tombol3) == LOW) {
    Serial.println("Tombol 3 ditekan - Lampu Merah, Kuning, Hijau Kedip Bergantian");
    for (int i = 0; i < 5; i++) {
      digitalWrite(lampuMerah, HIGH);
      delay(300);
      digitalWrite(lampuMerah, LOW);
      digitalWrite(lampuKuning, HIGH);
      delay(300);
      digitalWrite(lampuKuning, LOW);
      digitalWrite(lampuHijau, HIGH);
      delay(300);
      digitalWrite(lampuHijau, LOW);
    }
  }
}
