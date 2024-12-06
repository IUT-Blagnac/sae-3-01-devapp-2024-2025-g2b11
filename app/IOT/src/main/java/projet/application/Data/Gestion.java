package projet.application.Data;

import com.fasterxml.jackson.annotation.JsonProperty;

import java.util.List;
import java.util.Map;

public abstract class Gestion {

    @JsonProperty("PanneauSolaire")
    private List<PanneauSolaire> donneesPanneau;

    public List<PanneauSolaire> getPanneaux() {
        return donneesPanneau;
    }

    public void setPanneaux(List<PanneauSolaire> pdonneesPanneau) {
        this.donneesPanneau = pdonneesPanneau;
    }

    // Méthode abstraite pour être implémentée par les sous-classes
    public abstract Map<String, ?> getCapteurs();
}
