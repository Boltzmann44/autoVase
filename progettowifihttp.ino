#include <OneWire.h>
#include <DallasTemperature.h>

#include <WiFi.h>
#include <HTTPClient.h>

const char *serverName = "http://172.20.10.14/vaso/script23.php";

const char *ssid_Router = "iPhone di Sasy";
const char *password_Router = "ciaowifi";

unsigned long lastTime = 0;
unsigned long timerDelay = 360000;

int check=0;

//definiamo i pin dei sensori
#define SENS_TEMP 14 
#define SENS_LUM 34
#define SENS_TER 32

//definiamo i pin del sonar
#define ECHO 21
#define TRIG 19

//definiamo il pin del RELAY
#define RELAY 18

//definiamo i pin dei LED
#define LED_ACC 23
#define LED_TEMP 5
#define LED_LUM 22
#define LED_WATER 33


OneWire oneWire(SENS_TEMP); // Imposta la connessione OneWire
DallasTemperature sensoreTemp(&oneWire); // Dichiarazione dell'oggetto sensore

void setup() {
  Serial.begin(115200);       
  delay(2000);
  Serial.println("Setup start");
  WiFi.begin(ssid_Router, password_Router);
  Serial.println(String("Connessione a ")+ssid_Router);
  while(WiFi.status() != WL_CONNECTED){
    delay(500);
    Serial.print (".");
  }
  Serial.println("\nConnesso, Indirizzo IP: ");
  Serial.println(WiFi.localIP());
  Serial.println("Il timer è settato a 6 min. L'invio di dati verrà effettuata ogni 6 min");
  Serial.println("------- Avvio di AutoVase -------");

  //tutti i sensori
  sensoreTemp.begin();    
  pinMode(SENS_LUM,INPUT);  
  pinMode(SENS_TER,INPUT);
  
  //tutti i LED
  pinMode(LED_ACC,OUTPUT);
  pinMode(LED_TEMP,OUTPUT);
  pinMode(LED_LUM,OUTPUT);
  pinMode(LED_WATER,OUTPUT);

  //relay e sonar
  pinMode(RELAY,OUTPUT);
  pinMode(ECHO,INPUT);
  pinMode(TRIG,OUTPUT);

  //accendiamo il led di accensione
  digitalWrite(LED_ACC,HIGH);

  digitalWrite(RELAY,HIGH);
  delay(1000);

}

void loop() {
  //Richiesta valori dai sensori
  sensoreTemp.requestTemperatures(); 
  float temp = sensoreTemp.getTempCByIndex(0);
  int lum = analogRead(SENS_LUM);
  int ter = 4095-analogRead(SENS_TER);
  int humidity = map(ter, 0, 2300, 0, 100);
  //utilizzo sonar
  digitalWrite(TRIG,HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG,LOW);

  int duration = pulseIn(ECHO,HIGH);

  // Calcolo percentuale acqua (con una piccola tolleranza)
  float distance = duration/(54.5);
  float water_level = (9)-distance;
  //Serial.printf("  Distanza: %.1f",distance);
  int water_perc=0;
  int tmp= (water_level/7.5)*100;
  if(!(tmp<0 || tmp>100)){
    water_perc = tmp;}
  else if(tmp<0){
    water_perc =0;}
  else if(tmp>100){
    water_perc =100;}
  
  //Gestione LED
  /*if(temp>tempMax){
    digitalWrite(LED_TEMP,HIGH); 
  }
  else{ 
    digitalWrite(LED_TEMP,LOW);
  }*/
  int light =map(lum, 0, 4095, 0, 100);

  if(light>80){
    digitalWrite(LED_LUM,HIGH); 
  }
  else{
    digitalWrite(LED_LUM,LOW);
  }  

  if(water_perc<40){
    digitalWrite(LED_WATER,HIGH); 
  }
  else{
    digitalWrite(LED_WATER,LOW);
  }

  //Gestione pompa acqua

  if(ter>600){
    digitalWrite(RELAY,HIGH);
    check=1;
  }
  else if(ter<600 && water_perc>25 && check==1){
      digitalWrite(RELAY,LOW);
      delay(2000);
      digitalWrite(RELAY,HIGH);
  }

  //Stampa a video dei valori
  Serial.printf("C: %.1f",temp);
  Serial.printf("  L: %d",light);
  Serial.printf("  Umidità: %d",humidity);
  Serial.printf("  Acqua_perc: %d %\n",water_perc);

  //HTTP post ogni 6 min
  if((millis()-lastTime) > timerDelay){
    if(WiFi.status()==WL_CONNECTED){
      WiFiClient client;
      HTTPClient http;
  
      http.begin(client, serverName);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");
      float provola = 28.0;
      String postData = "temperature=" + String(provola) + "&light=" + String(light) + "&water=" + String(water_perc) + "&humidity=" + String(humidity);
      //String postData = "temperature=" + String(provola) + "&light=" + String(light) + "&water=" + String(water_perc);
      Serial.println("\n" + postData);
      int httpResponseCode = http.POST(postData);
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      http.end();
    }
    else{
      Serial.println("WiFi Disconesso!");
    }
    lastTime= millis();
  }
  
  delay(1000);
}


