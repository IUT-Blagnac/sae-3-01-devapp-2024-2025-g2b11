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

public class ConfigAlert {

    private Stage caStage;
    private ConfigAlertViewController ConfigAlertViewController;
    private ProjetIOT AppProjetIOT;


    public void doConfigDonnees() {
        this.ConfigAlertViewController.DisplayDialog();
    }

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
