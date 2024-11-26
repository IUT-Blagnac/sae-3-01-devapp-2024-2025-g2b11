package Acces;
import com.fasterxml.jackson.annotation.JsonProperty;

import Data.Capteur;
import Data.PanneauSolaire;

import java.util.List;
import java.util.Map;

public class SensorData {

    @JsonProperty("Capteurs AM")
    private Map<String, List<Capteur>> capteursAM;
    @JsonProperty("Panneaux")
    private PanneauSolaire panneaux;

    // Getters et Setters pour capteursAM et panneaux
    public Map<String, List<Capteur>> getCapteursAM() {
        return capteursAM;
    }

    public void setCapteursAM(Map<String, List<Capteur>> capteursAM) {
        this.capteursAM = capteursAM;
    }

    public PanneauSolaire getPanneaux() {
        return panneaux;
    }

    public void setPanneaux(PanneauSolaire panneaux) {
        this.panneaux = panneaux;
    }

}