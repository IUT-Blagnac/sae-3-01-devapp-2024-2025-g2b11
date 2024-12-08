package projet.application.Acces;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.databind.DeserializationFeature;
import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;

import projet.application.Data.Capteur;
import projet.application.Data.GestionAlert;
import projet.application.Data.GestionData;
import projet.application.Data.PanneauSolaire;

public class Reader {

    public static void main(String[] args) {
        getData();
    }

    /**
     * Récupère les données des panneaux solaires pour un type de donnée spécifique.
     *
     * @param dataType le type de donnée à récupérer (par exemple, "current power", "lifetime energy").
     * @param filePath le chemin vers le fichier JSON contenant les données des panneaux solaires.
     * @return une liste de valeurs float correspondant au type de donnée demandé.
     */
    public static List<Float> getSolarDataByType(String dataType, String filePath) {
        List<Float> dataValues = new ArrayList<>();
        try {
            ObjectMapper objectMapper = new ObjectMapper();
            JsonNode rootNode = objectMapper.readTree(new File(filePath));

            if (rootNode.has("PanneauSolaire")) {
                JsonNode solarPanelsNode = rootNode.get("PanneauSolaire");
                for (JsonNode panel : solarPanelsNode) {
                    switch (dataType.toLowerCase()) {
                        case "current power":
                            if (panel.has("currentPower") && panel.get("currentPower").has("power")) {
                                dataValues.add(panel.get("currentPower").get("power").floatValue());
                            }
                            break;
                        case "lifetime energy":
                            if (panel.has("lifeTimeData") && panel.get("lifeTimeData").has("energy")) {
                                dataValues.add(panel.get("lifeTimeData").get("energy").floatValue());
                            }
                            break;
                        default:
                            System.out.println("Type de donnée inconnu pour les panneaux solaires : " + dataType);
                            break;
                    }
                }
            } else {
                System.out.println("Le fichier JSON ne contient pas de noeud 'PanneauSolaire'.");
            }
        } catch (IOException e) {
            System.err.println("Erreur lors de la lecture du fichier JSON : " + e.getMessage());
        }
        return dataValues;
    }

    public static List<Float> getDataForRoomAndType(String room, String type, String filePath) {
        List<Float> values = new ArrayList<>();
        try {
            File file = new File(filePath);
    
            ObjectMapper objectMapper = new ObjectMapper();
            GestionData data = objectMapper.readValue(file, GestionData.class);
    
            Map<String, List<Capteur>> capteursAM = data.getCapteurs();
            List<Capteur> capteurs = capteursAM.get(room);
    
            if (capteurs != null) {
                for (Capteur capteur : capteurs) {
                    Float value = null; // Valeur par défaut pour différencier les absences
                    switch (type.toLowerCase()) {
                        case "température":
                            value = capteur.temperature;
                            break;
                        case "humidité":
                            value = capteur.humidity;
                            break;
                        case "co2":
                            value = (float) capteur.co2;
                            break;
                        case "tvoc":
                            value = (float) capteur.tvoc;
                            break;
                        case "illumination":
                            value = (float) capteur.illumination;
                            break;
                        case "infrared":
                            value = (float) capteur.infrared;
                            break;
                        case "infrared_and_visible":
                            value = (float) capteur.infrared_and_visible;
                            break;
                        case "pressure":
                            value = capteur.pressure;
                            break;
                        case "activity":
                            value = (float) capteur.activity;
                            break;
                    }
                    // Ajouter uniquement si la valeur n'est pas absente
                    if (value != null) {
                        values.add(value);
                    }
                }
            }
        } catch (Exception e) {
            System.out.println("Erreur lors de la lecture des données : " + e.getMessage());
            e.printStackTrace();
        }
        return values;
    }
    
    
    /**
     * Récupère les données pour un type de mesure spécifique dans une salle donnée.
     *
     * @param room     le nom de la salle dont les données doivent être récupérées.
     * @param type     le type de donnée à récupérer (par exemple, "température", "humidité").
     * @param filePath le chemin vers le fichier JSON contenant les données des capteurs.
     * @return une liste de valeurs float correspondant au type de donnée demandé pour la salle spécifiée.
     */
    public static List<String> getRoomNames(String filePath) {
        List<String> roomNames = new ArrayList<>();

        try {
            // Initialisation du mapper Jackson
            ObjectMapper objectMapper = new ObjectMapper();

            // Lecture du fichier JSON
            JsonNode rootNode = objectMapper.readTree(new File(filePath));

            // Vérification de la présence du noeud "Capteurs AM"
            if (rootNode.has("Capteurs AM")) {
                JsonNode capteursNode = rootNode.get("Capteurs AM");

                // Extraction des noms de salles
                capteursNode.fieldNames().forEachRemaining(roomNames::add);
            } else {
                System.out.println("Le fichier JSON ne contient pas de noeud 'Capteurs AM'.");
            }
        } catch (IOException e) {
            System.err.println("Erreur lors de la lecture du fichier JSON : " + e.getMessage());
        }

        return roomNames;
    }
    
    /**
     * Récupère et affiche les données des capteurs et des panneaux solaires à partir de fichiers JSON.
     *
     * Cette méthode :
     * - Charge les données de capteurs et les alertes à partir des fichiers `donneeJSON.json` et `alertJSON.json`.
     * - Affiche les dernières mesures disponibles pour chaque salle.
     * - Affiche les alertes liées aux niveaux de CO2, température et humidité.
     * - Affiche les données et alertes des panneaux solaires.
     *
     * Les fichiers doivent respecter les structures définies pour `GestionData` et `GestionAlert`.
     */
    public static void getData() {
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

                if (!mesureCapteur.isEmpty()) {
                    Capteur lastMesure = mesureCapteur.get(mesureCapteur.size() - 1); // Dernière mesure
                    System.out.println(roomName + ": " + lastMesure);
                }
            }
            System.out.println(
                    "-----------------------------------------ALERTES -----------------------------------------");

            // ALERTES
            Map<String, List<Capteur>>[] alertMaps = new Map[] { CO2capteursAM, tempcapteursAM, humcapteursAM };
            String[] alertTypes = new String[] { "CO2 Alert", "Temperature Alert", "Humidity Alert" };

            for (int i = 0; i < alertMaps.length; i++) {
                Map<String, List<Capteur>> alertMap = alertMaps[i];
                String alertType = alertTypes[i];

                System.out.println(alertType + ":");

                for (Map.Entry<String, List<Capteur>> entry : alertMap.entrySet()) {
                    String roomName = entry.getKey();
                    List<Capteur> mesureCapteur = entry.getValue();

                    if (!mesureCapteur.isEmpty()) {
                        Capteur lastMesure = mesureCapteur.get(mesureCapteur.size() - 1); // Dernière mesure
                        System.out.println(roomName + ": " + lastMesure);
                    }
                }
            }

            System.out.println("\nPanneaux solaires : ");
            List<PanneauSolaire> donneesPanneau = jsonData.getPanneaux();
            // for (PanneauSolaire panneau : donneesPanneau) {
            // System.out.println(panneau);
            // }
            List<PanneauSolaire> panneaux = jsonDataAlert.getPanneaux();

            System.out.println(donneesPanneau.get(donneesPanneau.size() - 1));

            System.out.println(
                    "-----------------------------------------ALERTE SOLAIRE -----------------------------------------");
            System.out.println(panneaux.get(panneaux.size() - 1));

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

}
