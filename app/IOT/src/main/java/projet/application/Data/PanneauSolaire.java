package projet.application.Data;


import java.util.Map;

import com.fasterxml.jackson.annotation.JsonProperty;

/**
 * Représente un panneau solaire et ses données associées.
 * Cette classe est utilisée pour modéliser les informations sur les panneaux solaires,
 * incluant les données historiques, la puissance actuelle et les métadonnées de mesure.
 */
public class PanneauSolaire {

    /**
     * Dernière mise à jour des données du panneau solaire.
     */
    @JsonProperty("lastUpdateTime")
    public String lastUpdateTime;

    /**
     * Données accumulées sur toute la durée de vie du panneau solaire.
     * Contient des clés décrivant les types de données (par exemple, énergie produite) et leurs valeurs associées.
     */
    @JsonProperty("lifeTimeData")
    public Map<String, Integer> lifeTimeData;

    /**
     * Données cumulées pour l'année précédente.
     * Contient des clés décrivant les types de données et leurs valeurs associées.
     */
    @JsonProperty("lastYearData")
    public Map<String, Integer> lastYearData;

    /**
     * Données cumulées pour le mois précédent.
     * Contient des clés décrivant les types de données et leurs valeurs associées.
     */
    @JsonProperty("lastMonthData")
    public Map<String, Integer> lastMonthData;

    /**
     * Données cumulées pour le jour précédent.
     * Contient des clés décrivant les types de données et leurs valeurs associées.
     */
    @JsonProperty("lastDayData")
    public Map<String, Integer> lastDayData;

    /**
     * Puissance actuelle mesurée par le panneau solaire.
     * Contient des clés décrivant les types de mesures et leurs valeurs en watts.
     */
    @JsonProperty("currentPower")
    public Map<String, Float> currentPower;

    /**
     * Indique l'identité de l'élément ou de la méthode ayant effectué la mesure.
     */
    @JsonProperty("measuredBy")
    public String measuredBy;

    /**
     * Jour correspondant aux données mesurées.
     */
    public String jour;

    /**
     * Heure correspondant aux données mesurées.
     */
    public String heure;

    /**
     * Retourne une représentation sous forme de chaîne de caractères des informations du panneau solaire.
     *
     * @return une chaîne contenant les données et métadonnées du panneau solaire.
     */
    @Override
    public String toString() {
        return "PanneauSolaire{" +
                "lastUpdateTime='" + lastUpdateTime + '\'' +
                ", lifeTimeData=" + lifeTimeData +
                ", lastYearData=" + lastYearData +
                ", lastMonthData=" + lastMonthData +
                ", lastDayData=" + lastDayData +
                ", currentPower=" + currentPower +
                ", measuredBy='" + measuredBy + '\'' +
                ", jour='" + jour + '\'' +
                ", heure='" + heure + '\'' +
                '}';
    }
}