package projet.application.view;


import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.CheckBox;
import javafx.scene.control.RadioButton;
import javafx.scene.control.ToggleGroup;
import javafx.stage.Stage;
import projet.application.ProjetIOT;
import projet.application.control.ConfigRoom;

public class ConfigDataSelect2ViewController {

    private Stage containingStage;

    private Stage dialogStage;

    private ProjetIOT App;

    @FXML
    private CheckBox temperature, co2, humidite, activite, tvoc, illumination, infrarouges, infravisible, pression, energie, puissance;
    
    @FXML
    private void doSauvegarder(){
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
        this.containingStage = _crStage;
        this.App = _appProjetIOT;
        this.dialogStage = _containingstage;

    }
}
