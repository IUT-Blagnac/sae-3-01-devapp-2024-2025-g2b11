package projet.application.control;

import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;
import projet.application.ProjetIOT;
import projet.application.view.ConfigDataSelect2ViewController;
import projet.application.view.ConfigRoomViewController;

import java.io.IOException;

/**
 * Contrôleur pour la configuration des données.
 *
 * Cette classe est responsable de l'initialisation et de l'affichage
 * de l'interface utilisateur permettant de sélectionner les données
 * à configurer. Elle gère également le contexte nécessaire pour les
 * interactions entre les différents éléments de l'application.
 *
 */
public class ConfigDataSelect {

    /** Stage utilisé pour afficher la fenêtre de configuration des données. */
    private Stage cdsStage;

    /** Contrôleur associé à la vue de sélection des données. */
    private ConfigDataSelect2ViewController ConfigdonneesViewController;

    /** Instance principale de l'application. */
    private ProjetIOT AppProjetIOT;

    /**
     * Affiche la boîte de dialogue pour la configuration des données.
     *
     * Cette méthode demande au contrôleur de la vue associée
     * d'afficher la fenêtre pour permettre à l'utilisateur
     * de configurer les paramètres des données.
     */
    public void doConfigDonnees() {
        this.ConfigdonneesViewController.DisplayDialog();
    }


    /**
     * Constructeur de la classe ConfigDataSelect.
     *
     * Initialise le contrôleur, charge la vue associée, et configure le stage
     * pour la fenêtre de sélection des données.
     *
     * @param _parentStage   le stage parent de la fenêtre de configuration.
     * @param _AppProjetIOT  l'instance principale de l'application {@link ProjetIOT}.
     * @param _containingstage le stage de la boîte de dialogue parent.
     */
    public ConfigDataSelect(Stage _parentStage, ProjetIOT _AppProjetIOT, Stage _containingstage) {
        try {
            this.AppProjetIOT = _AppProjetIOT;
            FXMLLoader loader = new FXMLLoader(ConfigRoomViewController.class.getResource("ConfigDataSelect2.fxml"));
            BorderPane root = loader.load();

            Scene scene = new Scene(root);
           
            this.cdsStage = new Stage();
            this.cdsStage.setTitle("ConfigRoom");
            this.cdsStage.initModality(Modality.WINDOW_MODAL);
            this.cdsStage.initOwner(_containingstage);
            this.cdsStage.setScene(scene);



            this.cdsStage.setResizable(false);

            this.ConfigdonneesViewController = loader.getController();
            this.ConfigdonneesViewController.initContext(this.cdsStage, this.AppProjetIOT, _containingstage);

        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
