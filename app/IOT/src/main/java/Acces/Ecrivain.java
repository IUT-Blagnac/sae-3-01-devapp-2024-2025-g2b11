package Acces;
import java.io.FileWriter;
import java.io.IOException;

public class Ecrivain{

    private static Ecrivain instance=null;

    private boolean capteur;
    private boolean solar;


    private boolean temperature;
    private boolean humidity;
    private boolean co2;
    private boolean pressure;
    private boolean activity;
    private boolean tvoc;
    private boolean illumination;
    private boolean infrared;
    private boolean infraredVis;
    private boolean deviceName;
    private boolean devEUI;
    private boolean room;
    private boolean floor;
    private boolean building;


    private boolean totalD;
    private boolean lYearD;
    private boolean lMonthD;
    private boolean lDayD;
    private boolean currP;
    private boolean lastUpdate;


    private String salles;
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

    public void setTemperature(boolean t){
        this.temperature=t;
    }

    public void setHumidity(boolean humidity){
        this.humidity=humidity;
    }

    public void setCo2(boolean co2){
        this.co2=co2;
    }

    public void setPressure(boolean pressure){
        this.pressure=pressure;
    }

    public void setActivity(boolean activity){
        this.activity=activity;
    }

    public void setTvoc(boolean tvoc){
        this.tvoc=tvoc;
    }

    public void setIllumination(boolean illumination){
        this.illumination=illumination;
    }

    public void setInfrared(boolean infrared){
        this.infrared=infrared;
    }

    public void setInfraredVis(boolean infraredVis){
        this.infraredVis=infraredVis;
    }

    public void setTotalD(boolean totalD){
        this.totalD=totalD;
    }

    public void setlYearD(boolean lYearD){
        this.lYearD=lYearD;
    }

    public void setlMonthD(boolean lMonthD){
        this.lMonthD=lMonthD;
    }

    public void setlDayD(boolean lDayD){
        this.lDayD=lDayD;
    }

    public void setCurrP(boolean currP){
        this.currP=currP;
    }

    public void setSalles(String salles){
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
    public void setLastUpdate(boolean lastUpdate){this.lastUpdate=lastUpdate;}

    public void setDeviceName(boolean deviceName){
        this.deviceName=deviceName;
    }

    public void setDevEUI(boolean devEUI){
        this.devEUI=devEUI;
    }

    public void setRoom(boolean room){
        this.room=room;
    }

    public void setFloor(boolean floor){
        this.floor=floor;
    }

    public void setBuilding(boolean building){
        building=building;
    }

    private Ecrivain(){
        reset();
    }





    private String build(){
        boolean type=false;

        String retour="[MQTT]\n"+
                "broker = mqtt.iut-blagnac.fr\n"+
                "port = 1883\n"+
                "topic_subscribe = "+(solar ? "solaredge/#"+(capteur ? ",AM107/by-room/\n" : "\n") : "AM107/by-room/\n")+
                "\n[DATA]\nsalles = ";
        if(salles.length()>0 && capteur)
            retour+=salles;
        retour+="\ntypes = ";

        if(temperature){
            retour+="temperature";
            type=true;
        }
        if(humidity){
            retour+=(type?",":"")+"humidity";
            type=true;
        }
        if(co2){
            retour+=(type?",":"")+"co2";
            type=true;
        }
        if(pressure){
            retour+=(type?",":"")+"pressure";
            type=true;
        }
        if(activity){
            retour+=(type?",":"")+"activity";
            type=true;
        }
        if(tvoc){
            retour+=(type?",":"")+"tvoc";
            type=true;
        }
        if(illumination){
            retour+=(type?",":"")+"illumination";
            type=true;
        }
        if(infrared){
            retour+=(type?",":"")+"infrared";
            type=true;
        }
        if(infraredVis){
            retour+=(type?",":"")+"infrared_and_visible";
            type=true;
        }
        if(deviceName){
            retour+=(type?",":"")+"deviceName";
            type=true;
        }
        if(devEUI){
            retour+=(type?",":"")+"devEUI";
            type=true;
        }
        if(room){
            retour+=(type?",":"")+"room";
            type=true;
        }
        if(floor){
            retour+=(type?",":"")+"floor";
            type=true;
        }
        if(building){
            retour+=(type?",":"")+"Building";
        }

        retour+="\ntypesSolaire = ";
        type=false;
        if(totalD){
            retour+="lifeTimeData";
            type=true;
        }
        if(lYearD){
            retour+=(type?",":"")+"lastYearData";
            type=true;
        }
        if(lMonthD){
            retour+=(type?",":"")+"lastMonthData";
            type=true;
        }
        if(lDayD){
            retour+=(type?",":"")+"lastDayData";
            type=true;
        }
        if(lastUpdate){
            retour+=(type?",":"")+"lastUpdateTime";
            type=true;
        }
        if(currP){
            retour+=(type?",":"")+"currentPower";
        }

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
        this.temperature=false;
        this.capteur=false;
        this.solar=false;
        this.humidity=false;
        this.co2=false;
        this.pressure=false;
        this.activity=false;
        this.tvoc=false;
        this.illumination=false;
        this.infrared=false;
        this.infraredVis=false;
        this.building=false;
        this.floor=false;
        this.room=false;
        this.devEUI=false;
        this.deviceName=false;
        this.totalD=false;
        this.lYearD=false;
        this.lMonthD=false;
        this.lDayD=false;
        this.currP=false;
        this.salles="";
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
