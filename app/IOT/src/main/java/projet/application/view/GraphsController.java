package projet.application.view;

import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.event.ActionEvent;
import javafx.fxml.FXML;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.stage.Stage;
import projet.application.Acces.Reader;

import java.io.File;
import java.nio.file.*;
import java.util.List;

public class GraphsController {

    @FXML
    private Label labelSalle;
    @FXML
    private LineChart<String, Number> lineChart;
    @FXML
    private MenuButton menuChoixDonnee;
    @FXML
    private ListView<String> listViewDonnees;
    @FXML
    private javafx.scene.control.Button boutonRetour;

    private String currentRoom = "Non sélectionnée"; // Salle sélectionnée
    private String currentType = "température";     // Type de donnée par défaut
    private final String dataFilePath = "dataJSON.json";

    private Thread fileWatcherThread;
    private Stage stage;

    public void setSalle(String salle) {
        this.currentRoom = salle;
        labelSalle.setText("Salle : " + salle);
        refreshGraph(); // Actualise lorsque la salle change
    }

    public void setStage(Stage stage) {
        this.stage = stage;
    }

    @FXML
    public void initialize() {
        // Initialiser le menu pour les types de données
        initializeMenu();

        // Mettre en place la surveillance du fichier pour actualisation automatique
        setupFileWatcher();
    }

    private void initializeMenu() {
        menuChoixDonnee.getItems().clear();
        List<String> typesDonnees = List.of("Température", "Humidité", "CO2");

        for (String type : typesDonnees) {
            MenuItem item = new MenuItem(type);
            item.setOnAction(event -> {
                currentType = type.toLowerCase();
                menuChoixDonnee.setText("Type: " + type);
                refreshGraph();
            });
            menuChoixDonnee.getItems().add(item);
        }
    }

    private void refreshGraph() {
        List<Float> data = Reader.getDataForRoomAndType(currentRoom, currentType, dataFilePath);

        if (data == null || data.isEmpty()) {
            Platform.runLater(() -> {
                lineChart.getData().clear();
                listViewDonnees.setItems(FXCollections.observableArrayList("Aucune donnée disponible pour ce type."));
            });
            return;
        }

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName(currentType);

        for (int i = 0; i < data.size(); i++) {
            series.getData().add(new XYChart.Data<>("Point " + (i + 1), data.get(i)));
        }

        Platform.runLater(() -> {
            lineChart.getData().clear();
            lineChart.getData().add(series);
            listViewDonnees.setItems(FXCollections.observableArrayList(data.stream().map(Object::toString).toList()));
        });
    }

    private void setupFileWatcher() {
        fileWatcherThread = new Thread(() -> {
            try {
                Path filePath = Paths.get(dataFilePath).toAbsolutePath();
                Path directory = filePath.getParent();
                WatchService watchService = FileSystems.getDefault().newWatchService();
                directory.register(watchService, StandardWatchEventKinds.ENTRY_MODIFY);

                while (!Thread.currentThread().isInterrupted()) {
                    WatchKey key = watchService.take();
                    for (WatchEvent<?> event : key.pollEvents()) {
                        if (event.kind() == StandardWatchEventKinds.ENTRY_MODIFY &&
                            event.context().toString().equals(filePath.getFileName().toString())) {
                            refreshGraph();
                        }
                    }
                    key.reset();
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        });
        fileWatcherThread.setDaemon(true);
        fileWatcherThread.start();
    }

    public void stopFileWatcher() {
        if (fileWatcherThread != null) {
            fileWatcherThread.interrupt();
        }
    }

    /**
     * Gérer le clic sur le bouton "Retour" pour revenir à l'écran `ChoixSalle.fxml`.
     */
    @FXML
    private void handleRetour(ActionEvent event) {
        stopFileWatcher(); // Arrêter la surveillance du fichier
        try {
            FXMLLoader loader = new FXMLLoader(getClass().getResource("choixSalle.fxml"));
            Scene choixSalleScene = new Scene(loader.load());

            ChoixSalleController choixSalleController = loader.getController();
            choixSalleController.setStage(stage);

            stage.setScene(choixSalleScene);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
