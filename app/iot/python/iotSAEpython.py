import paho.mqtt.client as mqtt
import json
import configparser
import os
import datetime

donneesSolaire = []
#global donne
donne =[]
salles = []

def read_config(chemin):
    config = configparser.ConfigParser()
    config.read(chemin)
    return config

def load_json_file(fichier):
    if os.path.exists(fichier):
        with open(fichier, "r",encoding="utf-8") as file:
            return json.load(file)
    return {"Capteurs AM":  {}, "Panneaux": {}}

def filtredonneesSolaire(data):
    di2 ={}
    
    for k, v in data.items():
        if k in donneesSolaire:
            di2[k] = v
    jsonfile = load_json_file(fichjson)

    if "Panneaux-Solaire" in jsonfile["Panneaux"]:
        jsonfile["Panneaux"]["Panneaux-Solaire"].append(di2)
    else :
        jsonfile["Panneaux"]["Panneaux-Solaire"] = [di2]

    with open(fichjson, "w",encoding="utf-8") as file:
        json.dump(jsonfile, file,ensure_ascii=False,indent=4)
    print(di2)


def filtredonneesCapteur(data):
    alert=False
    di2 = {}
    for di in data :
        for k, v in di.items():
                if k in donne:
                    di2[k]= v
                if k == 'room':
                    salle = v


    jour = datetime.datetime.now().strftime("%d-%m-%Y")
    heure = datetime.datetime.now().strftime("%H:%M:%S")

    if "temperature" in di2 and di2["temperature"] > s_temperature:
        jsonfile = load_json_file(alertjson)
        di2["jour"] = jour
        di2["heure"] = heure
        di2["room"] = salle

        if "temperatureAlert" not in jsonfile["Capteurs AM"]:
            jsonfile["Capteurs AM"]["temperatureAlert"] = {}
        if salle in jsonfile["Capteurs AM"]["temperatureAlert"] :
            jsonfile["Capteurs AM"]["temperatureAlert"][salle].append(di2)
        else:
            jsonfile["Capteurs AM"]["temperatureAlert"][salle] = [di2]
        alert=True
            
    if "co2" in di2 and di2["co2"] > s_co2:
        jsonfile = load_json_file(alertjson)
        di2["jour"] = jour
        di2["heure"] = heure
        di2["room"] = salle

        if "co2Alert" not in jsonfile["Capteurs AM"]:
            jsonfile["Capteurs AM"]["co2Alert"] = {}
        if salle in jsonfile["Capteurs AM"]["co2Alert"]:
            jsonfile["Capteurs AM"]["co2Alert"][salle].append(di2)
        else:
            jsonfile["Capteurs AM"]["co2Alert"][salle] = [di2]

        alert = True


    if "humidity" in di2 and di2["humidity"] > s_humidite:
        jsonfile = load_json_file(alertjson)
        di2["jour"] = jour
        di2["heure"] = heure
        di2["room"] = salle

        if "humiditeAlert" not in jsonfile["Capteurs AM"]:
            jsonfile["Capteurs AM"]["humiditeAlert"] = {}
        if salle in jsonfile["Capteurs AM"]["humiditeAlert"] : 
            jsonfile["Capteurs AM"]["humiditeAlert"][salle].append(di2)
        else:
            jsonfile["Capteurs AM"]["humiditeAlert"][salle] = [di2]

        alert=True
    
    if alert : 
        with open(alertjson, "w",encoding="utf-8") as file:
            json.dump(jsonfile, file,ensure_ascii=False,indent=4)
    else:
        jsonfile = load_json_file(fichjson)
    
        if salle in jsonfile["Capteurs AM"]:
            jsonfile["Capteurs AM"][salle].append(di2)
        else :
            jsonfile["Capteurs AM"][salle] = [di2]

        with open(fichjson, "w",encoding="utf-8") as file:
            json.dump(jsonfile, file,ensure_ascii=False,indent=4)
    print(di2)


def on_message(mqttc, obj, msg):
    data = json.loads(msg.payload.decode('utf-8')) 
    print(f"Message reçu sur {msg.topic}: {data}") 

    if 'AM' in (msg.topic):
        filtredonneesCapteur(data)
    if 'solaredge' in (msg.topic):
        filtredonneesSolaire(data)


def on_connect(client, userdata, flags, reason_code, properties=None):
    print(f"Connected to broker with {reason_code}")
    for topic in topic_subscribe:
        if 'AM107' in topic:
            for salle in salles:
                mqttc.subscribe(topic.strip()+salle+'/data')
                print(topic.strip()+salle+'/data')
        else :
            mqttc.subscribe(topic.strip())
            


fichconfig = read_config("config.ini")

mqttServer = fichconfig['MQTT']['broker']
port = int(fichconfig['MQTT']['port'])
topic_subscribe = fichconfig['MQTT']['topic_subscribe'].split(',')

donne = fichconfig['DATA']['types'].split(',')
donneesSolaire = fichconfig['DATA']['typesSolaire'].split(',')
salles = fichconfig['DATA']['salles'].split(',')
fichjson = fichconfig['OUTPUT']['json_file']
alertjson = fichconfig['OUTPUT']['alert_file']
print(alertjson)

s_temperature = int(fichconfig['DATA']['threshold_temperature'])
s_co2 = int(fichconfig['DATA']['threshold_co2'])
s_humidite = int(fichconfig['DATA']['threshold_humidity'])




if salles[0] == '' or salles[0].upper() =='ALL':
    salles[0] = '+' 


mqttc = mqtt.Client()
mqttc.connect(mqttServer, port, keepalive=60)
mqttc.on_connect = on_connect
mqttc.on_message = on_message
mqttc.loop_forever()
