package projet.application.control;

import projet.application.ProjetIOT;
import projet.application.Acces.Reader;
import projet.application.view.DashboardViewController;

import java.io.IOException;
import java.util.List;

import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;

public class Dashboard {

    private static final String JSON_FILE_PATH = "donneeJSON.json";
    private Stage dashboardStage;
    private DashboardViewController dashboardController;
    private ProjetIOT app;

    public Dashboard(Stage parentStage, ProjetIOT app) {
        this.app = app;
        try {
            FXMLLoader loader = new FXMLLoader(DashboardViewController.class.getResource("Dashboard.fxml"));
            BorderPane root = loader.load();

            Scene scene = new Scene(root);

            this.dashboardStage = new Stage();
            this.dashboardStage.setTitle("Tableau de Bord");
            this.dashboardStage.initModality(Modality.WINDOW_MODAL);
            this.dashboardStage.initOwner(parentStage);
            this.dashboardStage.setScene(scene);

            this.dashboardController = loader.getController();
            this.dashboardController.initContext(this.dashboardStage, this.app);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    public void showDashboard() {
        this.dashboardStage.show();
    }
    // Méthode pour récupérer les noms des salles
    public static List<String> getRoomNames() {
        return Reader.getRoomNames(JSON_FILE_PATH);
    }


    // Méthode pour récupérer les données en fonction du type sélectionné
    public static List<Float> getData(String selectionType, String specificSelection, String dataType) {
        if ("Salle".equalsIgnoreCase(selectionType)) {
            return Reader.getDataForRoomAndType(specificSelection, dataType, JSON_FILE_PATH);
        } else if ("Panneaux Solaires".equalsIgnoreCase(selectionType)) {
            return Reader.getSolarDataByType(dataType, JSON_FILE_PATH);
        }
        return List.of();
    }
}
