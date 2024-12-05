package projet;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;

public class ProjetIOT extends Application {

    @Override
    public void start(Stage primaryStage) throws Exception {
        // Charger le fichier FXML
        FXMLLoader loader = new FXMLLoader(getClass().getResource("MainView.fxml"));
        BorderPane root = loader.load();

        // Créer la scène avec le layout chargé
        Scene scene = new Scene(root);

        // Configurer la fenêtre principale
        primaryStage.setTitle("Projet IOT");
        primaryStage.setScene(scene);
        primaryStage.show();
    }

    public static void main(String[] args) {
        launch(args);
    }
}