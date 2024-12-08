package projet.application.Acces;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

public class RunPythonBackground implements Runnable {
    private String args;
    private volatile boolean isRunning;
    private int exitCode; // Code de sortie du script Python

    public RunPythonBackground(String _args) {
        this.isRunning = true;
        this.exitCode = -1; // Valeur par défaut pour indiquer une erreur ou pas encore exécuté
        this.args = _args;
    }

    @Override
    public void run() {
        try {
            ProcessBuilder pb = new ProcessBuilder(
                "py", // Commande pour exécuter Python
                "python/IoTPythonSAEvFinal.py", // Chemin relatif vers le script Python
                this.args // Argument pour tester la connexion MQTT
            );

            // Redirige la sortie standard et erreur dans la console Java
            pb.redirectErrorStream(true);

            Process process = pb.start();

        
            // Attendre la fin du script Python
            this.exitCode = process.waitFor();
            System.out.println("Le script Python s'est terminé avec le code : " + this.exitCode);

        } catch (IOException | InterruptedException e) {
            System.err.println("Erreur lors de l'exécution du script Python : " + e.getMessage());
            e.printStackTrace();
        }
    }

    public void stopIt() {
        this.isRunning = false;
    }

    public int getExitCode() {
        return this.exitCode;
    }
}
