package Data;

import java.util.List;
import java.util.Map;

import com.fasterxml.jackson.annotation.JsonProperty;

public class GestionAlert extends Gestion {
    @JsonProperty("Capteurs AM")
    private Map<String, Map<String, List<Capteur>>> AlertcapteursAM;

    public Map<String, List<Capteur>> getCapteursAMbyAlert(String alert) {
        return AlertcapteursAM.getOrDefault(alert, null);
    }
    
    @Override
    public Map<String, Map<String, List<Capteur>>> getCapteurs() {
        return AlertcapteursAM;
    }
}
