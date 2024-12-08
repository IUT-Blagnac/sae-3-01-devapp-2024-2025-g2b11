package projet.application.view;

import javafx.fxml.FXML;
import javafx.scene.control.TextField;
import javafx.stage.Stage;
import projet.application.Acces.Ecrivain;
import projet.application.ProjetIOT;


import java.io.IOException;
import java.util.ArrayList;

/**
 * Contrôleur de la vue de configuration des alertes dans l'application IoT.
 *
 * Cette classe permet à l'utilisateur de configurer les seuils d'alerte pour différentes métriques
 * (température, CO2, humidité, puissance, énergie) ainsi que la fréquence d'enregistrement des données.
 * Elle gère également l'écriture de ces paramètres dans le fichier `config.ini` à l'aide de la classe {@link Ecrivain}.
 *
 */
public class ConfigAlertViewController {
    /** Fenêtre principale contenant cette vue. */
    private Stage containingStage;

    /** Fenêtre de dialogue associée. */
    private Stage dialogStage;

    /** Instance de l'écrivain responsable de l'enregistrement des configurations. */
    private Ecrivain ecrivain;

    /** Référence vers l'application principale. */
    private ProjetIOT App;

    /** Récupération des TextField de la page FXML */
    @FXML
    private TextField seuil_temperature, seuil_co2, seuil_humidity, seuil_power, seuil_energy, frequence;

    /**
     * Sauvegarde les paramètres configurés par l'utilisateur dans le fichier `config.ini`.
     *
     * Les valeurs des seuils et de la fréquence sont extraites des champs texte et validées.
     * En cas d'erreur, les seuils non valides sont remplacés par leurs valeurs par défaut.
     *
     */
    @FXML
    private void doSauvegarder(){
        try{
            //Insertion des données dans l'objet Ecrivain
            this.ecrivain.setSeuilTemp(seuil_temperature.getText().length()>0 && seuil_temperature.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_temperature.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilCo2(seuil_co2.getText().length()>0 && seuil_co2.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_co2.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilHumdity(seuil_humidity.getText().length()>0 && seuil_humidity.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_humidity.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilPower(seuil_power.getText().length()>0 && seuil_power.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_power.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setSeuilEnergy(seuil_energy.getText().length()>0 && seuil_energy.getText().matches("-?[0-9]*") ? Integer.parseInt(seuil_energy.getText()) : Integer.MAX_VALUE);
            this.ecrivain.setFrequency(frequence.getText().length()>0 && frequence.getText().matches("[0-9]*") ? Integer.parseInt(frequence.getText()) : 60);

            //On construit le fichier config.ini
            this.ecrivain.writeConfig();

        }catch (IOException e){
            System.out.println(e.getMessage());
        }
        this.containingStage.close();
        this.dialogStage.close();
        System.out.println("Sauvegarder dans le fichier config ini les données sélectionnées");
    }

    /**
     * Retourne à la vue précédente sans enregistrer les changements.
     * Ferme la fenêtre de configuration actuelle.
     */
    @FXML
    private void doRetour(){
        this.containingStage.close();
        System.out.println("Retour à la configuration des données");
    }

    /**
     * Définit la fenêtre de dialogue associée à ce contrôleur.
     *
     * @param dialogStage fenêtre de dialogue à associer.
     */
    public void setDialogStage(Stage dialogStage) {

        this.dialogStage = dialogStage;

    }


    /**
     * Initialise la vue. Cette méthode est appelée automatiquement par JavaFX après le chargement du FXML.
     */
    @FXML
    private void initialize() {

    }

    /**
     * Affiche la fenêtre de dialogue associée à ce contrôleur.
     *
     * Cette méthode bloque jusqu'à la fermeture de la fenêtre.
     *
     */
    public void DisplayDialog(){
        this.containingStage.showAndWait();
    }

    /**
     * Initialise le contexte du contrôleur avec les objets principaux de l'application.
     *
     * @param _crStage         fenêtre principale contenant cette vue.
     * @param _appProjetIOT    référence à l'application principale.
     * @param _containingstage fenêtre de dialogue contenant cette vue.
     */
    public void initContext(Stage _crStage, ProjetIOT _appProjetIOT, Stage _containingstage) {
        this.ecrivain=Ecrivain.getInstance();
        this.containingStage = _crStage;
        this.App = _appProjetIOT;
        this.dialogStage = _containingstage;

    }

}
