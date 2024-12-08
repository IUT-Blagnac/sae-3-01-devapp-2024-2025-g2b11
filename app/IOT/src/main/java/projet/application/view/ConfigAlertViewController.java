package projet.application.view;

import javafx.fxml.FXML;
import javafx.scene.control.TextField;
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
    private TextField seuil_temperature, seuil_co2, seuil_humidity, seuil_power, seuil_energy, frequence;

    @FXML
    private void doSauvegarder() throws IOException {
        try{
            this.ecrivain.setSeuilTemp(seuil_temperature.getText().length()>0 && seuil_temperature.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_temperature.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilCo2(seuil_co2.getText().length()>0 && seuil_co2.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_co2.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilHumdity(seuil_humidity.getText().length()>0 && seuil_humidity.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_humidity.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilPower(seuil_power.getText().length()>0 && seuil_power.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_power.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilEnergy(seuil_energy.getText().length()>0 && seuil_energy.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_energy.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilEnergy(frequence.getText().length()>0 && frequence.getText().matches("[0-9]*") ? Integer.parseInt(frequence.getText()) : 60);

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
        this.initTextFields();
        this.containingStage = _crStage;
        this.App = _appProjetIOT;
        this.dialogStage = _containingstage;

    }

    public void initTextFields(){

    }
}
