package projet.application.Acces;

import javafx.application.Platform;
import java.util.List;
import projet.application.view.DashboardViewController;

public class AlertFetcher implements Runnable {
    
    private DashboardViewController controller;  // Référence au contrôleur pour mettre à jour l'UI

    // Constructeur
    public AlertFetcher(DashboardViewController controller) {
        this.controller = controller;
    }

    @Override
    public void run() {
        // Récupérer les alertes à partir de Reader
        List<String> alerts = Reader.getAlerts();
        
        // Mettre à jour l'interface graphique sur le thread JavaFX
        Platform.runLater(() -> {
            controller.setAlerts(alerts);
        });
    }
}
