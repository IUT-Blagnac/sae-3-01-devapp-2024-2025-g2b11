package projet.application.Acces;

import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.StandardCopyOption;

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

             // Récupération du script Python depuis les ressources
            InputStream pythonScriptStream = RunPythonBackground.class.getResourceAsStream("IoTPythonSAEvFinal.py");

            if (pythonScriptStream == null) {
                throw new FileNotFoundException("Le fichier Python 'IoTPythonSAEvFinal.py' est introuvable dans le classpath.");
            } 

            Path tempScriptPath = Files.createTempFile("IoTPythonSAEvFinal", ".py");
            Files.copy(pythonScriptStream, tempScriptPath, StandardCopyOption.REPLACE_EXISTING);

            ProcessBuilder pb = new ProcessBuilder(
                "py", // Commande pour exécuter Python
                tempScriptPath.toString(), // Chemin relatif vers le script Python
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
