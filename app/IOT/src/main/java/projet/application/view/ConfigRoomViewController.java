package projet.application.view;

import javafx.fxml.FXML;
import javafx.scene.control.ToggleButton;
import javafx.scene.control.ToggleGroup;
import javafx.stage.Stage;
import projet.application.ProjetIOT;

import java.awt.*;




public class ConfigRoomViewController {
    private ProjetIOT AppProjetIOT;
    private Stage containingStage;

    @FXML
    private ToggleButton amphi1, salleE208, salleE210, salleE207, salleE101, salleHallAmphi, salleE100, salleE103, 
               salleHallEntreePrincipale, salleB001, salleLocalVelo, salleFoyerPersonnels, salleC001, 
               salleConseil, salleC002, salleE007, salleC102, salleC004, salleB234, salleE209, 
               salleB203, salleB106, salleE004, salleB103, salleC006, salleE102, salleB110, salleE106, 
               salleB202, salleB201, salleB109, salleB002, salleFoyerEtudiantsEntree, salleB105, 
               salleB111, salleE006, salleB113, salleB217, salleB112, salleE001;



    public void initContext(Stage _crStage, ProjetIOT _appProjetIOT) {
        this.containingStage = _crStage;
        this.AppProjetIOT = _appProjetIOT;
        this.configure();
    }
    private void configure() {
        System.out.println("Developpement");
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
        this.containingStage.close();
        System.out.println("Valider la configuration des salles");
    }

}

