package projet.application.Acces;

import javafx.application.Platform;
import javafx.collections.FXCollections;
import javafx.collections.ObservableList;
import projet.application.Data.Capteur;
import projet.application.Data.GestionAlert;

import com.fasterxml.jackson.databind.ObjectMapper;

import java.io.File;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

public class AlertManager implements Runnable {

    private final ObservableList<String> alerts; // Liste observable pour les ListView
    private final LocalDateTime appLaunchTime;
    private final Set<String> displayedAlerts; // Suivi des alertes affichées

    public AlertManager(LocalDateTime appLaunchTime) {
        this.alerts = FXCollections.observableArrayList();
        this.appLaunchTime = appLaunchTime;
        this.displayedAlerts = new HashSet<>();
    }

    public ObservableList<String> getAlerts() {
        return alerts;
    }

    @Override
    public void run() {
        while (true) {
            try {
                List<String> newAlerts = fetchAlertsSinceLaunch();

                Platform.runLater(() -> {
                    for (String alert : newAlerts) {
                        if (displayedAlerts.add(alert)) { // Ajoute seulement si non présent
                            alerts.add(alert);
                        }
                    }
                });

                Thread.sleep(5000); // Rafraîchissement toutes les 5 secondes
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    private List<String> fetchAlertsSinceLaunch() {
        List<String> newAlerts = new java.util.ArrayList<>();

        try {
            ObjectMapper objectMapper = new ObjectMapper();
            GestionAlert alertData = objectMapper.readValue(new File("alertJSON.json"), GestionAlert.class);

            Map<String, Map<String, List<Capteur>>> alertMap = alertData.getCapteurs();
            DateTimeFormatter dateFormatter = DateTimeFormatter.ofPattern("dd-MM-yyyy HH:mm:ss");

            for (Map.Entry<String, Map<String, List<Capteur>>> entry : alertMap.entrySet()) {
                String alertType = entry.getKey();
                Map<String, List<Capteur>> roomAlerts = entry.getValue();

                for (Map.Entry<String, List<Capteur>> roomEntry : roomAlerts.entrySet()) {
                    String room = roomEntry.getKey();
                    List<Capteur> capteurs = roomEntry.getValue();

                    for (Capteur capteur : capteurs) {
                        LocalDateTime alertTime = LocalDateTime.parse(
                                capteur.jour + " " + capteur.heure, dateFormatter);

                        if (alertTime.isAfter(appLaunchTime)) {
                            String alertMessage = String.format(
                                    "%s Alert en %s: Temperature %.2f°C, Humidity %.2f%%, CO2 %d ppm à %s %s",
                                    alertType, room, capteur.temperature, capteur.humidity, capteur.co2, capteur.jour,
                                    capteur.heure);
                            newAlerts.add(alertMessage);
                        }
                    }
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }

        return newAlerts;
    }
}