package projet.application.control;

import projet.application.Acces.Reader;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.List;

import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;

public class Dashboard {

    private static final String JSON_FILE_PATH = "app\\IOT\\donneeJSON.json";

    // Méthode pour récupérer les noms des salles
    public static List<String> getRoomNames() {
        return Reader.getRoomNames(JSON_FILE_PATH);
    }

    // Méthode pour récupérer les données des panneaux solaires
    public static List<String> getSolarPanelData() {
        return List.of("Panneau"); // Ajouter une vraie récupération si nécessaire
    }

    // Méthode pour récupérer les données en fonction du type sélectionné
    public static List<Float> getData(String selectionType, String specificSelection, String dataType) {
        if ("Salle".equalsIgnoreCase(selectionType)) {
            return Reader.getDataForRoomAndType(specificSelection, dataType, JSON_FILE_PATH);
        } else if ("Panneaux Solaires".equalsIgnoreCase(selectionType)) {
            return getSolarData(dataType, JSON_FILE_PATH);
        }
        return List.of();
    }
    
    public static List<Float> getSolarData(String dataType, String filePath) {
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
            System.out.println("Le fichier JSON ne contient pas de données pour les panneaux solaires.");
        }
    } catch (IOException e) {
        System.err.println("Erreur lors de la lecture du fichier JSON : " + e.getMessage());
    }
    return dataValues;
}

}
