package projet.application.Acces;

import java.io.FileWriter;
import java.io.IOException;
import java.util.List;


/**
 * Classe responsable de la génération et de l'écriture d'un fichier de configuration `config.ini`.
 *
 * La classe suit un modèle Singleton, ce qui garantit qu'une seule instance est utilisée
 * tout au long de l'application. Elle permet de configurer différents paramètres liés aux capteurs,
 * à l'énergie solaire et aux seuils d'alerte.
 *
 */
public class Ecrivain{

    /** Instance unique de la classe (Singleton). */
    private static Ecrivain instance = null;

    /** Indique si les capteurs sont activés. */
    private boolean capteur;

    /** Indique si les données solaires sont activées. */
    private boolean solar;


    //Il manque : deviceName, devEUI, room, floor, building
    /** Liste des types de données capturées. */
    private String[] typesDonnee;

    /** Liste des types de données solaires capturées. */
    private String[] typesSolaire;

    /** Liste des salles configurées pour la capture de données. */
    private String[] salles;

    /** Fréquence d'enregistrement des données (en secondes). */
    private int frequency;

    /** Seuil d'alerte pour la température. */
    private String seuilTemp;

    /** Seuil d'alerte pour le CO2. */
    private String seuilCo2;

    /** Seuil d'alerte pour l'humidité. */
    private String seuilHumdity;

    /** Seuil d'alerte pour la puissance. */
    private String seuilPower;

    /** Seuil d'alerte pour l'énergie. */
    private String seuilEnergy;


    /**
     * Définit si les capteurs sont activés.
     *
     * @param capteur {@code true} si les capteurs sont activés, sinon {@code false}.
     */
    public void setCapteur(boolean capteur){
        this.capteur=capteur;
    }

    /**
     * Définit si les données solaires sont activées.
     *
     * @param solar {@code true} si les données solaires sont activées, sinon {@code false}.
     */
    public void setSolar(boolean solar){
        this.solar=solar;
    }

    /**
     * Définit la liste des types de données capturées.
     *
     * @param liste tableau de chaînes représentant les types de données.
     */
    public void setTypesDonnee(String[] liste){
        this.typesDonnee=liste;
    }

    /**
     * Définit la liste des types de données solaires capturées.
     *
     * @param liste tableau de chaînes représentant les types de données solaires.
     */
    public void setTypesSolaire(String[] liste){
        this.typesSolaire=liste;
    }

    /**
     * Définit la liste des salles configurées pour la capture de données.
     *
     * @param salles tableau de chaînes représentant les salles.
     */
    public void setSalles(String[] salles){
        this.salles=salles;
    }

    /**
     * Définit la fréquence d'enregistrement des données.
     *
     * @param frequency fréquence en secondes.
     */
    public void setFrequency(int frequency){this.frequency=frequency;}

    /**
     * Définit le seuil d'alerte pour la température.
     *
     * @param seuilTemp seuil de température.
     */
    public void setSeuilTemp(int seuilTemp){
        this.seuilTemp=""+seuilTemp;
    }

    /**
     * Définit le seuil d'alerte pour le CO2.
     *
     * @param seuilCo2 seuil de CO2.
     */
    public void setSeuilCo2(int seuilCo2){
        this.seuilCo2=""+seuilCo2;
    }

    /**
     * Définit le seuil d'alerte pour l'humidité.
     *
     * @param seuilHumdity seuil d'humidité.
     */
    public void setSeuilHumdity(int seuilHumdity){
        this.seuilHumdity=""+seuilHumdity;
    }

    /**
     * Définit le seuil d'alerte pour la puissance.
     *
     * @param seuilPower seuil de puissance.
     */
    public void setSeuilPower(int seuilPower){
        this.seuilPower=""+seuilPower;
    }

    /**
     * Définit le seuil d'alerte pour l'énergie.
     *
     * @param seuilEnergy seuil d'énergie.
     */
    public void setSeuilEnergy(int seuilEnergy){
        this.seuilEnergy=""+seuilEnergy;
    }

    /**
     * Constructeur privé (Singleton).
     * Initialise les valeurs par défaut.
     */
    private Ecrivain(){
        reset();
    }





    /**
     * Génère une chaîne représentant le contenu du fichier de configuration.
     *
     * @return chaîne formatée pour le fichier `config.ini`.
     */
    private String build(){

        String retour="[MQTT]\n"+
                "broker = mqtt.iut-blagnac.fr\n"+
                "port = 1883\n"+
                "topic_subscribe = "+(solar ? "solaredge/#"+(capteur ? ",AM107/by-room/\n" : "\n") : "AM107/by-room/\n")+
                "\n[DATA]\nsalles = ";

        if(salles!=null && capteur)
            for (int i=0;i<salles.length;i++)
                    retour+=salles[i]+(i<salles.length-1 ? "," : "");


        retour+="\ntypes = ";
        if(typesDonnee!=null)
            for (int i=0;i<typesDonnee.length;i++)
                    retour+=typesDonnee[i]+(i<typesDonnee.length-1 ? "," : "");


        retour+="\ntypesSolaire = ";
        if(typesSolaire!=null)
            for (int i=0;i<typesSolaire.length;i++)
                    retour+=typesSolaire[i]+(i<typesSolaire.length-1 ? "," : "");


        retour+="\nfrequency = "+frequency+"\n\n\n"+
                "[OUTPUT]\n"+
                "json_file = donneeJSON.json\n"+
                "alert_file = alertJSON.json\n"+
                "\n"+
                "[ALERTE]";
        if(seuilTemp.length()>0)
            retour+="\nseuil_temperature = "+seuilTemp;
        if(seuilCo2.length()>0)
            retour+="\nseuil_co2 = "+seuilCo2;
        if(seuilHumdity.length()>0)
            retour+="\nseuil_humidity = "+seuilHumdity;
        if(seuilPower.length()>0)
            retour+="\nseuil_power = "+seuilPower;
        if(seuilEnergy.length()>0)
            retour+="\nseuil_energy = "+seuilEnergy;
        return retour;
    }




    /**
     * Écrit le contenu du fichier de configuration dans `config.ini`.
     *
     * @throws IOException si une erreur d'écriture survient.
     */
    public void writeConfig() throws IOException{
        try{
            FileWriter writer = new FileWriter("config.ini");


            writer.write(build());


            writer.close();

        }catch(IOException e){
            System.out.println(e.getMessage());
            throw new IOException(e);
        }
    }

    /**
     * Réinitialise tous les paramètres à leurs valeurs par défaut.
     */
    public void reset(){
        typesDonnee=null;
        this.capteur=false;
        this.solar=false;
        this.salles=null;
        this.seuilTemp=":";
        this.seuilEnergy="";
        this.seuilHumdity="";
        this.seuilPower="";
        this.seuilCo2="";
        this.frequency=60;
    }

    /**
     * Retourne l'instance unique de la classe.
     *
     * @return instance de {@code Ecrivain}.
     */
    public static Ecrivain getInstance(){
        if(instance==null){
            instance=new Ecrivain();
        }
        return instance;
    }
}
