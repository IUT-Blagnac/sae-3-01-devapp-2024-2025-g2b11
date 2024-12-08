package projet.application.view;


import javafx.fxml.FXML;
import javafx.scene.control.CheckBox;
import javafx.stage.Stage;
import projet.application.Acces.Ecrivain;
import projet.application.ProjetIOT;
import projet.application.control.ConfigAlert;
import projet.application.control.ConfigDataSelect;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class ConfigDataSelect2ViewController {

    private Stage containingStage;

    private Stage dialogStage;
    private Ecrivain ecrivain;

    private ProjetIOT App;

    @FXML
    private CheckBox temperature, co2, humidity, activity, tvoc, illumination, infrared, infrared_and_visible, pressure, currentPower, lastDayData;
    private List<CheckBox> listeCheckBox;
    private List<String> typeTabCapt;
    private List<String> typeTabSolaire;

    @FXML
    private void doSauvegarder() throws IOException {
        //On ajoute les types de données à récupérer pour les capteurs
        String[] tab=new String[this.typeTabCapt.size()];
        for (int i=0;i<tab.length;i++)
            tab[i]=this.typeTabCapt.get(i);
        this.ecrivain.setTypesDonnee(tab);

        //On ajoute les types de données à récupérer pour les panneaux solaires
        tab=new String[this.typeTabSolaire.size()];
        for (int i=0;i<tab.length;i++)
            tab[i]=this.typeTabSolaire.get(i);
        this.ecrivain.setTypesSolaire(tab);


        ConfigAlert configAlert = new ConfigAlert(this.containingStage, this.App, this.dialogStage);
        configAlert.doConfigDonnees();

        
        this.containingStage.close();
        this.dialogStage.close();
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
        this.typeTabCapt =new ArrayList<>();
        this.typeTabSolaire =new ArrayList<>();
        this.ecrivain=Ecrivain.getInstance();
        this.initRadio();
        this.containingStage = _crStage;
        this.App = _appProjetIOT;
        this.dialogStage = _containingstage;

    }

    public void initRadio(){
        this.listeCheckBox=new ArrayList<>(Arrays.asList(temperature, co2, humidity, activity, tvoc, illumination, infrared, infrared_and_visible, pressure, currentPower, lastDayData));
        for (CheckBox c : this.listeCheckBox)
            c.setOnAction(event -> this.clicked(c));
    }

    public void clicked(CheckBox checkBox){
        if (checkBox.getId().equals("currentPower") || checkBox.getId().equals("lastDayData")){
            if (checkBox.isSelected()){
                System.out.println(checkBox.getId());
                this.typeTabSolaire.add(checkBox.getId());
            }else {
                this.typeTabSolaire.remove(checkBox.getId());
                System.out.println(checkBox.getId());
            }
        }else
            if (checkBox.isSelected()){
                System.out.println(checkBox.getId());
                this.typeTabCapt.add(checkBox.getId());
            }else {
                this.typeTabCapt.remove(checkBox.getId());
                System.out.println(checkBox.getId());
            }
    }
}
