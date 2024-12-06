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

public class ConfigDataSelect {
    private Stage cdsStage;
    private ConfigDataSelect2ViewController ConfigdonneesViewController;
    private ProjetIOT AppProjetIOT;

    public void doConfigDonnees() {
        this.ConfigdonneesViewController.DisplayDialog();
    }

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
