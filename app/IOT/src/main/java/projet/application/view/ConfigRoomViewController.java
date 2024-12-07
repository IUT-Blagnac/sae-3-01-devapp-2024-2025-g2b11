package projet.application.view;

import javafx.fxml.FXML;
import javafx.scene.control.ToggleButton;
import javafx.stage.Stage;
import projet.application.Acces.Ecrivain;
import projet.application.ProjetIOT;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;


public class ConfigRoomViewController {
    private ProjetIOT AppProjetIOT;
    private Stage containingStage;
    private Ecrivain ecrivain;
    private List<String> listeSalles;

    @FXML
    private ToggleButton amphi1, E208, E210, E207, E101, hallAmphi, E100, E103,
            hallEntree, B001, localVelo, foyerPers, C001,
            salleConseil, C002, E007, C102, C004, B234, E209,
               B203, B106, E004, B103, C006, E102, B110, E106,
               B202, B201, B109, B002, foyerEtu, B105,
               B111, E006, B113, B217, B112, E001;
    private List<ToggleButton> toggleButtons;

    // Méthode pour les initialiser




    public void initContext(Stage _crStage, ProjetIOT _appProjetIOT) {
        this.containingStage = _crStage;
        this.AppProjetIOT = _appProjetIOT;
        this.configure();
    }
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

    public void DisplayDialog(){
        this.containingStage.showAndWait();
    }

    @FXML
    public void doRetour(){
        this.containingStage.close();
    }   

    @FXML
    public void doValider(){
        String[] tab=new String[this.listeSalles.size()];
        for (int i=0;i<tab.length;i++)
            tab[i]=this.listeSalles.get(i);
        this.ecrivain.setSalles(tab);
        this.containingStage.close();
        System.out.println("Valider la configuration des salles");
    }

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

