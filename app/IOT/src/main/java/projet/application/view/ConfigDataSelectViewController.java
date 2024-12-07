package projet.application.view;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.RadioButton;
import javafx.scene.control.ToggleGroup;
import javafx.stage.Stage;
import projet.application.Acces.Ecrivain;
import projet.application.ProjetIOT;
import projet.application.control.ConfigDataSelect;
import projet.application.control.ConfigRoom;

public class ConfigDataSelectViewController {

    @FXML
    private ToggleGroup dataChoiceGroup;

    @FXML
    private RadioButton solarPanelsRadio;

    @FXML
    private RadioButton sensorsRadio;

    @FXML
    private RadioButton bothRadio;

    @FXML
    private Button chooseRoomButton;

    @FXML
    private Button nextButton;

    @FXML
    private Button backButton;
    private Ecrivain ecrivain;
    private Stage primaryStage;

    private Stage dialogStage;

    private ProjetIOT App;

    public void setProjetApp(ProjetIOT appProjet) {
            this.App = appProjet;
	}

    public void setDialogStage(Stage dialogStage) {

        this.dialogStage = dialogStage;

    }

    public void setPrimaryStage(Stage primaryStage) {
        this.primaryStage = primaryStage;
    }

    // Méthode pour gérer les changements dans le groupe de boutons radio
    @FXML
    private void initialize() {
        this.ecrivain=Ecrivain.getInstance();
        ecrivain.setSolar(true);
        ecrivain.setCapteur(true);
        // Ajouter un listener pour gérer l'activation du bouton "Choix salle"
        dataChoiceGroup.selectedToggleProperty().addListener((observable, oldToggle, newToggle) -> {
            // Si l'utilisateur sélectionne "Capteurs" ou "Les deux", activer le bouton "Choix salle"
            if (sensorsRadio.isSelected() || bothRadio.isSelected()) {
                ecrivain.setCapteur(true);
                if (bothRadio.isSelected()){
                    ecrivain.setSolar(true);
                }else {
                    ecrivain.setSolar(false);
                }
                chooseRoomButton.setDisable(false);
            } else {
                ecrivain.setCapteur(false);
                chooseRoomButton.setDisable(true);
            }
        });
    }

    // Méthode pour l'action du bouton "Suivant"
    @FXML
    private void onNextButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour aller à la page de configuration suivante
        System.out.println("Suivant : Accéder à la configuration suivante.");
        ConfigDataSelect CDS = new ConfigDataSelect(this.primaryStage, this.App, this.dialogStage);
        CDS.doConfigDonnees();
    }

    // Méthode pour l'action du bouton "Retour"
    @FXML
    private void onBackButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour revenir à la page précédente
        this.ecrivain.reset();
        this.dialogStage.close();
        System.out.println("Retour : Revenir à la page précédente.");
    }

    // Méthode pour l'action du bouton "Choix salle"
    @FXML
    private void onChooseRoomButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour choisir une salle en fonction des capteurs
        System.out.println("Choisir une salle");
        ConfigRoom CR = new ConfigRoom(this.dialogStage, this.App);
        CR.doConfigroom();
    }
}
