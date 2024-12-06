package projet.application.control;

import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;
import projet.application.ProjetIOT;
import projet.application.view.ConfigRoomViewController;

import java.io.IOException;

public class ConfigRoom {
    private Stage crStage;
    private ConfigRoomViewController crViewController;
    private ProjetIOT AppProjetIOT;

    public void doConfigroom(){
        this.crViewController.DisplayDialog();
    }

    public ConfigRoom(Stage _parentStage, ProjetIOT _AppProjetIOT) {
        this.AppProjetIOT = _AppProjetIOT;
        try{
            FXMLLoader loader = new FXMLLoader(ConfigRoomViewController.class.getResource("ConfigRoomSelect.fxml"));
            BorderPane root = loader.load();

            Scene scene = new Scene(root);

            this.crStage = new Stage();
            this.crStage.initModality(Modality.WINDOW_MODAL);
            this.crStage.initOwner(_parentStage);
            this.crStage.setScene(scene);
            this.crStage.setTitle("ConfigRoom");
            this.crStage.setResizable(false);

            this.crViewController = loader.getController();
            this.crViewController.initContext(this.crStage, this.AppProjetIOT);

        }
        catch(IOException e){
            e.printStackTrace();
        }


    }
}
