package projet.application.view;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Label;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.stage.Stage;
import projet.application.control.Dashboard;
import projet.application.ProjetIOT;

import java.util.List;

public class DashboardViewController {

    @FXML
    private LineChart<String, Number> lineChart;

    @FXML
    private CategoryAxis xAxis;

    @FXML
    private NumberAxis yAxis;

    @FXML
    private MenuButton selectionTypeMenuButton;

    @FXML
    private MenuButton specificSelectionMenuButton;

    @FXML
    private MenuButton dataSelectionMenuButton;

    @FXML
    private Label nomSalle;

    private Stage dialogStage;
    private ProjetIOT app;

    private boolean animation = true;
    private String selectedType = "Salle";
    private String selectedSpecific = null;
    private String selectedData = "température";

    private Thread graphRefreshThread;

    public void setDialogStage(Stage dialogStage) {
        this.dialogStage = dialogStage;
    }

    public void setProjetApp(ProjetIOT app) {
        this.app = app;
    }

    @FXML
    private void initialize() {

        initializeTypeSelectionMenu();
        initializeDataSelectionMenu();
        updateSpecificSelectionMenu();

        startGraphRefreshThread();
    }

    private void initializeTypeSelectionMenu() {
        selectionTypeMenuButton.getItems().clear();

        List<String> types = List.of("Salle", "Panneaux Solaires");
        for (String type : types) {
            MenuItem item = new MenuItem(type);
            item.setOnAction(event -> handleTypeSelection(type));
            selectionTypeMenuButton.getItems().add(item);
        }

        // Sélection par défaut
        selectionTypeMenuButton.setText("Type: Salle");
        handleTypeSelection("Salle");
    }

    /**
     * Gère la sélection du type (Salle ou Panneaux Solaires).
     */
    private void handleTypeSelection(String type) {
        animation = true;
        selectedType = type;

        if ("Panneaux Solaires".equals(type)) {
            configureSolarDataSelectionMenu();
        } else {
            configureRoomDataSelectionMenu();
        }

        selectionTypeMenuButton.setText("Type: " + type);
        updateSpecificSelectionMenu(); // Met à jour les options spécifiques (salles ou panneaux)
    }

    /**
     * Configure le MenuButton des données pour les salles.
     */
    private void configureRoomDataSelectionMenu() {
        dataSelectionMenuButton.getItems().clear();

        List<String> roomDataTypes = List.of("Température", "Humidité", "CO2");
        for (String dataType : roomDataTypes) {
            MenuItem item = new MenuItem(dataType);
            item.setOnAction(event -> {
                handleDataSelection(dataType);
                animation = true;
            });
            dataSelectionMenuButton.getItems().add(item);
        }

        // Sélection par défaut
        dataSelectionMenuButton.setText("Données: Température");
        selectedData = "température";
    }

    /**
     * Configure le MenuButton des données pour les panneaux solaires.
     */
    private void configureSolarDataSelectionMenu() {
        dataSelectionMenuButton.getItems().clear();

        List<String> solarDataTypes = List.of("Current Power", "Lifetime Energy");
        for (String dataType : solarDataTypes) {
            MenuItem item = new MenuItem(dataType);
            item.setOnAction(event -> {
                handleDataSelection(dataType);
                animation = true;
            });
            dataSelectionMenuButton.getItems().add(item);
        }

        // Sélection par défaut
        dataSelectionMenuButton.setText("Données: Current Power");
        selectedData = "current power";
    }

    /**
     * Gère la sélection des données (Température, CO2, etc.).
     */
    private void handleDataSelection(String dataType) {
        selectedData = dataType.toLowerCase();
        dataSelectionMenuButton.setText("Données: " + dataType);
        configureAxes(); // Configure les axes en fonction des données sélectionnées
    }

    private void initializeDataSelectionMenu() {
        dataSelectionMenuButton.getItems().clear();

        List<String> dataTypes = List.of("Température", "Humidité", "CO2");
        for (String dataType : dataTypes) {
            MenuItem item = new MenuItem(dataType);
            item.setOnAction(event -> {
                animation = true;
                selectedData = dataType.toLowerCase();
                dataSelectionMenuButton.setText("Données: " + dataType);
                configureAxes();
            });
            dataSelectionMenuButton.getItems().add(item);
        }

        dataSelectionMenuButton.setText("Données: Température");
        configureAxes();
    }

