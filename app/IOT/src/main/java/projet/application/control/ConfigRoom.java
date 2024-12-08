package projet.application.control;

import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;
import projet.application.ProjetIOT;
import projet.application.view.ConfigRoomViewController;

import java.io.IOException;

/**
 * Classe responsable de la gestion et de l'affichage de la fenêtre de configuration des salles.
 * Cette classe initialise l'interface de sélection des salles et permet de lancer la configuration.
 */
public class ConfigRoom {
    /** Fenêtre dédiée à la configuration des salles. */
    private Stage crStage;

    /** Contrôleur associé à la vue de configuration des salles. */
    private ConfigRoomViewController crViewController;

    /** Référence à l'application principale ProjetIOT. */
    private ProjetIOT AppProjetIOT;

    /**
     * Affiche la fenêtre de configuration des salles.
     * Cette méthode appelle la méthode correspondante du contrôleur associé.
     */
    public void doConfigroom(){
        this.crViewController.DisplayDialog();
    }

    /**
     * Constructeur de la classe ConfigRoom.
     * Initialise la fenêtre de configuration des salles, charge la vue depuis un fichier FXML, et configure le contrôleur associé.
     *
     * @param _parentStage   Stage parent de la fenêtre de configuration.
     * @param _AppProjetIOT  Référence à l'application principale ProjetIOT.
     */
    public ConfigRoom(Stage _parentStage, ProjetIOT _AppProjetIOT) {
        this.AppProjetIOT = _AppProjetIOT;
        try{
            FXMLLoader loader = new FXMLLoader(ConfigRoomViewController.class.getResource("configroomselect.fxml"));
            BorderPane root = loader.load();

            Scene scene = new Scene(root);

            this.crStage = new Stage();
            this.crStage.setTitle("ConfigRoom");
            this.crStage.initModality(Modality.WINDOW_MODAL);
            this.crStage.initOwner(_parentStage);
            this.crStage.setScene(scene);
            
            this.crStage.setResizable(false);

            this.crViewController = loader.getController();
            this.crViewController.initContext(this.crStage, this.AppProjetIOT);

        }
        catch(IOException e){
            e.printStackTrace();
        }


    }
}
