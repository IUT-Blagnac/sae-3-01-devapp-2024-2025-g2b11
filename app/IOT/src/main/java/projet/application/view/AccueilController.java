package projet.application.view;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ButtonType;
import javafx.stage.Stage;
import javafx.scene.control.Alert.AlertType;

import java.util.Optional;

public class AccueilController {

    @FXML
    private Button btnConfigurer;

    @FXML
    private Button btnInfos;

    @FXML
    private Button btnQuitter;

    @FXML
    private Button btnGraphiques;

    @FXML
    private Button btnAlertes;

    private Stage primaryStage;

    /**
     * Définit la scène principale pour ce contrôleur.
     *
     * @param primarStage la scène principale.
     */
    public void setPrimaryStage(Stage primarStage) {
        this.primaryStage = primarStage;
        this.primaryStage.setOnCloseRequest(event -> {
            event.consume();
            actionQuitter(null);
        });
    }

    // Méthode pour le bouton "Configurer"
    @FXML
    private void actionConfigurer(ActionEvent event) {
        Alert alert = new Alert(AlertType.INFORMATION);
        alert.setTitle("Configuration");
        alert.setHeaderText("Configurer les paramètres du système IoT");
        alert.setContentText("Vous pouvez maintenant configurer votre système IoT.");
        alert.showAndWait();
    }

    // Méthode pour le bouton "Infos"
    @FXML
    private void actionInfos(ActionEvent event) {
        Alert alert = new Alert(AlertType.INFORMATION);
        alert.setTitle("Informations");
        alert.setHeaderText("Informations sur l'application IoT");
        alert.setContentText("Cette application permet de gérer les capteurs et appareils IoT.");
        alert.showAndWait();
    }

    // Méthode pour le bouton "Quitter"
    @FXML
    private void actionQuitter(ActionEvent event) {
        Alert alert = new Alert(AlertType.CONFIRMATION);
        alert.setTitle("Quitter");
        alert.setHeaderText("Voulez-vous vraiment quitter ?");
        alert.setContentText("Êtes-vous sûr de vouloir quitter l'application ?");
        Optional<ButtonType> result = alert.showAndWait();
        if (result.isPresent() && result.get() == ButtonType.OK) {
            System.exit(0);
        }
    }

    // Méthode pour le bouton "Graphiques"
    @FXML
    private void actionAfficherGraphique(ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("choixSalle.fxml"));
            Scene choixSalleScene = new Scene(loader.load());
    
            ChoixSalleController choixSalleController = loader.getController();
            choixSalleController.setStage((Stage) ((Button) event.getSource()).getScene().getWindow());
    
            Stage stage = (Stage) ((Button) event.getSource()).getScene().getWindow();
            stage.setScene(choixSalleScene);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    // Méthode pour le bouton "Alertes"
    @FXML
    private void actionAfficherAlertes(ActionEvent event) {
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("alertes.fxml"));
            Scene alertScene = new Scene(loader.load());
            Stage stage = (Stage) ((Button) event.getSource()).getScene().getWindow();
            stage.setScene(alertScene);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
