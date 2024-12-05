package projet.Data;


import java.util.Map;

import com.fasterxml.jackson.annotation.JsonProperty;

public class PanneauSolaire {

    @JsonProperty("lastUpdateTime")
    public String lastUpdateTime;

    @JsonProperty("lifeTimeData")
    public Map<String, Integer> lifeTimeData;  // Pour gérer les données imbriquées

    @JsonProperty("lastYearData")
    public Map<String, Integer> lastYearData;  

    @JsonProperty("lastMonthData")
    public Map<String, Integer> lastMonthData;  

    @JsonProperty("lastDayData")
    public Map<String, Integer> lastDayData;  

    @JsonProperty("currentPower")
    public Map<String, Float> currentPower;  

    @JsonProperty("measuredBy")
    public String measuredBy;

    public String jour;
    public String heure;

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
/**public class PanneauSolaire{

    public String lastUpdateTime;
    public int lifeTimeData;
    public int lastYearData;
    public int lastMonthData;
    public int lastDayData;
    public float currentPower;
    public String measuredBy;
*/
}