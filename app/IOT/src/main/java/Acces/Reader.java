package Acces;

import java.io.File;
import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.databind.DeserializationFeature;
import com.fasterxml.jackson.databind.ObjectMapper;

import Data.Capteur;
import Data.GestionData;
import Data.PanneauSolaire;

public class Reader {
    
    public static void main(String[] args) {
        getData();
    }
    public static void getData(){
        try {
            ObjectMapper objectMapper = new ObjectMapper();
            objectMapper.configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, false);

            GestionData jsonData = objectMapper.readValue(new File("nvfichier.json"), GestionData.class);

            Map<String, List<Capteur>> capteursAM = jsonData.getCapteursAM();
            for (Map.Entry<String, List<Capteur>> entry : capteursAM.entrySet()) {
                String roomName = entry.getKey();
                List<Capteur> mesureCapteur = entry.getValue();

                if (!mesureCapteur.isEmpty()) {
                    Capteur lastMesure = mesureCapteur.get(mesureCapteur.size() - 1);  // Dernière mesure
                    System.out.println(roomName +": "+ lastMesure);
                }
            }
            //System.out.println(capteursAM);
            

            // Afficher la dernière entrée des panneaux solaires
            // Map<String, List<PanneauSolaire>> panneau = jsonData.getPanneaux();
           
            // System.out.println(panneau);

            // for (Map.Entry<String, List<PanneauSolaire>> entry : panneau.entrySet()) {
            //     String roomName = entry.getKey();
            //     List<PanneauSolaire> elemListePann = entry.getValue();

            //     if (!elemListePann.isEmpty()) {
            //         PanneauSolaire lastMesure = elemListePann.get(elemListePann.size() - 1);  // Dernière mesure
            //         System.out.println(roomName +": "+ lastMesure);
            //     }
            // }

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    
}
