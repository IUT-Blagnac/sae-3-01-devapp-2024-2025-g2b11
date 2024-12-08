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

/**
 * Contrôleur pour la vue de sélection des données dans l'application.
 *
 * Permet à l'utilisateur de choisir entre différentes sources de données
 * (panneaux solaires, capteurs, ou les deux) et de naviguer vers la configuration
 * des salles ou la page suivante/retour. Ce contrôleur gère également l'intégration
 * avec l'instance {@link Ecrivain} pour sauvegarder les préférences de l'utilisateur.
 *
 */
public class ConfigDataSelectViewController {

    /** Groupe de boutons radio pour le choix des données. */
    @FXML
    private ToggleGroup dataChoiceGroup;

    /** Bouton radio pour sélectionner les données des panneaux solaires. */
    @FXML
    private RadioButton solarPanelsRadio;

    /** Bouton radio pour sélectionner les données des capteurs. */
    @FXML
    private RadioButton sensorsRadio;

    /** Bouton radio pour sélectionner les données des capteurs et des panneaux solaires. */
    @FXML
    private RadioButton bothRadio;

    /** Bouton pour accéder à la configuration des salles. */
    @FXML
    private Button chooseRoomButton;

    /** Bouton pour passer à l'étape suivante de la configuration. */
    @FXML
    private Button nextButton;

    /** Bouton pour revenir à l'étape précédente. */
    @FXML
    private Button backButton;

    /** Instance de {@link Ecrivain} pour gérer la configuration des données. */
    private Ecrivain ecrivain;

    /** Stage principal de l'application. */
    private Stage primaryStage;

    /** Stage de la boîte de dialogue actuelle. */
    private Stage dialogStage;

    /** Instance principale de l'application. */
    private ProjetIOT App;

    /**
     * Définit l'instance principale de l'application.
     *
     * @param appProjet l'instance de l'application {@link ProjetIOT}.
     */
    public void setProjetApp(ProjetIOT appProjet) {
            this.App = appProjet;
	}

    /**
     * Définit le stage de la boîte de dialogue actuelle.
     *
     * @param dialogStage le stage de la boîte de dialogue.
     */
    public void setDialogStage(Stage dialogStage) {

        this.dialogStage = dialogStage;

    }

    /**
     * Définit le stage principal de l'application.
     *
     * @param primaryStage le stage principal.
     */
    public void setPrimaryStage(Stage primaryStage) {
        this.primaryStage = primaryStage;
    }

    /**
     * Initialise le contrôleur et configure les listeners pour les interactions utilisateur.
     *
     * Configure les actions pour le groupe de boutons radio, permettant d'activer ou de
     * désactiver le bouton de choix des salles en fonction des sélections.
     *
     */
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

    /**
     * Gère l'action du bouton "Suivant".
     *
     * Passe à l'étape suivante de la configuration en instanciant {@link ConfigDataSelect}.
     *
     * @param event l'événement déclenché par le bouton.
     */
    @FXML
    private void onNextButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour aller à la page de configuration suivante
        System.out.println("Suivant : Accéder à la configuration suivante.");
        ConfigDataSelect CDS = new ConfigDataSelect(this.primaryStage, this.App, this.dialogStage);
        CDS.doConfigDonnees();
    }

    /**
     * Gère l'action du bouton "Retour".
     *
     * Réinitialise les paramètres via {@link Ecrivain#reset()} et ferme la boîte de dialogue.
     *
     * @param event l'événement déclenché par le bouton.
     */
    @FXML
    private void onBackButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour revenir à la page précédente
        this.ecrivain.reset();
        this.dialogStage.close();
        System.out.println("Retour : Revenir à la page précédente.");
    }

    /**
     * Gère l'action du bouton "Choix salle".
     *
     * Ouvre une nouvelle configuration des salles en utilisant {@link ConfigRoom}.
     *
     * @param event l'événement déclenché par le bouton.
     */
    @FXML
    private void onChooseRoomButtonClicked(ActionEvent event) {
        // Implémentez ici la logique pour choisir une salle en fonction des capteurs
        System.out.println("Choisir une salle");
        ConfigRoom CR = new ConfigRoom(this.dialogStage, this.App);
        CR.doConfigroom();
    }
}
