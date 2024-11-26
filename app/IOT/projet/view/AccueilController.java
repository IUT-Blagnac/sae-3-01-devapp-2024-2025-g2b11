package projet.view;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.Alert.AlertType;

public class AccueilController {

    @FXML
    private Button btnAide;

    @FXML
    private Button btnConfigurer;

    @FXML
    private Button btnQuitter;

    /**
     * Cette méthode est appelée lorsque l'utilisateur clique sur le bouton "Aide".
     * Elle affiche une boîte de dialogue d'aide.
     */
    @FXML
    private void afficherAide(ActionEvent event) {
        // Afficher une fenêtre d'aide
        Alert alert = new Alert(AlertType.INFORMATION);
        alert.setTitle("Aide");
        alert.setHeaderText("Aide Contextuelle");
        alert.setContentText("Ici vous trouverez des informations pour vous aider à utiliser l'application.");

        alert.showAndWait();
    }

    /**
     * Cette méthode est appelée lorsque l'utilisateur clique sur le bouton "Configurer".
     * Elle peut ouvrir une autre fenêtre de configuration ou afficher un message de configuration.
     */
    @FXML
    private void configurer(ActionEvent event) {
        // Affichage d'une boîte de dialogue pour simuler la configuration
        Alert alert = new Alert(AlertType.INFORMATION);
        alert.setTitle("Configuration");
        alert.setHeaderText("Configurer les paramètres");
        alert.setContentText("Cette section permet de configurer les paramètres du système IoT.");

        alert.showAndWait();
    }

    /**
     * Cette méthode est appelée lorsque l'utilisateur clique sur le bouton "Quitter".
     * Elle ferme l'application.
     */
    @FXML
    private void quitter(ActionEvent event) {
        // Fermer l'application
        System.exit(0);
    }
}
