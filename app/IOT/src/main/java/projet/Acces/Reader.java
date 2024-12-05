package projet.Acces;

import java.io.File;
import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.databind.DeserializationFeature;
import com.fasterxml.jackson.databind.ObjectMapper;

import projet.Data.Capteur;
import projet.Data.GestionAlert;
import projet.Data.GestionData;
import projet.Data.PanneauSolaire;

public class Reader {
    
    public static void main(String[] args) {
        getData();
    }


    public static void getData(){
        try {
            ObjectMapper objectMapper = new ObjectMapper();
            objectMapper.configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, true);

            GestionData jsonData = objectMapper.readValue(new File("donneeJSON.json"), GestionData.class);
            GestionAlert jsonDataAlert = objectMapper.readValue(new File("alertJSON.json"), GestionAlert.class);

            Map<String, List<Capteur>> capteursAM = jsonData.getCapteurs();

            System.out.println(jsonDataAlert.getCapteurs());
            
            Map<String, List<Capteur>> CO2capteursAM = jsonDataAlert.getCapteursAMbyAlert("co2Alert");
            Map<String, List<Capteur>> tempcapteursAM = jsonDataAlert.getCapteursAMbyAlert("temperatureAlert");
            Map<String, List<Capteur>> humcapteursAM = jsonDataAlert.getCapteursAMbyAlert("humiditeAlert");


            for (Map.Entry<String, List<Capteur>> entry : capteursAM.entrySet()) {
                String roomName = entry.getKey();
                List<Capteur> mesureCapteur = entry.getValue();

                if (!mesureCapteur.isEmpty() ) {
                    Capteur lastMesure = mesureCapteur.get(mesureCapteur.size() - 1);  // Dernière mesure
                    System.out.println(roomName +": "+ lastMesure);
                }
            }
            System.out.println("-----------------------------------------ALERTES -----------------------------------------");

            //ALERTES 
            Map<String, List<Capteur>>[] alertMaps = new Map[]{CO2capteursAM, tempcapteursAM, humcapteursAM};
            String[] alertTypes = new String[]{"CO2 Alert", "Temperature Alert", "Humidity Alert"};
            
            for (int i = 0; i < alertMaps.length; i++) {
                Map<String, List<Capteur>> alertMap = alertMaps[i];
                String alertType = alertTypes[i];
                
                System.out.println(alertType + ":");
                
                for (Map.Entry<String, List<Capteur>> entry : alertMap.entrySet()) {
                    String roomName = entry.getKey();
                    List<Capteur> mesureCapteur = entry.getValue();

                    if (!mesureCapteur.isEmpty() ) {
                        Capteur lastMesure = mesureCapteur.get(mesureCapteur.size() - 1);  // Dernière mesure
                        System.out.println(roomName + ": " + lastMesure);
                    }
                }
            }
            

            System.out.println("\nPanneaux solaires : ");
            List<PanneauSolaire> donneesPanneau = jsonData.getPanneaux();
            // for (PanneauSolaire panneau : donneesPanneau) {
            //     System.out.println(panneau);
            // }    
            List<PanneauSolaire> panneaux = jsonDataAlert.getPanneaux();

            System.out.println(donneesPanneau.get(donneesPanneau.size()-1));

             System.out.println("-----------------------------------------ALERTE SOLAIRE -----------------------------------------");
            System.out.println(panneaux.get(panneaux.size()-1));



        } catch (Exception e) {
            e.printStackTrace();
        }
    }
    
}
