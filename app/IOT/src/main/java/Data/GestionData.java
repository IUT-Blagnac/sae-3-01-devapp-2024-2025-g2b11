package Data;
import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.List;
import java.util.Map;

public class GestionData {

    @JsonProperty("Capteurs AM")
    private Map<String, List<Capteur>> capteursAM;
    @JsonProperty("Panneaux-Solaire")
    private Map<String, List<PanneauSolaire>> donneesPanneau;

    // Getters et Setters pour capteursAM et panneaux
    public Map<String, List<Capteur>> getCapteursAM() {
        return capteursAM;
    }
    public void setCapteursAM(Map<String, List<Capteur>> capteursAM) {
        this.capteursAM = capteursAM;
    }
    public Map<String, List<PanneauSolaire>> getPanneaux() {
        return donneesPanneau;
    }
    public void setPanneaux(Map<String,List<PanneauSolaire>> pdonneesPanneau) {
        this.donneesPanneau = pdonneesPanneau;
    }

}