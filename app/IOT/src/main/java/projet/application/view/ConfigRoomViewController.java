package projet.application.view;

import javafx.fxml.FXML;
import javafx.scene.control.ToggleButton;
import javafx.stage.Stage;
import projet.application.Acces.Ecrivain;
import projet.application.ProjetIOT;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;


/**
 * Contrôleur pour la gestion de la configuration des salles dans l'application.
 * Cette classe permet de sélectionner les salles et de valider ou annuler les choix via une interface graphique.
 */
public class ConfigRoomViewController {
    /** Référence à l'application principale ProjetIOT. */
    private ProjetIOT AppProjetIOT;

    /** Fenêtre contenant l'interface de configuration des salles. */
    private Stage containingStage;

    /** Instance unique de l'objet Ecrivain pour écrire les configurations. */
    private Ecrivain ecrivain;

    /** Liste des identifiants des salles sélectionnées. */
    private List<String> listeSalles;


    /** Boutons bascule représentant les salles dans l'interface. */
    @FXML
    private ToggleButton amphi1, E208, E210, E207, E101, hallAmphi, E100, E103,
            hallEntree, B001, localVelo, foyerPers, C001,
            salleConseil, C002, E007, C102, C004, B234, E209,
               B203, B106, E004, B103, C006, E102, B110, E106,
               B202, B201, B109, B002, foyerEtu, B105,
               B111, E006, B113, B217, B112, E001;

    /** Liste des boutons ToggleButton pour manipuler leur état. */
    private List<ToggleButton> toggleButtons;

    /**
     * Initialise le contexte du contrôleur avec la fenêtre principale et l'application.
     *
     * @param _crStage    Fenêtre principale de configuration.
     * @param _appProjetIOT Référence à l'application principale ProjetIOT.
     */
    public void initContext(Stage _crStage, ProjetIOT _appProjetIOT) {
        this.containingStage = _crStage;
        this.AppProjetIOT = _appProjetIOT;
        this.configure();
    }

    /**
     * Configure les boutons bascule en leur assignant un style et une action.
     */
    private void configure() {
        this.ecrivain=Ecrivain.getInstance();
        this.listeSalles=new ArrayList<>();
        toggleButtons = Arrays.asList(amphi1, E208, E210, E207, E101, hallAmphi, E100, E103,
                hallEntree, B001, localVelo, foyerPers, C001,
                salleConseil, C002, E007, C102, C004, B234, E209,
                B203, B106, E004, B103, C006, E102, B110, E106,
                B202, B201, B109, B002, foyerEtu, B105,
                B111, E006, B113, B217, B112, E001);
        for (ToggleButton boutonCourant : this.toggleButtons){
            boutonCourant.setStyle("-fx-background-color: #800000; -fx-text-fill: white;");
            boutonCourant.setOnMouseClicked(event -> doSalle(boutonCourant));
        }

    }

    /**
     * Affiche la fenêtre de configuration des salles.
     * La méthode bloque l'exécution jusqu'à ce que la fenêtre soit fermée.
     */
    public void DisplayDialog(){
        this.containingStage.showAndWait();
    }

    /**
     * Ferme la fenêtre de configuration des salles.
     */
    @FXML
    public void doRetour(){
        this.containingStage.close();
    }

    /**
     * Valide la sélection des salles et enregistre les résultats via l'objet `Ecrivain`.
     */
    @FXML
    public void doValider(){
        String[] tab=new String[this.listeSalles.size()];
        for (int i=0;i<tab.length;i++)
            tab[i]=this.listeSalles.get(i);
        this.ecrivain.setSalles(tab);
        this.containingStage.close();
        System.out.println("Valider la configuration des salles");
    }

    /**
     * Gère l'état d'un bouton bascule lors de son clic, ajoutant ou supprimant la salle associée.
     * Change également la couleur de fond du bouton pour indiquer son état actif/inactif.
     *
     * @param bouton Bouton bascule cliqué.
     */
    public void doSalle(ToggleButton bouton){
        switch (bouton.getId()){
            case "hallAmphi":
                if (this.listeSalles.contains("hall-amphi")){
                    this.listeSalles.remove("hall-amphi");
                    bouton.setStyle("-fx-background-color: #800000; -fx-text-fill: white;");
                }else {
                    this.listeSalles.add("hall-amphi");
                    bouton.setStyle("-fx-background-color: #0a5a0a; -fx-text-fill: white;");
                }
                break;
            case "hallEntree":
                if (this.listeSalles.contains("hall-entree-principale")){
                    this.listeSalles.remove("hall-entree-principale");
                    bouton.setStyle("-fx-background-color: #800000; -fx-text-fill: white;");
                }else {
                    this.listeSalles.add("hall-entree-principale");
                    bouton.setStyle("-fx-background-color: #0a5a0a; -fx-text-fill: white;");
                }
                break;
            case "localVelo":
                if (this.listeSalles.contains("Local-velo")){
                    this.listeSalles.remove("Local-velo");
                    bouton.setStyle("-fx-background-color: #800000; -fx-text-fill: white;");
                }else {
                    this.listeSalles.add("Local-velo");
                    bouton.setStyle("-fx-background-color: #0a5a0a; -fx-text-fill: white;");
                }
                break;
            case "foyerPers":
                if (this.listeSalles.contains("Foyer-personnels")){
                    this.listeSalles.remove("Foyer-personnels");
                    bouton.setStyle("-fx-background-color: #800000; -fx-text-fill: white;");
                }else {
                    this.listeSalles.add("Foyer-personnels");
                    bouton.setStyle("-fx-background-color: #0a5a0a; -fx-text-fill: white;");
                }
                break;
            case "salleConseil":
                if (this.listeSalles.contains("Salle-conseil")){
                    this.listeSalles.remove("Salle-conseil");
                    bouton.setStyle("-fx-background-color: #800000; -fx-text-fill: white;");
                }else {
                    this.listeSalles.add("Salle-conseil");
                    bouton.setStyle("-fx-background-color: #0a5a0a; -fx-text-fill: white;");
                }
                break;
            case "foyerEtu":
                if (this.listeSalles.contains("Foyer-etudiants-entrée")){
                    this.listeSalles.remove("Foyer-etudiants-entrée");
                    bouton.setStyle("-fx-background-color: #800000; -fx-text-fill: white;");
                }else {
                    this.listeSalles.add("Foyer-etudiants-entrée");
                    bouton.setStyle("-fx-background-color: #0a5a0a; -fx-text-fill: white;");
                }
                break;
            default:
                if (this.listeSalles.contains(bouton.getId())){
                    this.listeSalles.remove(bouton.getId());
                    bouton.setStyle("-fx-background-color: #800000; -fx-text-fill: white;");
                }else {
                    this.listeSalles.add(bouton.getId());
                    bouton.setStyle("-fx-background-color: #0a5a0a; -fx-text-fill: white;");
                }
        }

    }

}

