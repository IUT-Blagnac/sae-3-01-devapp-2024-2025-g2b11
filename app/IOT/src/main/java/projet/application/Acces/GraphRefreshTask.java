package projet.application.Acces;


import javafx.application.Platform;

public class GraphRefreshTask implements Runnable {
    private volatile boolean running = true;
    private final Runnable refreshAction;

    /**
     * Constructeur pour le thread de rafraîchissement graphique.
     *
     * @param refreshAction L'action à exécuter périodiquement pour rafraîchir le graphique.
     */
    public GraphRefreshTask(Runnable refreshAction) {
        this.refreshAction = refreshAction;
    }

    @Override
    public void run() {
        while (running) {
            try {
                Thread.sleep(500);
                Platform.runLater(refreshAction);
            } catch (InterruptedException e) {
                Thread.currentThread().interrupt();
            }
        }
    }

    /**
     * Arrête le thread de rafraîchissement.
     */
    public void stop() {
        running = false;
    }
}
