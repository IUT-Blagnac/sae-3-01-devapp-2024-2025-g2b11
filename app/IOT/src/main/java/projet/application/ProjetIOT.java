package projet.application;

import java.io.IOException;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;
import projet.application.view.AccueilViewController;
import projet.application.view.ConfigDataSelectViewController;
import projet.application.view.DashboardViewController;

public class ProjetIOT extends Application {
    private BorderPane root;
    private Stage primarStage;

    @Override
    public void start(Stage primaryStage) throws Exception {
        this.primarStage = primaryStage;
        this.root = new BorderPane();

        Scene scene = new Scene(root);
        //Recupere l'icone du jeu
        primarStage.setTitle("IoT Project");
        primarStage.setScene(scene);
        loadAccueil();
        primarStage.show(); //Lancement du Launcher du jeu
    }

    public void loadAccueil() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(ProjetIOT.class.getResource("view/Accueil.fxml"));
            
            BorderPane vueHome = loader.load();
            
            AccueilViewController Actrl = loader.getController();
            Actrl.setPrimaryStage(primarStage);
            Actrl.setPorjetApp(this);
            
            this.root.setCenter(vueHome);
                        
        } catch (IOException e) {
            System.out.println("Erreur lors du chargement de la vue Accueil.fxml");
            System.exit(1);
        }	
    }

    public void loadSelectConfig() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(ProjetIOT.class.getResource("view/ConfigDataSelect.fxml"));

            BorderPane bp = loader.load();

            Scene scene = new Scene(bp);
            
            Stage dialoStage = new Stage();
            dialoStage.setTitle("Configuration des données");
            dialoStage.initModality(Modality.WINDOW_MODAL);
            dialoStage.initOwner(this.primarStage);
            dialoStage.setScene(scene);
            

            ConfigDataSelectViewController ConfigDataSelectctrl = loader.getController();
            ConfigDataSelectctrl.setDialogStage(dialoStage);
            ConfigDataSelectctrl.setProjetApp(this);
            
            dialoStage.show();

            
            

        } catch (IOException e) {
            System.out.println(e);
            System.exit(1);
        }
    }

    public void loadDashboard() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(ProjetIOT.class.getResource("view/dashboard.fxml"));
    
            BorderPane dashboardPane = loader.load();
    
            // Création d'un nouveau stage pour le tableau de bord
            Stage dashboardStage = new Stage();
            dashboardStage.setTitle("Tableau de Bord");
            dashboardStage.initModality(Modality.WINDOW_MODAL); // Fenêtre modale
            dashboardStage.initOwner(this.primarStage); // Définit la fenêtre principale comme parent
            Scene scene = new Scene(dashboardPane);
            dashboardStage.setScene(scene);
    
            // Initialisation du contrôleur
            DashboardViewController dashboardCtrl = loader.getController();
            dashboardCtrl.setDialogStage(dashboardStage);
            dashboardCtrl.setProjetApp(this);
    
            dashboardStage.show();
    
        } catch (IOException e) {
            System.err.println("Erreur lors du chargement du tableau de bord : " + e.getMessage());
            e.printStackTrace();
        }
    }
    
    

    public static void main(String[] args) {
        launch(args);
    }
}