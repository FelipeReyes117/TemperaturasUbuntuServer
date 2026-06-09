#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT.h>

const char* ssid      = "UADEO-ESP32";
const char* password  = "isof2026";
const char* serverURL = "http://192.168.1.207/user23060301/Temperaturas/recibe.php";
const char* correo    = "reyesgodinezfelipedejesus@gmail.com";
const char* contrasena = "2204255f187e7d7217dad90f468ab07d";
const int   lugar     = 1;

#define DHTPIN 2
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

void enviarDatos(float temperatura, int lugar, String correo, String contrasena)
{
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Sin conexión WiFi");
    return;
  }
  HTTPClient http;
  http.begin(serverURL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String body = "temp=" + String(temperatura) +
                "&Ubi=" + String(lugar) +
                "&cor=" + correo +
                "&pass=" + contrasena;

  int httpCode = http.POST(body);
  if (httpCode > 0) {
    Serial.println("Respuesta: " + http.getString());
  } else {
    Serial.println("Error HTTP: " + String(httpCode));
  }
  http.end();
}

void setup()
{
  Serial.begin(115200);
  delay(1000);
  dht.begin();
  WiFi.begin(ssid, password);
  Serial.print("Conectando");
  int intentos = 0;
  while (WiFi.status() != WL_CONNECTED && intentos < 20) {
    delay(500);
    Serial.print(".");
    intentos++;
  }
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("No se pudo conectar");
    return;
  }
  Serial.println("\nConectado!");
  Serial.print("IP del ESP32: ");
  Serial.println(WiFi.localIP());
}

void loop()
{
  float temperatura = dht.readTemperature();
  if (isnan(temperatura)) {
    Serial.println("Error al leer DHT11");
  } else {
    Serial.print("Temperatura: ");
    Serial.print(temperatura);
    Serial.println(" °C");
    enviarDatos(temperatura, lugar, correo, contrasena);
  }
  delay(10000);
}
