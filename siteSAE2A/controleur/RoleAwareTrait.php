<?php
namespace controleur;

/**
 * Trait RoleAwareTrait
 * 
 * Centralise la gestion du rôle et l'affichage des vues pour tous les contrôleurs.
 * Le rôle est lu depuis $_SESSION['role'] — source unique de vérité.
 * 
 * Utilisation dans les vues PHP  : $role
 * Utilisation dans les vues Twig : {{ role }}
 */
trait RoleAwareTrait
{
    /**
     * Retourne le rôle courant depuis la session.
     */
    public function getRole(): string
    {
        return $_SESSION['role'] ?? 'unknown';
    }

    /**
     * Vérifie que le rôle courant est admin.
     * Redirige en 403 sinon.
     */
    protected function checkAdmin(): void
    {
        if ($this->getRole() !== 'admin') {
            header('HTTP/1.1 403 FORBIDDEN');
            $dVueErreur[] = "Accès refusé : Vous avez besoin d'être connecté en tant qu'admin";
            $this->afficherVue('erreur', $dVueErreur);
        }
    }

    /**
     * Vérifie que le rôle courant est employe ou supérieur.
     * Redirige en 403 sinon.
     */
    protected function checkEmploye(): void
    {
        if (!in_array($this->getRole(), ['admin','employe'])) {
            header('HTTP/1.1 403 FORBIDDEN');
            $dVueErreur[] = "Accès refusé : Vous avez besoin d'être connecté en tant qu'employé ou admin";
            $this->afficherVue('erreur', $dVueErreur);
        }
    }

    /**
     * Vérifie que le rôle courant est user ou supérieur.
     * Redirige en 403 sinon.
     */
    protected function checkUser(): void
    {
        
        if (!in_array($this->getRole(), ['admin','employe','user'])) {
            header('HTTP/1.1 403 FORBIDDEN');
            $dVueErreur[] = "Accès refusé : Vous avez besoin d'être connecté";
            $this->afficherVue('erreur', $dVueErreur);
        }
    }

    /**
     * Vérifie que l'utilisateur est connecté (rôle != 'unknown').
     * Redirige vers /home sinon.
     */
    protected function requireConnected(): void
    {
        if ($this->getRole() === 'unknown' || !isset($_SESSION['username'])) {
            header('Location: /siteSAE2A/connection');
            exit;
        }
    }

    /**
     * Affiche une vue PHP ou Twig en lui injectant automatiquement le rôle.
     *
     * Variables disponibles dans la vue :
     *   - $role       (PHP) / {{ role }}       (Twig) → rôle de l'utilisateur
     *   - $dVueEreur  (PHP) / {{ erreurs }}    (Twig) → tableau d'erreurs
     *   - $results    (PHP) / {{ results }}    (Twig) → données métier
     */
    private function afficherVue(string $vueKey, array $dVueErreur, ?array $results = null): void
    {
        global $rep, $vues, $twig;

        if (!isset($vues[$vueKey])) {
            echo "Vue '$vueKey' non définie.";
            exit;
        }

        $vuePath  = $vues[$vueKey];
        $role     = $this->getRole(); // toujours disponible dans la vue

        // ── Vues Twig ──────────────────────────────────────────────────
        if (str_ends_with($vuePath, '.twig')) {
            echo $twig->render($vuePath, [
                'erreurs' => $dVueErreur,
                'results' => $results,
                'role'    => $role,
            ]);
            return;
        }

        // ── Vues PHP ───────────────────────────────────────────────────
        $cheminVue = realpath($rep . $vuePath);
        if ($cheminVue && file_exists($cheminVue)) {
            // $role, $dVueEreur et $results sont accessibles dans la vue
            require_once($cheminVue); // NOSONAR
        } else {
            echo "Fichier de vue introuvable : " . ($rep . $vuePath);
            exit;
        }
    }
}
