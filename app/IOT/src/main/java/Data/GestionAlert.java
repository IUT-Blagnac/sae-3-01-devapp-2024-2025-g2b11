package Data;

import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.annotation.JsonProperty;

public class GestionAlert {
    
    @JsonProperty("Capteurs AM")
    private Map<String, Map<String, List<Capteur>>> AlertcapteursAM;

    @JsonProperty("PanneauSolaire")
    private List<PanneauSolaire> donneesPanneau;

    public Map<String, Map<String, List<Capteur>>> getAlertcapteursAM() {
        return AlertcapteursAM;
    }

    public Map<String, List<Capteur>> getCapteursAMbyAlert(String alert) {
        return AlertcapteursAM.getOrDefault(alert, null);
    }
    
    public List<PanneauSolaire> getPanneaux() {
        return donneesPanneau;
    }
    public void setPanneaux(List<PanneauSolaire> pdonneesPanneau) {
        this.donneesPanneau = pdonneesPanneau;
    }
}
