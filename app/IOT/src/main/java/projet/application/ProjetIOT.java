package projet.application;

import java.io.IOException;
import java.time.LocalDateTime;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Modality;
import javafx.stage.Stage;
import projet.application.control.Dashboard;
import projet.application.Acces.AlertManager;
import projet.application.Acces.RunPythonBackground;
import projet.application.view.AccueilViewController;
import projet.application.view.ConfigDataSelectViewController;
import projet.application.view.DashboardViewController;

public class ProjetIOT extends Application {
    private BorderPane root;
    private Stage primarStage;
    private int exitCodeMQTT;
    private AlertManager alertManager;

    @Override
    public void start(Stage primaryStage) throws Exception {
        this.primarStage = primaryStage;
        this.root = new BorderPane();

        LocalDateTime appLaunchTime = LocalDateTime.now();
        this.alertManager = new AlertManager(appLaunchTime);

        Thread alertThread = new Thread(alertManager);
        alertThread.setDaemon(true);
        alertThread.start();

        Scene scene = new Scene(root);
        // Recupere l'icone du jeu
        primarStage.setTitle("IoT Project");
        primarStage.setScene(scene);
        loadAccueil();
        primarStage.show(); // Lancement du Launcher du jeu
    }

    public void loadAccueil() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(ProjetIOT.class.getResource("view/Accueil.fxml"));

            BorderPane vueHome = loader.load();

            AccueilViewController Actrl = loader.getController();
            Actrl.setPrimaryStage(primarStage);
            Actrl.setPorjetApp(this);

            Actrl.setAlertManager(this.alertManager);

            this.root.setCenter(vueHome);

            LoadPython("tes");

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
        Dashboard dashboard = new Dashboard(this.primarStage, this,this.alertManager);
        dashboard.showDashboard();
    }

    public void LoadPython(String args) {
        RunPythonBackground pythonRunner = new RunPythonBackground(args);
        Thread pythonThread = new Thread(pythonRunner);
        pythonThread.setDaemon(true); // S'arrête automatiquement quand l'application ferme
        pythonThread.start();

        if (args.equals("test")) {
            try {
                pythonThread.join();
                int exitCode = pythonRunner.getExitCode();
                this.exitCodeMQTT = exitCode;
                System.out.println("Code de sortie récupéré dans le Main : " + exitCode);

                if (exitCode == 0) {
                    System.out.println("Le script Python s'est terminé avec succès.");
                } else {
                    System.out.println("Le script Python a rencontré une erreur.");
                }
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
    }

    public int getExitCodeMQTT() {
        return this.exitCodeMQTT;
    }   


    public static void main(String[] args) {
        launch(args);
    }
}