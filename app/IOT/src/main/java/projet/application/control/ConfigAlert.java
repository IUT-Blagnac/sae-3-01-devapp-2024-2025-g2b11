package projet.application.control;

import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;
import projet.application.ProjetIOT;
import projet.application.view.ConfigAlertViewController;
import projet.application.view.ConfigDataSelect2ViewController;
import projet.application.view.ConfigRoomViewController;

import java.io.IOException;

/**
 * Contrôleur pour la gestion de la configuration des alertes.
 *
 * Cette classe est responsable de l'initialisation et de l'affichage
 * de l'interface utilisateur permettant de configurer les alertes.
 * Elle gère également le contexte nécessaire pour l'interaction entre
 * les différents éléments de l'application.
 *
 */
public class ConfigAlert {

    /** Stage utilisé pour afficher la fenêtre de configuration des alertes. */
    private Stage caStage;
    /** Contrôleur associé à la vue de configuration des alertes. */
    private ConfigAlertViewController ConfigAlertViewController;
    /** Instance principale de l'application. */
    private ProjetIOT AppProjetIOT;


    /**
     * Affiche la boîte de dialogue de configuration des alertes.
     *
     * Cette méthode demande au contrôleur de la vue associée
     * d'afficher la fenêtre pour permettre à l'utilisateur de
     * configurer les paramètres des alertes.
     */
    public void doConfigDonnees() {
        this.ConfigAlertViewController.DisplayDialog();
    }

    /**
     * Constructeur de la classe ConfigAlert.
     *
     * Initialise le contrôleur, charge la vue associée, et configure le stage
     * pour la fenêtre de configuration des alertes.
     *
     * @param _parentStage   le stage parent de la fenêtre de configuration.
     * @param _AppProjetIOT  l'instance principale de l'application {@link ProjetIOT}.
     * @param _containingstage le stage de la boîte de dialogue parent.
     */
    public ConfigAlert(Stage _parentStage, ProjetIOT _AppProjetIOT, Stage _containingstage) {
        try {
            this.AppProjetIOT = _AppProjetIOT;
            FXMLLoader loader = new FXMLLoader(ConfigRoomViewController.class.getResource("ConfigAlertSelect.fxml"));
            BorderPane root = loader.load();

            Scene scene = new Scene(root);

            this.caStage = new Stage();
            this.caStage.setTitle("ConfigAlerte");
            this.caStage.initModality(Modality.WINDOW_MODAL);
            this.caStage.initOwner(_containingstage);
            this.caStage.setScene(scene);



            this.caStage.setResizable(false);

            this.ConfigAlertViewController = loader.getController();
            this.ConfigAlertViewController.initContext(this.caStage, this.AppProjetIOT, _containingstage);

        } catch (IOException e) {
            e.printStackTrace();
        }
    }
}
