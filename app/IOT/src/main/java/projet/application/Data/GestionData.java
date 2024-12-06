package projet.application.Data;
import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.List;
import java.util.Map;

public class GestionData extends Gestion {
    @JsonProperty("Capteurs AM")
    private Map<String, List<Capteur>> capteursAM;

    public void setCapteursAM(Map<String, List<Capteur>> capteursAM) {
        this.capteursAM = capteursAM;
    }
    
    @Override
    public Map<String, List<Capteur>> getCapteurs() {
        return capteursAM;
    }

}