package projet.application.view;

import javafx.fxml.FXML;
import javafx.stage.Stage;
import projet.application.Acces.Ecrivain;
import projet.application.ProjetIOT;

import java.io.IOException;
import java.util.ArrayList;

public class ConfigAlertViewController {
    private Stage containingStage;

    private Stage dialogStage;

    private Ecrivain ecrivain;

    private ProjetIOT App;

    @FXML
    private void doSauvegarder() throws IOException {
        try{


            //On construit le fichier config.ini
            this.ecrivain.writeConfig();

        }catch (IOException e){
            System.out.println(e.getMessage());
        }
        this.containingStage.close();
        this.dialogStage.close();
        System.out.println("Sauvegarder dans le fichier config ini les données sélectionnées");
    }

    @FXML
    private void doRetour(){
        this.containingStage.close();
        System.out.println("Retour à la configuration des données");
    }












    public void setDialogStage(Stage dialogStage) {

        this.dialogStage = dialogStage;

    }


    // Méthode pour gérer les changements dans le groupe de boutons radio
    @FXML
    private void initialize() {

    }
    public void DisplayDialog(){
        this.containingStage.showAndWait();
    }

    public void initContext(Stage _crStage, ProjetIOT _appProjetIOT, Stage _containingstage) {
        this.ecrivain=Ecrivain.getInstance();
        this.containingStage = _crStage;
        this.App = _appProjetIOT;
        this.dialogStage = _containingstage;

    }
}
