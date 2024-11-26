package org.example;

import java.io.IOException;

public class Main{
    public static void main(String[] args) throws IOException{
        Ecrivain e= Ecrivain.getInstance();

        e.setTemperature(true);
        e.setHumidity(true);
        e.setCo2(true);
        e.setTotalD(true);
        e.setLastUpdate(true);
        e.setFrequency(3600);
        e.setSeuilTemp(25);
        e.setSeuilCo2(2000);
        e.setSeuilHumdity(55);

        e.writeConfig();
    }
}