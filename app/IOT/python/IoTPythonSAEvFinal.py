import paho.mqtt.client as mqtt
import json
import configparser
import os
import datetime
import time


def read_config(chemin):
    config = configparser.ConfigParser()
    config.read(chemin)
    return config


def load_json_file(fichier)->dict:
    if os.path.exists(fichier):
        with open(fichier, "r",encoding="utf-8") as file:
            return json.load(file)
    return {"Capteurs AM":  {}, "PanneauSolaire": []}

def filtredonneesSolaire(data):
    alert=False
    di2 ={}
    
    for k, v in data.items():
        if k in donneesSolaire:
            di2[k] = v

    jour = datetime.datetime.now().strftime("%d-%m-%Y")
    heure = datetime.datetime.now().strftime("%H:%M:%S")

    if ("currentPower" in di2 and di2["currentPower"]["power"] > s_power) or ("lastDayData" in di2 and di2["lastDayData"]["energy"] > s_energy):
        alert=True

    if alert : 
        di2["jour"] = jour
        di2["heure"] = heure

        if "PanneauSolaire" in dictalert:
            dictalert["PanneauSolaire"].append(di2)
        else :
            dictalert["PanneauSolaire"] = [di2]
    else:
        if "PanneauSolaire" in dictfreq:
            dictfreq["PanneauSolaire"].append(di2)
        else :
            dictfreq["PanneauSolaire"] = [di2]

   



def filtredonneesCapteur(data):
    alert=False
    di2 = {}
    for di in data :
        for k, v in di.items():
                if k in donnees:
                    di2[k]= v
                if k == 'room':
                    salle = v

    jour = datetime.datetime.now().strftime("%d-%m-%Y")
    heure = datetime.datetime.now().strftime("%H:%M:%S")

    if "temperature" in di2 and di2["temperature"] > s_temperature:

        if "temperatureAlert" not in dictalert["Capteurs AM"]:
            dictalert["Capteurs AM"]["temperatureAlert"] = {}
        if salle in dictalert["Capteurs AM"]["temperatureAlert"] :
            dictalert["Capteurs AM"]["temperatureAlert"][salle].append(di2)
        else:
            dictalert["Capteurs AM"]["temperatureAlert"][salle] = [di2]
        alert=True
            
    if "co2" in di2 and di2["co2"] > s_co2:
        if "co2Alert" not in dictalert["Capteurs AM"]:
            dictalert["Capteurs AM"]["co2Alert"] = {}
        if salle in dictalert["Capteurs AM"]["co2Alert"]:
            dictalert["Capteurs AM"]["co2Alert"][salle].append(di2)
        else:
            dictalert["Capteurs AM"]["co2Alert"][salle] = [di2]

        alert = True

    if "humidity" in di2 and di2["humidity"] > s_humidite:
        if "humiditeAlert" not in dictalert["Capteurs AM"]:
            dictalert["Capteurs AM"]["humiditeAlert"] = {}
        if salle in dictalert["Capteurs AM"]["humiditeAlert"] : 
            dictalert["Capteurs AM"]["humiditeAlert"][salle].append(di2)
        else:
            dictalert["Capteurs AM"]["humiditeAlert"][salle] = [di2]

        alert=True
    
    if alert : 
        print("Alerte salle ",salle, " : ", jour, heure)
        di2["jour"] = jour
        di2["heure"] = heure
        di2["room"] = salle
    else:

        if salle in dictfreq["Capteurs AM"]:
            dictfreq["Capteurs AM"][salle].append(di2)
        else :
            dictfreq["Capteurs AM"][salle] = [di2]

def sauvegarder():
    with open(fichjson, "w", encoding="utf-8") as freq_file:
        json.dump(dictfreq, freq_file, ensure_ascii=False, indent=4)

    with open(alertjson, "w", encoding="utf-8") as alert_file:
        json.dump(dictalert, alert_file, ensure_ascii=False, indent=4)



def on_message(mqttc, obj, msg):
    data = json.loads(msg.payload.decode('utf-8')) 
    #print(f"Message reçu sur {msg.topic}: {data}") 

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



def main():
    fichconfig = read_config("config.ini")
    
    global mqttc, topic_subscribe, donnees, donneesSolaire, salles
    global frequence_sauvegarde, fichjson, alertjson
    global s_temperature, s_co2, s_humidite, s_power, s_energy
    global dictfreq, dictalert

    mqttServer = fichconfig['MQTT']['broker']
    port = int(fichconfig['MQTT']['port'])
    topic_subscribe = fichconfig['MQTT']['topic_subscribe'].split(',')

    donnees = fichconfig['DATA']['types'].split(',')
    donneesSolaire = fichconfig['DATA']['typesSolaire'].split(',')
    salles = fichconfig['DATA']['salles'].split(',')
    frequence_sauvegarde = int(fichconfig['DATA']['frequency'])

    fichjson = fichconfig['OUTPUT']['json_file']
    alertjson = fichconfig['OUTPUT']['alert_file']

    s_temperature = int(fichconfig['ALERTE']['seuil_temperature'])
    s_co2 = int(fichconfig['ALERTE']['seuil_co2'])
    s_humidite = int(fichconfig['ALERTE']['seuil_humidity'])
    s_power = float(fichconfig['ALERTE']['seuil_power'])
    s_energy = int(fichconfig['ALERTE']['seuil_energy'])

    dictfreq = load_json_file(fichjson)
    dictalert = load_json_file(alertjson)

    if salles[0] == '' or salles[0].upper() == 'ALL':
        salles[0] = '+'

    mqttc = mqtt.Client()
    mqttc.connect(mqttServer, port, keepalive=60)
    mqttc.on_connect = on_connect
    mqttc.on_message = on_message
    mqttc.loop_start()  # exécute la réception des messages MQTT dans un thread

    dernier_sauvegarde = datetime.datetime.now()

    while True:
        maintenant = datetime.datetime.now()
        delta = (maintenant - dernier_sauvegarde).total_seconds()

        if delta >= frequence_sauvegarde:
            sauvegarder()
            print(f"Sauvegarde effectuée à {maintenant}")
            dernier_sauvegarde = maintenant

        time.sleep(1)


if __name__ == "__main__":
    main()
