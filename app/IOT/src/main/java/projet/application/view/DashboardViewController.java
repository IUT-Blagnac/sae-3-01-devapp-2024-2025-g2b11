package projet.application.view;

import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.chart.CategoryAxis;
import javafx.scene.chart.LineChart;
import javafx.scene.chart.NumberAxis;
import javafx.scene.chart.XYChart;
import javafx.scene.control.Label;
import javafx.scene.control.ListView;
import javafx.scene.control.MenuButton;
import javafx.scene.control.MenuItem;
import javafx.scene.layout.StackPane;
import javafx.stage.Stage;
import projet.application.control.Dashboard;
import projet.application.ProjetIOT;
import projet.application.Acces.AlertManager;
import projet.application.Acces.GraphRefreshTask;

import java.util.List;
import java.util.Map;

/**
 * Contrôleur pour la vue du tableau de bord (Dashboard).
 * Permet l'affichage et la manipulation des données des capteurs
 * et des panneaux solaires sous forme graphique.
 */
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
    private MenuButton allOrSpecificMenuButton;

    @FXML
    private ListView<String> alertListView;

    private static final List<String> ROOM_DATA_TYPES = List.of(
            "Température", "Humidité", "CO2", "TVOC", "Activity",
            "Illumination", "Infrared", "Infrared_and_visible", "Pressure");

    private Stage stage;
    private ProjetIOT app;

    private Map<String, List<Float>> previousAllRoomsData = null;

    private boolean allRoomsChartDisplayed = false;
    private boolean animation = true;
    private String selectedType = "Salle";
    private String selectedSpecific = null;
    private String selectedData = "température";
    private String selectedChartType = "Courbes";
    private String allOrSpecificChoice = "Toutes les salles";

    private Thread graphRefreshThread;
    private GraphRefreshTask graphRefreshTask;

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

    /**
     * Définit l'application principale pour le contrôleur.
     *
     * @param app L'instance de l'application ProjetIOT.
     */
    public void setProjetApp(ProjetIOT app) {
        this.app = app;
    }

    /**
     * Méthode appelée automatiquement lors de l'initialisation de la vue.
     * Configure les menus, initialise les états des boutons et lance le
     * rafraîchissement des graphiques.
     */
    @FXML
    private void initialize() {
        initializeTypeSelectionMenu();
        initializeAllOrSpecificMenu();
        configureRoomDataSelectionMenu();
        updateSpecificSelectionMenu();
        initializeChartTypeMenu();
        updateButtonStates();
        startGraphRefreshThread();

    }

    public void setAlertManager(AlertManager alertManager) {
        alertListView.setItems(alertManager.getAlerts());
    }

    /**
     * Initialise le menu pour choisir entre "Toutes les salles" ou une salle
     * spécifique.
     */
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

    /**
     * Gère la sélection entre "Toutes les salles" et une salle spécifique.
     *
     * @param choice La sélection de l'utilisateur.
     */
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
            previousAllRoomsData = null;
        } else {
            chartTypeMenuButton.setDisable(false); // Activer le bouton si "Choisir une salle" est sélectionné
        }

        updateButtonStates(); // Mettre à jour les états des boutons
        refreshChart(); // Mettre à jour le graphique
    }

    /**
     * Initialise le menu pour sélectionner le type (Salle ou Panneaux Solaires).
     */
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
     * Gère la sélection entre Salle et Panneaux Solaires.
     *
     * @param type Le type sélectionné.
     */
    private void handleTypeSelection(String type) {
        animation = true;

        // Si le type change, réinitialiser les données précédentes
        if (!type.equals(selectedType)) {
            previousAllRoomsData = null; // Forcer le rafraîchissement des graphiques
        }

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

    /**
     * Met à jour l'état des différents boutons du tableau de bord en fonction des
     * sélections actuelles.
     * Cette méthode ajuste l'activation ou la désactivation des menus selon le type
     * sélectionné (Salle ou Panneaux Solaires) et les choix utilisateur (Toutes les
     * salles ou Choisir une salle).
     */
    private void updateButtonStates() {

        allOrSpecificMenuButton.setDisable(!"Salle".equals(selectedType));

        specificSelectionMenuButton
                .setDisable(!"Choisir une salle".equals(allOrSpecificChoice) || !"Salle".equals(selectedType));

        dataSelectionMenuButton.setDisable(!"Salle".equals(selectedType) && !"Panneaux Solaires".equals(selectedType));

        chartTypeMenuButton.setDisable("Toutes les salles".equals(allOrSpecificChoice));
    }

    /**
     * Initialise le menu des types de graphiques disponibles.
     * Ce menu permet de choisir entre l'affichage des graphiques en "Lignes" ou la
     * "Dernière valeur".
     * Les choix mettent à jour le type de graphique sélectionné et rafraîchissent
     * l'affichage.
     */
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

    /**
     * Configure le menu de sélection des données pour les panneaux solaires.
     * Ce menu permet à l'utilisateur de choisir le type de données à afficher,
     * telles que "Puissance actuelle" ou "Énergie totale".
     */
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
     * Gère la sélection du type de données à afficher.
     * Cette méthode est appelée lorsqu'une option de données est choisie dans le
     * menu,
     * telle que "Température", "CO2", ou tout autre type.
     * 
     * @param dataType Le type de données sélectionné par l'utilisateur.
     */
    private void handleDataSelection(String dataType) {

        if (allOrSpecificChoice.equals("Choisir une salle")) {
            lineChart.setAnimated(true);
        }

        selectedData = dataType.toLowerCase();
        dataSelectionMenuButton.setText("Données: " + dataType);

        allRoomsChartDisplayed = false;

        refreshChart();
    }

    /**
     * Met à jour le menu des sélections spécifiques en fonction du type sélectionné
     * (Salle ou Panneaux Solaires).
     * Cette méthode recharge les options disponibles dans le menu déroulant pour
     * permettre à l'utilisateur
     * de choisir une salle spécifique ou une autre catégorie pertinente.
     */
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

    /**
     * Configure les axes X et Y du graphique en fonction du type sélectionné (Salle
     * ou Panneaux Solaires)
     * et des données à afficher.
     * Les étiquettes, plages et unités des axes sont adaptées dynamiquement selon
     * la sélection.
     */
    private void configureAxes() {
        xAxis.setLabel("Points de mesure");
        xAxis.lookup(".axis-label").setStyle("-fx-text-fill: white;");
        yAxis.lookup(".axis-label").setStyle("-fx-text-fill: white;");
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
                    yAxis.setUpperBound(3000);
                    yAxis.setTickUnit(375);
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

    /**
     * Démarre un thread en arrière-plan pour actualiser périodiquement le
     * graphique.
     */
    private void startGraphRefreshThread() {
        graphRefreshTask = new GraphRefreshTask(this::refreshChart);
        graphRefreshThread = new Thread(graphRefreshTask);
        graphRefreshThread.setDaemon(true);
        graphRefreshThread.start();
    }

    /**
     * Actualise le graphique en fonction des données sélectionnées et du type de
     * visualisation choisi.
     */
    private void refreshChart() {
        configureAxes();

        if ("Dernière valeur".equals(selectedChartType)) {
            showLastValue();
        } else {
            if ("Toutes les salles".equals(allOrSpecificChoice)) {
                showAllRoomsData();
            } else {
                showLineChart();
            }
        }

        lineChart.layout();
    }

    /**
     * Affiche les données de toutes les salles sous forme de courbes.
     * Cette méthode récupère les données de chaque salle pour le type de donnée
     * sélectionné,
     * les organise en séries, et les affiche dans un graphique.
     * Si les données sont déjà affichées, la méthode retourne immédiatement.
     */
    private void showAllRoomsData() {
        lineChart.setAnimated(false);

        lineChart.setVisible(true);
        gaugePane.setVisible(false);

        if (selectedData == null) {
            lineChart.getData().clear();
            lineChart.setTitle("Aucune donnée disponible");
            return;
        }

        // Récupérer les données en fonction du type sélectionné
        List<String> rooms = "Salle".equalsIgnoreCase(selectedType)
                ? Dashboard.getRoomNames()
                : List.of("Panneaux Solaires");

        // Préparer une map pour stocker les données actuelles
        Map<String, List<Float>> currentAllRoomsData = new java.util.HashMap<>();

        for (String room : rooms) {
            List<Float> data = Dashboard.getData(selectedType, room, selectedData);
            if (data != null && !data.isEmpty()) {
                currentAllRoomsData.put(room, data);
            }
        }

        // Comparer currentAllRoomsData et previousAllRoomsData
        if (previousAllRoomsData != null && previousAllRoomsData.equals(currentAllRoomsData)) {
            // Les données sont identiques à la dernière fois, on ne rafraîchit pas
            return;
        }

        // Rafraîchir le graphique
        lineChart.getData().clear();
        for (Map.Entry<String, List<Float>> entry : currentAllRoomsData.entrySet()) {
            String room = entry.getKey();
            List<Float> data = entry.getValue();

            XYChart.Series<String, Number> series = new XYChart.Series<>();
            series.setName(room + " - " + selectedData);

            for (int i = 0; i < data.size(); i++) {
                float value = data.get(i);

                // Filtrer les valeurs égales à 0 pour température, humidité et CO2
                if ((selectedData.equalsIgnoreCase("température") ||
                        selectedData.equalsIgnoreCase("humidité") ||
                        selectedData.equalsIgnoreCase("co2")) && value == 0) {
                    continue; // Ignorer cette valeur
                }

                series.getData().add(new XYChart.Data<>("Point " + (i + 1), value));
            }

            lineChart.getData().add(series);
        }

        nomSalle.setText("Graphique : Toutes les salles");
        allRoomsChartDisplayed = true;

        // Mettre à jour les données précédentes
        previousAllRoomsData = currentAllRoomsData;
    }

    /**
     * Affiche un graphique en ligne pour une salle spécifique et un type de données
     * sélectionné.
     * Cette méthode récupère les données associées à une salle et les organise sous
     * forme de série
     * pour un affichage sous forme de courbe dans le graphique.
     * Si aucune donnée n'est disponible, un message approprié est affiché dans le
     * graphique.
     */
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
            float value = data.get(i);

            // Filtrer les points pour la température, CO2 et humidité
            if ((selectedData.equalsIgnoreCase("température")
                    || selectedData.equalsIgnoreCase("co2")
                    || selectedData.equalsIgnoreCase("humidité"))
                    && value == 0) {
                continue; // Ignorer les points avec des valeurs égales à 0
            }

            if (selectedData.equalsIgnoreCase("infrared") ||
                    selectedData.equalsIgnoreCase("infrared and visible") ||
                    selectedData.equalsIgnoreCase("activity")) {
                // Booléen : Oui/Non
                String yValue = booleanToText(value);
                series.getData().add(new XYChart.Data<>(xValue, yValue.equals("Oui") ? 1 : 0));
            } else {
                // Valeur numérique
                series.getData().add(new XYChart.Data<>(xValue, value));
            }
        }

        lineChart.getData().clear();
        lineChart.getData().add(series);
        nomSalle.setText("Graphique : " + selectedSpecific);
        lineChart.setAnimated(false);
    }

    /**
     * Affiche la dernière valeur mesurée pour une salle ou un panneau solaire
     * spécifique.
     * Cette méthode présente les données sous forme d'une jauge ou d'un texte en
     * grand format.
     * Si aucune donnée n'est disponible, un message d'erreur est affiché.
     */
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

    /**
     * Convertit une valeur numérique booléenne en texte.
     *
     * @param value la valeur numérique (1 pour "Oui", autre pour "Non").
     * @return "Oui" si la valeur est 1, sinon "Non".
     */
    private String booleanToText(float value) {
        return value == 1 ? "Oui" : "Non";
    }

    /**
     * Récupère l'unité correspondant à un type de donnée spécifique.
     *
     * @param dataType le type de donnée pour lequel récupérer l'unité (par exemple,
     *                 "température", "humidité").
     * @return l'unité correspondant au type de donnée, ou une chaîne vide si aucune
     *         unité n'est applicable.
     */
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
            case "current power": // Panneaux Solaires
                return "W";
            case "lifetime energy": // Panneaux Solaires
                return "Wh";
            default:
                return "";
        }
    }

    public void stopGraphRefreshThread() {
        if (graphRefreshTask != null) {
            graphRefreshTask.stop();
        }
        if (graphRefreshThread != null) {
            graphRefreshThread.interrupt();
        }
    }
}
