package Data;

import java.util.Map;

import com.fasterxml.jackson.annotation.JsonProperty;

public class PanneauSolaire {

    @JsonProperty("lastUpdateTime")
    public String lastUpdateTime;

    @JsonProperty("lifeTimeData")
    public Map<String, Integer> lifeTimeData;  // Pour gérer les données imbriquées

    @JsonProperty("lastYearData")
    public Map<String, Integer> lastYearData;  // Pour gérer les données imbriquées

    @JsonProperty("lastMonthData")
    public Map<String, Integer> lastMonthData;  // Pour gérer les données imbriquées

    @JsonProperty("lastDayData")
    public Map<String, Integer> lastDayData;  // Pour gérer les données imbriquées

    @JsonProperty("currentPower")
    public Map<String, Float> currentPower;  // Pour gérer les données imbriquées

    @JsonProperty("measuredBy")
    public String measuredBy;

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
                '}';
    }
}