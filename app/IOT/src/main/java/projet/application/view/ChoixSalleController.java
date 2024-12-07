package projet.application.view;

import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.scene.control.Button;
import javafx.scene.control.ToggleButton;
import javafx.scene.layout.GridPane;
import javafx.stage.Stage;
import projet.application.Acces.Reader;

import java.util.List;

public class ChoixSalleController {

    @FXML
    private GridPane gridPaneSalles;

    @FXML
    private Button boutonRetour;

    @FXML
    private Button boutonValider;

    private Stage stage;

    private ToggleButton selectedButton = null; // Bouton actuellement sélectionné

    @FXML
    public void initialize() {
        // Charger les noms des salles depuis le fichier JSON
        List<String> roomNames = Reader.getRoomNames("dataJSON.json");

        // Mettre à jour les boutons existants
        int index = 0;
        for (int row = 0; row < gridPaneSalles.getRowCount(); row++) {
            for (int col = 0; col < gridPaneSalles.getColumnCount(); col++) {
                if (index >= roomNames.size()) {
                    break; // Sortir si toutes les salles sont assignées
                }

                ToggleButton button = (ToggleButton) getNodeFromGridPane(gridPaneSalles, col, row);
                if (button != null) {
                    button.setText(roomNames.get(index));
                    button.setDisable(false);
                    button.setOnAction(event -> handleRoomSelection(button));
                    index++;
                }
            }
        }
    }

    /**
     * Gère la sélection d'une salle via un bouton.
     *
     * @param button le bouton sélectionné
     */
    private void handleRoomSelection(ToggleButton button) {
        if (selectedButton != null) {
            selectedButton.setSelected(false); // Désélectionner le bouton précédent
        }
        selectedButton = button;
        selectedButton.setSelected(true); // Sélectionner le bouton actuel
        System.out.println("Salle sélectionnée : " + selectedButton.getText());
    }

    /**
     * Retourne le noeud spécifique d'une grille donnée.
     *
     * @param gridPane la grille
     * @param colIndex colonne cible
     * @param rowIndex ligne cible
     * @return le noeud (ou null si inexistant)
     */
    private ToggleButton getNodeFromGridPane(GridPane gridPane, int colIndex, int rowIndex) {
        for (javafx.scene.Node node : gridPane.getChildren()) {
            if (GridPane.getColumnIndex(node) != null &&
                GridPane.getColumnIndex(node) == colIndex &&
                GridPane.getRowIndex(node) != null &&
                GridPane.getRowIndex(node) == rowIndex) {
                return (ToggleButton) node;
            }
        }
        return null;
    }

    /**
     * Action du bouton Retour : revenir à l'accueil.
     */
    @FXML
    private void handleRetour(ActionEvent event) {
        try {
            // Retourner à l'écran d'accueil
            javafx.fxml.FXMLLoader loader = new javafx.fxml.FXMLLoader(getClass().getResource("Accueil.fxml"));
            javafx.scene.Scene accueilScene = new javafx.scene.Scene(loader.load());

            AccueilController accueilController = loader.getController();
            accueilController.setPrimaryStage(stage);

            stage.setScene(accueilScene);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * Action du bouton Valider : passer à l'écran des graphiques avec la salle sélectionnée.
     */
    @FXML
    private void handleValider(ActionEvent event) {
        if (selectedButton == null) {
            System.out.println("Aucune salle sélectionnée.");
            return;
        }
        try {
            javafx.fxml.FXMLLoader loader = new javafx.fxml.FXMLLoader(getClass().getResource("graphpane.fxml"));
            javafx.scene.Scene graphsScene = new javafx.scene.Scene(loader.load());

            GraphsController graphsController = loader.getController();
            graphsController.setSalle(selectedButton.getText());
            graphsController.setStage(stage);
            stage.setScene(graphsScene);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void setStage(Stage stage) {
        this.stage = stage;
    }
}
