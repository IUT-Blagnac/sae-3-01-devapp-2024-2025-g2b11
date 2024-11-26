package Data;

public class Capteur{
    public float temperature;
    public float humidity;
    public int activity;
    public int co2;
    public int tvoc;
    public int illumination;
    public int infrared;
    public int infrared_and_visible;
    public float pressure;
    public String name;
    public String devEUI;
    public String room;
    public int floor;
    public String Building;

    @Override
    public String toString() {
    return "Données capteur{" +
            "temperature=" + temperature +
            ", humidity=" + humidity +
            ", activity=" + activity +
            ", co2=" + co2 +
            ", tvoc=" + tvoc +
            ", illumination=" + illumination +
            ", infrared=" + infrared +
            ", infrared_and_visible=" + infrared_and_visible +
            ", pressure=" + pressure +
            ", name='" + name + '\'' +
            ", devEUI='" + devEUI + '\'' +
            ", room='" + room + '\'' +
            ", floor=" + floor +
            ", Building='" + Building + '\'' +
            '}';
}

}