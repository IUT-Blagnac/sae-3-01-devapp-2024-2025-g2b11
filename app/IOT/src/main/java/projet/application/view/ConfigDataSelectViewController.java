package projet.application.view;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.RadioButton;
import javafx.scene.control.ToggleGroup;

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

    // Méthode pour gérer les changements dans le groupe de boutons radio
    @FXML
    private void initialize() {
        // Ajouter un listener pour gérer l'activation du bouton "Choix salle"
        dataChoiceGroup.selectedToggleProperty().addListener((observable, oldToggle, newToggle) -> {
            // Si l'utilisateur sélectionne "Capteurs" ou "Les deux", activer le bouton "Choix salle"
            if (sensorsRadio.isSelected() || bothRadio.isSelected()) {
                chooseRoomButton.setDisable(false);
            } else {
                chooseRoomButton.setDisable(true);
            }
        });
    }

    // Méthode pour l'action du bouton "Suivant"
    @FXML
    private void onNextButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour aller à la page de configuration suivante
        System.out.println("Suivant : Accéder à la configuration suivante.");
    }

    // Méthode pour l'action du bouton "Retour"
    @FXML
    private void onBackButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour revenir à la page précédente
        System.out.println("Retour : Revenir à la page précédente.");
    }

    // Méthode pour l'action du bouton "Choix salle"
    @FXML
    private void onChooseRoomButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour choisir une salle en fonction des capteurs
        System.out.println("Choisir une salle");
    }
}
