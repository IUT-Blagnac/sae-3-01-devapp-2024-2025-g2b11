package projet.application;

import java.io.IOException;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import projet.application.view.AccueilController;

public class ProjetIOT extends Application {
    private BorderPane root;
    private Stage primarStage;

    @Override
    public void start(Stage primaryStage) throws Exception {
        this.primarStage = primaryStage;
        this.root = new BorderPane();

        Scene scene = new Scene(root);
        primaryStage.setTitle("IoT Project");
        primaryStage.setScene(scene);

        // Lancer le script Python
        lancerProgrammePython();

        // Charger l'accueil
        loadAcceuil();
        primaryStage.show();
    }

    public void loadAcceuil() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(ProjetIOT.class.getResource("view/Accueil.fxml"));
            
            BorderPane vueHome = loader.load();
            
            AccueilController Actrl = loader.getController();
            Actrl.setPrimaryStage(primarStage);

            this.root.setCenter(vueHome);
        } catch (IOException e) {
            System.out.println(e);
            System.exit(1);
        }
    }

    public void lancerProgrammePython() {
        Thread pythonThread = new Thread(() -> {
            try {
                ProcessBuilder pb = new ProcessBuilder(
                    "python", // Commande pour exécuter Python
                    "app/IOT/python/IoTPythonSAEvFinal.py" // Chemin relatif vers le script Python
                );
                pb.redirectOutput(ProcessBuilder.Redirect.INHERIT);
                pb.redirectError(ProcessBuilder.Redirect.INHERIT);
                pb.start();
            } catch (IOException e) {
                System.err.println("Erreur lors de l'exécution du script Python : " + e.getMessage());
                e.printStackTrace();
            }
        });
        pythonThread.setDaemon(true);
        pythonThread.start();
    }

    @Override
    public void stop() throws Exception {
        super.stop();
        System.out.println("Arrêt de l'application. Assurez-vous que le script Python est également arrêté si nécessaire.");
    }

    public static void main(String[] args) {
        launch(args);
    }
}
