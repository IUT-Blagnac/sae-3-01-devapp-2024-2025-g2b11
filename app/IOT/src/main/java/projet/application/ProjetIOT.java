package projet.application;

import java.io.IOException;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.image.Image;
import javafx.scene.layout.BorderPane;
import javafx.stage.Stage;
import projet.application.view.AccueilViewController;

public class ProjetIOT extends Application {
    private BorderPane root;
    private Stage primarStage;

    @Override
    public void start(Stage primaryStage) throws Exception {
        this.primarStage = primaryStage;
        this.root = new BorderPane();

        Scene scene = new Scene(root);
        //Recupere l'icone du jeu
        primarStage.setTitle("Akari");
        primarStage.setScene(scene);
        loadAcceuil(); 
        primarStage.show(); //Lancement du Launcher du jeu
    }

    public void loadAcceuil() {
        try {
            FXMLLoader loader = new FXMLLoader();
            loader.setLocation(ProjetIOT.class.getResource("view/accueilpane.fxml"));
            
            BorderPane vueHome = loader.load();
            
            AccueilViewController Actrl = loader.getController();
            Actrl.setPrimaryStage(primarStage);

            
            this.root.setCenter(vueHome);
                        
        } catch (IOException e) {
            System.out.println("Ressource FXML non disponible : LoadAccueil");
            System.exit(1);
        }	
    }

    public static void main(String[] args) {
        launch(args);
    }
}