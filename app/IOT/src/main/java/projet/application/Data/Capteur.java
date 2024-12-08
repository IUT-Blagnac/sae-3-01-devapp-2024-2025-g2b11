package projet.application.Data;

/**
 * Classe représentant un capteur et ses données associées.
 *
 * Cette classe contient des informations sur les mesures collectées par un capteur,
 * ainsi que des métadonnées concernant le capteur lui-même, telles que son emplacement
 * et son identification.
 *
 */
public class Capteur{

    /** Température mesurée par le capteur en degrés Celsius. */
    public float temperature;

    /** Humidité mesurée par le capteur en pourcentage. */
    public float humidity;

    /** Activité détectée par le capteur, représentée par une valeur entière. */
    public int activity;

    /** Taux de CO2 mesuré par le capteur en ppm (parties par million). */
    public int co2;

    /** Concentration de composés organiques volatils totaux (TVOC) mesurée en ppm. */
    public int tvoc;

    /** Niveau d'illumination mesuré par le capteur en lux. */
    public int illumination;

    /** Niveau d'infrarouge détecté par le capteur. */
    public int infrared;

    /** Niveau combiné d'infrarouge et de lumière visible détecté. */
    public int infrared_and_visible;

    /** Pression atmosphérique mesurée par le capteur en hPa. */
    public float pressure;

    /** Nom du capteur. */
    public String name;

    /** Identifiant unique du dispositif (devEUI). */
    public String devEUI;

    /** Salle où le capteur est installé. */
    public String room;

    /** Étage où le capteur est situé. */
    public int floor;

    /** Bâtiment où le capteur est installé. */
    public String Building;

    /** Date de la collecte des données, formatée en jour (JJ/MM/AAAA). */
    public String jour;

    /** Heure de la collecte des données, formatée en HH:mm:ss. */
    public String heure;

    /**
     * Redéfinit la méthode `toString` pour fournir une représentation textuelle des données du capteur.
     *
     * @return une chaîne de caractères représentant les données et les métadonnées du capteur.
     */
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
            ", jour='" + jour + '\'' +
            ", heure='" + heure + '\'' +
            '}';
}

}