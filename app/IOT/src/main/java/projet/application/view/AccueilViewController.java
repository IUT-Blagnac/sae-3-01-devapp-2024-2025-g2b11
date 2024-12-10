package projet.application.view;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.Initializable;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ButtonType;
import javafx.scene.control.ListView;
import javafx.stage.Stage;
import projet.application.ProjetIOT;
import projet.application.Acces.AlertManager;
import javafx.scene.control.Alert.AlertType;

import java.net.URL;
import java.util.Optional;
import java.util.ResourceBundle;

public class AccueilViewController implements Initializable {

    // Déclaration des composants FXML
    @FXML
    private ListView<String> listViewParties;

    @FXML
    private Button btnConfigurer;

    @FXML
    private Button btnInfos;

    @FXML
    private Button btnQuitter;
    @FXML
    private Button btnTestMQTT;

    private Stage primaryStage;
    
        private ProjetIOT App;
    
    
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
    
        public void setPorjetApp(ProjetIOT appProjet) {
            this.App = appProjet;
	}


    // Méthode appelée pour l'action du bouton "Configurer"
    @FXML
    private void actionConfigurer(ActionEvent event) {
        // Afficher une boîte de dialogue de configuration (exemple)
        // Alert alert = new Alert(AlertType.INFORMATION);
        // alert.setTitle("Configuration");
        // alert.setHeaderText("Configurer les paramètres du système IoT");
        // alert.setContentText("Vous pouvez maintenant configurer votre système IoT.");

        // alert.showAndWait();

        // Charger la vue de configuration
        App.loadSelectConfig();

    }

    @FXML
    private void actionDashboard(ActionEvent event) {
        App.loadDashboard();
    }

    // Méthode appelée pour l'action du bouton "Infos"
    @FXML
    private void actionInfos(ActionEvent event) {
        // Afficher une boîte de dialogue d'information
        Alert alert = new Alert(AlertType.INFORMATION);
        alert.setTitle("Informations");
        alert.setHeaderText("Informations sur l'application IoT");
        alert.setContentText("Cette application permet de gérer les capteurs et appareils IoT.");

        alert.showAndWait();
    }

    // Méthode appelée pour l'action du bouton "Quitter"
    @FXML
    private void actionQuitter(ActionEvent event) {
        // Confirmation avant de quitter
        Alert alert = new Alert(AlertType.CONFIRMATION);
        alert.setTitle("Quitter");
        alert.setHeaderText("Voulez-vous vraiment quitter ?");
        alert.setContentText("Êtes-vous sûr de vouloir quitter l'application ?");

        // Si l'utilisateur clique sur "OK", on quitte l'application
        Optional<ButtonType> result = alert.showAndWait();
        if (result.isPresent() && result.get() == ButtonType.OK) {
            System.exit(0);
        }
    }

   

    // Méthode pour l'action du menu "Importer votre grille" (sur l'élément "Fichier")
    @FXML
    private void actionOuvrirFichier(ActionEvent event) {
        // Exemple d'une fenêtre d'importation ou une autre action
        Alert alert = new Alert(AlertType.INFORMATION);
        alert.setTitle("Importer");
        alert.setHeaderText("Importer une grille");
        alert.setContentText("Ouvrez et chargez votre grille.");

        alert.showAndWait();
    }

  

    // Méthode pour l'action du menu "À propos"
    @FXML
    private void actionApropos(ActionEvent event) {
        // Affichage d'une fenêtre "À propos"
        Alert alert = new Alert(AlertType.INFORMATION);
        alert.setTitle("À propos");
        alert.setHeaderText("À propos de l'application");
        alert.setContentText("Application IoT développée pour surveiller les capteurs.");

        alert.showAndWait();
    }

    // Méthode pour l'action du menu "Close"
    @FXML
    private void actionQuitterMenu(ActionEvent event) {
        // Quitter l'application
        System.exit(0);
    }

    @FXML
    private void doTestConnexionMQTT(){
        System.out.println("Test de connexion MQTT");
        App.LoadPython("test");
        int exitcode = App.getExitCodeMQTT();
        if (exitcode == 0) {
            Alert alert = new Alert(AlertType.INFORMATION);
            alert.setTitle("Test de connexion MQTT");
            alert.setHeaderText("Test de connexion MQTT réussi");
            alert.setContentText("La connexion MQTT a été établie avec succès.");

            alert.showAndWait();
        } else {
            Alert alert = new Alert(AlertType.ERROR);
            alert.setTitle("Test de connexion MQTT");
            alert.setHeaderText("Erreur de connexion MQTT");
            alert.setContentText("Impossible de se connecter au serveur MQTT.");

            alert.showAndWait();
        }
    }
    
    public void setAlertManager(AlertManager alertManager) {
        listViewParties.setItems(alertManager.getAlerts());
    }

    @Override
    public void initialize(URL location, ResourceBundle resources) {
       
    }
}
