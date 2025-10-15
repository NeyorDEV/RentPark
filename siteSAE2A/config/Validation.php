<?php
namespace config;

class Validation
{

    static function val_action($action)
    {

        if (!isset($action)) {
            throw new Exception('pas d\'action');
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
    public static function val_user(string &$username, string &$password, string &$confirm, string &$role, array &$errors)
    {
        $username = trim($username ?? '');
        $password = trim($password ?? '');
        $confirm  = trim($confirm ?? '');
        $role     = trim($role ?? '');

        if ($username === '' || $password === '' || $confirm === '' || $role === '') {
            $errors[] = "Tous les champs sont requis.";
        }

        if ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }

        if (!in_array($role, ['admin', 'employe', 'client'])) {
            $errors[] = "Rôle invalide.";
        }

        
        $username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }



}

?>

