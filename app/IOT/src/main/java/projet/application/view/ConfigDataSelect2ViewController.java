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

/**
 * Contrôleur pour la deuxième étape de la configuration des données.
 *
 * Permet à l'utilisateur de sélectionner les types de données à surveiller
 * pour les capteurs et les panneaux solaires. Les choix sont ensuite sauvegardés
 * et transmis à l'instance de {@link Ecrivain} pour la génération des configurations.
 *
 */
public class ConfigDataSelect2ViewController {

    /** Stage contenant cette vue. */
    private Stage containingStage;

    /** Stage de la boîte de dialogue actuelle. */
    private Stage dialogStage;

    /** Instance de l'application principale. */
    private ProjetIOT App;

    /** Instance de {@link Ecrivain} pour sauvegarder les choix de configuration. */
    private Ecrivain ecrivain;

    /** Cases à cocher pour les données disponibles. */
    @FXML
    private CheckBox temperature, co2, humidity, activity, tvoc, illumination, infrared, infrared_and_visible, pressure, currentPower, lastDayData;

    /** Liste des cases à cocher dans l'interface. */
    private List<CheckBox> listeCheckBox;

    /** Liste des types de données sélectionnés pour les capteurs. */
    private List<String> typeTabCapt;

    /** Liste des types de données sélectionnés pour les panneaux solaires. */
    private List<String> typeTabSolaire;

    /**
     * Sauvegarde les choix de types de données et passe à l'étape suivante.
     *
     * Les choix de données pour les capteurs et les panneaux solaires sont ajoutés à
     * l'instance {@link Ecrivain}, et la configuration des alertes est lancée via {@link ConfigAlert}.
     *
     * @throws IOException si une erreur d'entrée/sortie se produit.
     */
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

    /**
     * Retourne à l'étape précédente de la configuration.
     *
     * Ferme la fenêtre actuelle sans sauvegarder les modifications.
     *
     */
    @FXML
    private void doRetour(){
        this.containingStage.close();
        System.out.println("Retour à la configuration des données");
    }

    /**
     * Définit le stage de la boîte de dialogue actuelle.
     *
     * @param dialogStage le stage de la boîte de dialogue.
     */
    public void setDialogStage(Stage dialogStage) {

        this.dialogStage = dialogStage;

    }


    // Méthode pour gérer les changements dans le groupe de boutons radio
    @FXML
    private void initialize() {
       
    }
    /**
     * Affiche la boîte de dialogue associée à cette vue.
     *
     * Cette méthode attend que l'utilisateur ferme la boîte de dialogue.
     *
     */
    public void DisplayDialog(){
        this.containingStage.showAndWait();
    }

    /**
     * Initialise le contexte de la vue.
     *
     * Prépare les listes pour les types de données, configure l'instance {@link Ecrivain}
     * et initialise les gestionnaires d'événements pour les cases à cocher.
     *
     *
     * @param _crStage       le stage contenant cette vue.
     * @param _appProjetIOT  l'instance principale de l'application.
     * @param _containingstage le stage de la boîte de dialogue.
     */

    public void initContext(Stage _crStage, ProjetIOT _appProjetIOT, Stage _containingstage) {
        this.typeTabCapt =new ArrayList<>();
        this.typeTabSolaire =new ArrayList<>();
        this.ecrivain=Ecrivain.getInstance();
        this.initRadio();
        this.containingStage = _crStage;
        this.App = _appProjetIOT;
        this.dialogStage = _containingstage;

    }

    /**
     * Initialise les gestionnaires d'événements pour les cases à cocher.
     *
     * Chaque case à cocher est liée à une action qui ajoute ou supprime
     * le type de données sélectionné dans les listes correspondantes.
     *
     */
    public void initRadio(){
        this.listeCheckBox=new ArrayList<>(Arrays.asList(temperature, co2, humidity, activity, tvoc, illumination, infrared, infrared_and_visible, pressure, currentPower, lastDayData));
        for (CheckBox c : this.listeCheckBox)
            c.setOnAction(event -> this.clicked(c));
    }

    /**
     * Gère les clics sur les cases à cocher.
     *
     * Ajoute ou supprime le type de données de la liste correspondante
     * (capteurs ou panneaux solaires) en fonction de la case cochée.
     *
     * @param checkBox la case à cocher sur laquelle l'utilisateur a cliqué.
     */
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
