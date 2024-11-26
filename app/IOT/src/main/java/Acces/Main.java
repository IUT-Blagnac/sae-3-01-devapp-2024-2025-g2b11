package Acces;

import com.fasterxml.jackson.databind.DeserializationFeature;
import com.fasterxml.jackson.databind.ObjectMapper;

import Data.Capteur;

import java.io.File;
import java.util.List;
import java.util.Map;

public class Main {
    public static void main(String[] args) {
        try {
            ObjectMapper objectMapper = new ObjectMapper();
            objectMapper.configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, false);

            SensorData sensorData = objectMapper.readValue(new File("data.json"), SensorData.class);

            // Afficher la dernière entrée des capteurs
            Map<String, List<Capteur>> capteursAM = sensorData.getCapteursAM();
            for (Map.Entry<String, List<Capteur>> entry : capteursAM.entrySet()) {
                String roomName = entry.getKey();
                List<Capteur> mesureCapteur = entry.getValue();

                if (!mesureCapteur.isEmpty()) {
                    Capteur lastMesure = mesureCapteur.get(mesureCapteur.size() - 1);  // Dernière mesure
                    System.out.println(roomName +": "+ lastMesure);
                }
            }
            System.out.println(capteursAM.get("C102").get(0).temperature);
            

            // // Afficher la dernière entrée des panneaux solaires
            // SensorData.Panneaux panneaux = sensorData.getPanneaux();
            // List<SensorData.Panneau> panneauxSolaire = panneaux.getPanneauxSolaire();
            // if (!panneauxSolaire.isEmpty()) {
            //     SensorData.Panneau lastPanneau = panneauxSolaire.get(panneauxSolaire.size() - 1); // Dernier panneau
            //                                                                                       // solaire
            //     System.out.println("Last Panel Update Time: " + lastPanneau.getLastUpdateTime());
            //     System.out.println("Last Energy: " + lastPanneau.getLifeTimeData().getEnergy());
            // }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
