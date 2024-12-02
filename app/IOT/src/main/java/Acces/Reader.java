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
            objectMapper.configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, true);

            GestionData jsonData = objectMapper.readValue(new File("dataJSON.json"), GestionData.class);

            Map<String, List<Capteur>> capteursAM = jsonData.getCapteursAM();
            for (Map.Entry<String, List<Capteur>> entry : capteursAM.entrySet()) {
                String roomName = entry.getKey();
                List<Capteur> mesureCapteur = entry.getValue();

                if (!mesureCapteur.isEmpty() && roomName.equals("E208")) {
                    Capteur lastMesure = mesureCapteur.get(mesureCapteur.size() - 1);  // Dernière mesure
                    System.out.println(roomName +": "+ lastMesure);
                }
            }
            //System.out.println(capteursAM);
            

            System.out.println("\nPanneaux solaires : ");
            List<PanneauSolaire> donneesPanneau = jsonData.getPanneaux();
            // for (PanneauSolaire panneau : donneesPanneau) {
            //     System.out.println(panneau);
            // }    

            System.out.println(donneesPanneau.get(donneesPanneau.size()-1));



        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    
}