    private void updateSpecificSelectionMenu() {
        specificSelectionMenuButton.getItems().clear();

        List<String> options = "Salle".equalsIgnoreCase(selectedType)
                ? Dashboard.getRoomNames()
                : Dashboard.getSolarPanelData();

        for (String option : options) {
            MenuItem item = new MenuItem(option);
            item.setOnAction(event -> {
                animation = true;
                selectedSpecific = option;
                specificSelectionMenuButton.setText("Sélection: " + option);
            });
            specificSelectionMenuButton.getItems().add(item);
        }

        if (!options.isEmpty()) {
            selectedSpecific = options.get(0);
            specificSelectionMenuButton.setText("Sélection: " + selectedSpecific);
        } else {
            selectedSpecific = null;
            specificSelectionMenuButton.setText("Sélection: Aucune");
        }
    }

    private void configureAxes() {
        xAxis.setLabel("Points de mesure");

        if ("Salle".equalsIgnoreCase(selectedType)) {
            switch (selectedData) {
                case "température":
                    yAxis.setLabel("Température (°C)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(0);
                    yAxis.setUpperBound(40);
                    yAxis.setTickUnit(5);
                    break;
                case "humidité":
                    yAxis.setLabel("Humidité (%)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(0);
                    yAxis.setUpperBound(100);
                    yAxis.setTickUnit(10);
                    break;
                case "co2":
                    yAxis.setLabel("CO2 (ppm)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(0);
                    yAxis.setUpperBound(2000);
                    yAxis.setTickUnit(250);
                    break;
                default:
                    yAxis.setLabel("Valeurs");
                    yAxis.setAutoRanging(true);
                    break;
            }
        } else if ("Panneaux Solaires".equalsIgnoreCase(selectedType)) {
            switch (selectedData) {
                case "current power":
                    yAxis.setLabel("Puissance Actuelle (W)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(0);
                    yAxis.setUpperBound(3000);
                    yAxis.setTickUnit(500);
                    break;
                case "lifetime energy":
                    yAxis.setLabel("Énergie Totale (Wh)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(3400000);
                    yAxis.setUpperBound(3500000);
                    yAxis.setTickUnit(10000);
                    break;
                default:
                    yAxis.setLabel("Valeurs");
                    yAxis.setAutoRanging(true);
                    break;
            }
        }
    }

    private void startGraphRefreshThread() {
        graphRefreshThread = new Thread(() -> {
            while (!Thread.currentThread().isInterrupted()) {
                try {
                    // Délai entre les rafraîchissements
                    Thread.sleep(500);
                    Platform.runLater(this::refreshChart);

                } catch (InterruptedException e) {
                    Thread.currentThread().interrupt();
                }
            }
        });
        graphRefreshThread.setDaemon(true);
        graphRefreshThread.start();
    }

    private void refreshChart() {
        lineChart.setAnimated(animation);

        if (selectedSpecific == null || selectedData == null) {
            lineChart.getData().clear();
            lineChart.setTitle("Aucune donnée disponible");
            return;
        }

        List<Float> data = Dashboard.getData(selectedType, selectedSpecific, selectedData);

        if (data == null || data.isEmpty()) {
            lineChart.getData().clear();
            lineChart.setTitle("Aucune donnée disponible pour " + selectedSpecific);
            return;
        }

        XYChart.Series<String, Number> series = new XYChart.Series<>();
        series.setName(selectedSpecific + " - " + selectedData);

        for (int i = 0; i < data.size(); i++) {
            series.getData().add(new XYChart.Data<>("Point " + (i + 1), data.get(i)));
        }

        lineChart.getData().clear();
        lineChart.getData().add(series);
        nomSalle.setText("Graphique : " + selectedSpecific);
        animation = false;
    }

    public void stopGraphRefreshThread() {
        if (graphRefreshThread != null) {
            graphRefreshThread.interrupt();
        }
    }
}
