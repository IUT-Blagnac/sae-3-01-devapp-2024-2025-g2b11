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
import javafx.scene.layout.StackPane;
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
    private MenuButton chartTypeMenuButton;

    @FXML
    private StackPane gaugePane;

    @FXML
    private Label nomSalle;

    @FXML
    private MenuButton allOrSpecificMenuButton; // Nouveau menu ajouté

    private static final List<String> ROOM_DATA_TYPES = List.of(
            "Température", "Humidité", "CO2", "TVOC", "Activity",
            "Illumination", "Infrared", "Infrared_and_visible", "Pressure");

    private Stage stage;
    private ProjetIOT app;

    private boolean allRoomsChartDisplayed = false; // Indicateur pour suivre l'affichage du graphique
    private boolean animation = true;
    private String selectedType = "Salle";
    private String selectedSpecific = null;
    private String selectedData = "température";
    private String selectedChartType = "Courbes";
    private String allOrSpecificChoice = "Toutes les salles"; // Par défaut : Toutes les salles

    private Thread graphRefreshThread;

    /**
     * Initialise le contexte du contrôleur.
     *
     * @param stage La fenêtre associée à cette vue.
     * @param app   L'application principale.
     */

    public void initContext(Stage stage, ProjetIOT app) {
        this.stage = stage;
        this.app = app;
    }

    public void setProjetApp(ProjetIOT app) {
        this.app = app;
    }

    @FXML
    private void initialize() {
        initializeTypeSelectionMenu();
        initializeAllOrSpecificMenu();
        configureRoomDataSelectionMenu();
        updateSpecificSelectionMenu();
        initializeChartTypeMenu();

        updateButtonStates(); // Initialiser les états des boutons
        startGraphRefreshThread();
    }

    private void initializeAllOrSpecificMenu() {
        allOrSpecificMenuButton.getItems().clear();

        List<String> choices = List.of("Toutes les salles", "Choisir une salle");
        for (String choice : choices) {
            MenuItem item = new MenuItem(choice);
            item.setOnAction(event -> handleAllOrSpecificChoice(choice));
            allOrSpecificMenuButton.getItems().add(item);
        }

        // Par défaut : Toutes les salles
        allOrSpecificMenuButton.setText("Toutes les salles");
        handleAllOrSpecificChoice("Toutes les salles");
    }

    private void handleAllOrSpecificChoice(String choice) {
        allOrSpecificChoice = choice;
        allOrSpecificMenuButton.setText(choice);

        // Réinitialiser le flag allRoomsChartDisplayed si "Choisir une salle" est
        // sélectionné
        if ("Choisir une salle".equals(choice)) {
            allRoomsChartDisplayed = false; // Réinitialisation ici
        }

        // Si "Toutes les salles" est sélectionné, forcer le mode "Lignes" et désactiver
        // le bouton
        if ("Toutes les salles".equals(choice)) {
            selectedChartType = "Lignes";
            chartTypeMenuButton.setText("Graphique: Lignes");
            chartTypeMenuButton.setDisable(true);
        } else {
            chartTypeMenuButton.setDisable(false); // Activer le bouton si "Choisir une salle" est sélectionné
        }

        updateButtonStates(); // Mettre à jour les états des boutons
        refreshChart(); // Mettre à jour le graphique
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
            // Forcer "Choisir une salle" pour Panneaux Solaires
            handleAllOrSpecificChoice("Choisir une salle");
        } else {
            configureRoomDataSelectionMenu();
            // Remettre à "Toutes les salles" pour les Salles
            handleAllOrSpecificChoice("Toutes les salles");
        }

        selectionTypeMenuButton.setText("Type: " + type);
        updateSpecificSelectionMenu(); // Met à jour les options spécifiques
        updateButtonStates(); // Mettre à jour les états des boutons
    }

    private void updateButtonStates() {
        // Désactiver le menu "Toutes les salles / Choisir une salle" pour "Panneaux
        // Solaires"
        allOrSpecificMenuButton.setDisable(!"Salle".equals(selectedType));

        // Activer/désactiver le menu des salles spécifiques
        specificSelectionMenuButton
                .setDisable(!"Choisir une salle".equals(allOrSpecificChoice) || !"Salle".equals(selectedType));

        // Activer/désactiver les autres menus selon le type sélectionné
        dataSelectionMenuButton.setDisable(!"Salle".equals(selectedType) && !"Panneaux Solaires".equals(selectedType));

        // Désactiver le bouton du type de graphique si "Toutes les salles" est
        // sélectionné
        chartTypeMenuButton.setDisable("Toutes les salles".equals(allOrSpecificChoice));
    }

    private void initializeChartTypeMenu() {
        chartTypeMenuButton.getItems().clear();

        List<String> chartTypes = List.of("Lignes", "Dernière valeur");
        for (String chartType : chartTypes) {
            MenuItem item = new MenuItem(chartType);
            item.setOnAction(event -> {
                selectedChartType = chartType;
                chartTypeMenuButton.setText("Graphique: " + chartType);
                refreshChart();
            });
            chartTypeMenuButton.getItems().add(item);
        }

        chartTypeMenuButton.setText("Graphique: Lignes");
    }

    /**
     * Configure le MenuButton des données pour les salles.
     */
    private void configureRoomDataSelectionMenu() {
        dataSelectionMenuButton.getItems().clear();
    
        for (String dataType : ROOM_DATA_TYPES) {
            MenuItem item = new MenuItem(dataType);
            item.setOnAction(event -> handleDataSelection(dataType));
            dataSelectionMenuButton.getItems().add(item);
        }
    
        // Sélection par défaut
        dataSelectionMenuButton.setText("Données: Température");
        selectedData = "température";
    
        updateButtonStates();
    }
    
    

    private void configureSolarDataSelectionMenu() {
        dataSelectionMenuButton.getItems().clear();

        List<String> solarDataTypes = List.of("Current Power", "Lifetime Energy");
        for (String dataType : solarDataTypes) {
            MenuItem item = new MenuItem(dataType);
            item.setOnAction(event -> handleDataSelection(dataType));
            dataSelectionMenuButton.getItems().add(item);
        }

        // Sélection par défaut
        dataSelectionMenuButton.setText("Données: Current Power");
        selectedData = "current power";

        updateButtonStates(); // Mettre à jour les états des boutons
    }

    /**
     * Gère la sélection des données (Température, CO2, etc.).
     */
    private void handleDataSelection(String dataType) {
        if(allOrSpecificChoice.equals("Choisir une salle")){
            lineChart.setAnimated(true);
        }
       
        selectedData = dataType.toLowerCase();
        dataSelectionMenuButton.setText("Données: " + dataType);



        allRoomsChartDisplayed = false;

        refreshChart();


    }


    private void updateSpecificSelectionMenu() {
        specificSelectionMenuButton.getItems().clear();

        List<String> options = "Salle".equalsIgnoreCase(selectedType)
                ? Dashboard.getRoomNames()
                : List.of("Panneaux Solaires");

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
            switch (selectedData.toLowerCase()) {
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
                case "tvoc":
                    yAxis.setLabel("TVOC (ppb)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(0);
                    yAxis.setUpperBound(300);
                    yAxis.setTickUnit(50);
                    break;
                case "illumination":
                    yAxis.setLabel("Illumination (lux)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(0);
                    yAxis.setUpperBound(10);
                    yAxis.setTickUnit(1);
                    break;
                case "infrared":
                case "infrared and visible":
                case "activity":
                    yAxis.setLabel(selectedData + " (Présence/Absence)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(0);
                    yAxis.setUpperBound(2);
                    yAxis.setTickUnit(1);
                    break;
                case "pressure":
                    yAxis.setLabel("Pression (hPa)");
                    yAxis.setAutoRanging(false);
                    yAxis.setLowerBound(950);
                    yAxis.setUpperBound(1050);
                    yAxis.setTickUnit(10);
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
        configureAxes();
    
        // Activer l'animation uniquement si "Choisir une salle" est sélectionné

    
        if ("Dernière valeur".equals(selectedChartType)) {
            showLastValue();
        } else {
            if ("Toutes les salles".equals(allOrSpecificChoice)) {
                showAllRoomsData();
            } else {
                showLineChart();
            }
        }
    
        lineChart.layout(); // Forcer le redessin du graphique
    }
    

    private void showAllRoomsData() {
        lineChart.setAnimated(false);
        // Si le graphique pour toutes les salles est déjà affiché, on arrête la méthode
        if (allRoomsChartDisplayed) {
            return;
        }

        lineChart.setVisible(true);
        gaugePane.setVisible(false);

        if (selectedData == null) {
            lineChart.getData().clear();
            lineChart.setTitle("Aucune donnée disponible");
            return;
        }

        lineChart.getData().clear();
        List<String> rooms = Dashboard.getRoomNames();

        for (String room : rooms) {
            List<Float> data = Dashboard.getData("Salle", room, selectedData);


            if (data == null || data.isEmpty()) {
                continue;
            }

            XYChart.Series<String, Number> series = new XYChart.Series<>();
            series.setName(room + " - " + selectedData);

            for (int i = 0; i < data.size(); i++) {
                series.getData().add(new XYChart.Data<>("Point " + (i + 1), data.get(i)));
            }

            lineChart.getData().add(series);
        }

        nomSalle.setText("Graphique : Toutes les salles");
        allRoomsChartDisplayed = true; // On définit le flag à vrai après le rendu du graphique
    }

    private void showLineChart() {
        lineChart.setVisible(true);
        gaugePane.setVisible(false);
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
            String xValue = "Point " + (i + 1);
            if (selectedData.equalsIgnoreCase("infrared") || 
                selectedData.equalsIgnoreCase("infrared and visible") || 
                selectedData.equalsIgnoreCase("activity")) {
                // Booléen : Oui/Non
                String yValue = booleanToText(data.get(i));
                series.getData().add(new XYChart.Data<>(xValue, yValue.equals("Oui") ? 1 : 0));
            } else {
                // Valeur numérique
                series.getData().add(new XYChart.Data<>(xValue, data.get(i)));
            }
        }
        

        lineChart.getData().clear();
        lineChart.getData().add(series);
        nomSalle.setText("Graphique : " + selectedSpecific);
        lineChart.setAnimated(false);
    }

    private void showLastValue() {
        lineChart.setVisible(false);
        gaugePane.setVisible(true);
    
        if (selectedSpecific == null || selectedData == null) {
            gaugePane.getChildren().clear();
            Label noDataLabel = new Label("Aucune donnée disponible");
            noDataLabel.setStyle("-fx-font-size: 24px; -fx-text-fill: #ffffff;");
            gaugePane.getChildren().add(noDataLabel);
            return;
        }
    
        List<Float> data = Dashboard.getData(selectedType, selectedSpecific, selectedData);
    
        if (data == null || data.isEmpty()) {
            gaugePane.getChildren().clear();
            Label noDataLabel = new Label("Aucune donnée disponible pour " + selectedSpecific);
            noDataLabel.setStyle("-fx-font-size: 24px; -fx-text-fill: #ffffff;");
            gaugePane.getChildren().add(noDataLabel);
            return;
        }
    
        Float lastValue = data.get(data.size() - 1);
        String displayValue;
    
        if (selectedData.equalsIgnoreCase("infrared") || 
            selectedData.equalsIgnoreCase("infrared and visible") || 
            selectedData.equalsIgnoreCase("activity")) {
            // Booléen : afficher Oui/Non
            displayValue = booleanToText(lastValue);
        } else {
            // Valeurs numériques avec unité
            String unit = getUnitForDataType(selectedData);
            displayValue = String.format("%.2f %s", lastValue, unit);
        }
    
        gaugePane.getChildren().clear();
        Label gaugeText = new Label(displayValue);
        gaugeText.setStyle("-fx-font-size: 48px; -fx-text-fill: #ffffff;");
        gaugePane.getChildren().add(gaugeText);
        nomSalle.setText("Dernière valeur : " + selectedSpecific);
    }   

    private String booleanToText(float value) {
        return value == 1 ? "Oui" : "Non";
    }
    

    private String getUnitForDataType(String dataType) {
        switch (dataType.toLowerCase()) {
            case "température":
                return "°C";
            case "humidité":
                return "%";
            case "co2":
                return "ppm";
            case "tvoc":
                return "ppb";
            case "illumination":
                return "lux";
            case "infrared":
            case "infrared and visible":
            case "activity":
                return ""; 
            case "pressure":
                return "hPa";
            default:
                return "";
        }
    }
    
    

    public void stopGraphRefreshThread() {
        if (graphRefreshThread != null) {
            graphRefreshThread.interrupt();
        }
    }
}
