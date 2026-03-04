<?php
namespace config;


class Validation
{

    public static function val_action($action)
    {

        if (!isset($action)) {
            throw new \Exception('pas d\'action');
            //on pourrait aussi utiliser
//$action = $_GET['action'] ?? 'no';
            // This is equivalent to:
            //$action =  if (isset($_GET['action'])) $action=$_GET['action']  else $action='no';
        }
    }

    /**
     * Valide les données d'un formulaire voiture
     *
     * @param string $modele
     * @param string $couleur
     * @param string|int $puissance
     * @param array &$dVueErreur
     */
    public static function val_voiture(string &$modele, string &$couleur, &$puissance, array &$dVueErreur)
    {
        // Modèle
        if (!isset($modele) || trim($modele) === '') {
            $dVueErreur[] = "Le modèle est obligatoire";
        } else {
            // Protection contre injection de code
            $modele = filter_var($modele, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        // Couleur
        if (!isset($couleur) || trim($couleur) === '') {
            $dVueErreur[] = "La couleur est obligatoire";
        } else {
            $couleur = filter_var($couleur, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        // Puissance
        if (!isset($puissance) || !filter_var($puissance, FILTER_VALIDATE_INT)) {
            $dVueErreur[] = "La puissance doit être un entier";
            $puissance = 0;
        } else {
            $puissance = (int)$puissance;
        }
    }
    public static function val_user(string &$username, string &$password, string &$role, array &$errors)
    {
        $username = trim($username ?? '');
        $password = trim($password ?? '');
        $role     = trim($role ?? '');

        if ($username === '' || $password === '' || $role === '') {
            $errors[] = "Tous les champs sont requis.";
        }

        if (!in_array($role, ['admin', 'employe', 'user'])) {
            $errors[] = "Rôle invalide.";
        }

        
        $username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }

    public static function val_connection(string &$username, string &$password, ?string $savepass, array &$errors)
    {
        // Nettoyage et vérification de base
        $username = trim($username ?? '');
        $password = trim($password ?? '');
    
        if ($username === '' || $password === '') {
            $errors[] = "Tous les champs sont requis.";
            return;
        }
    
        // Vérifie que le hash est bien fourni
        if ($savepass === null) {
            $errors[] = "Utilisateur introuvable.";
            return;
        }
    
        // Vérifie la correspondance entre mot de passe saisi et hash stocké
        if (!password_verify($password, $savepass)) {
            $errors[] = "Mot de passe ou nom d'utilisateur invalide.";
        }
    
        // Nettoyage du nom d’utilisateur
        $username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    

}

?>

