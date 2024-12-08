package projet.application.Acces;

import java.io.FileWriter;
import java.io.IOException;
import java.util.List;

public class Ecrivain{

    private static Ecrivain instance=null;

    private boolean capteur;
    private boolean solar;


    //Il manque : deviceName, devEUI, room, floor, building
    private String[] typesDonnee;


    private String[] typesSolaire;


    private String[] salles;
    private int frequency;

    private String seuilTemp;
    private String seuilCo2;
    private String seuilHumdity;
    private String seuilPower;
    private String seuilEnergy;



    public void setCapteur(boolean capteur){
        this.capteur=capteur;
    }

    public void setSolar(boolean solar){
        this.solar=solar;
    }
    public void setTypesDonnee(String[] liste){
        this.typesDonnee=liste;
    }
    public void setTypesSolaire(String[] liste){
        this.typesSolaire=liste;
    }

    public void setSalles(String[] salles){
        this.salles=salles;
    }
    public void setFrequency(int frequency){this.frequency=frequency;}

    public void setSeuilTemp(int seuilTemp){
        this.seuilTemp=""+seuilTemp;
    }

    public void setSeuilCo2(int seuilCo2){
        this.seuilCo2=""+seuilCo2;
    }

    public void setSeuilHumdity(int seuilHumdity){
        this.seuilHumdity=""+seuilHumdity;
    }

    public void setSeuilPower(int seuilPower){
        this.seuilPower=""+seuilPower;
    }

    public void setSeuilEnergy(int seuilEnergy){
        this.seuilEnergy=""+seuilEnergy;
    }

    private Ecrivain(){
        reset();
    }





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

    public static Ecrivain getInstance(){
        if(instance==null){
            instance=new Ecrivain();
        }
        return instance;
    }
}
