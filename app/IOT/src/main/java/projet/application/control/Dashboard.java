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

    /**
     * Constructeur de la classe `Dashboard`.
     *
     * @param parentStage La fenêtre parent de laquelle dépend le tableau de bord.
     * @param app L'instance principale de l'application `ProjetIOT`.
     *
     * Ce constructeur initialise une nouvelle fenêtre pour afficher le tableau de bord :
     * - Charge la vue FXML `Dashboard.fxml`.
     * - Configure la fenêtre en tant que fenêtre modale pour la fenêtre parent.
     * - Associe le contrôleur `DashboardViewController` à la vue chargée.
     * - Passe le contexte de l'application et de la fenêtre au contrôleur.
     *
     * En cas d'erreur lors du chargement de la vue, l'exception est imprimée dans la console.
     */
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


    /**
     * Méthode statique pour récupérer les données en fonction du type sélectionné.
     *
     * @param selectionType Le type de sélection : "Salle" ou "Panneaux Solaires".
     * @param specificSelection Le nom spécifique de la salle ou une autre entité sélectionnée.
     * @param dataType Le type de données à récupérer (par exemple, température, humidité, etc.).
     * @return Une liste de valeurs flottantes correspondant aux données récupérées. Renvoie une liste vide si aucune donnée n'est disponible.
     *
     * Cette méthode :
     * - Appelle `Reader.getDataForRoomAndType` pour récupérer les données d'une salle.
     * - Appelle `Reader.getSolarDataByType` pour récupérer les données des panneaux solaires.
     * - Renvoie une liste vide si le type de sélection n'est ni "Salle" ni "Panneaux Solaires".
     */
    public static List<Float> getData(String selectionType, String specificSelection, String dataType) {
        if ("Salle".equalsIgnoreCase(selectionType)) {
            return Reader.getDataForRoomAndType(specificSelection, dataType, JSON_FILE_PATH);
        } else if ("Panneaux Solaires".equalsIgnoreCase(selectionType)) {
            return Reader.getSolarDataByType(dataType, JSON_FILE_PATH);
        }
        return List.of();
    }
}
