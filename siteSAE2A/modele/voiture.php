<?php
namespace modele;
class Voiture {
    private int $voiture;     // ex : id ou nombre
    private string $modele;   // ex : "BMW"
    private string $couleur;     // ex : "noir"

    private string $puissance; 

    public function __construct(int $voiture, string $modele, string $couleur, string $puissance) {
        $this->voiture = $voiture;
        $this->modele = $modele;
        $this->couleur = $couleur;
        $this->puissance = $puissance;
    }

    // Getters
    public function getVoiture(): int {
        return $this->voiture;
    }

    public function getModele(): string {
        return $this->modele;
    }

    public function getCouleur(): string {
        return $this->couleur;
    }

    public function getPuissance(): string {
        return $this->puissance;

    }  
   

    // Pour pouvoir afficher directement l'objet avec echo
    public function __toString(): string {
        return "Voiture: {$this->voiture}, Modèle: {$this->modele}, Couleur: {$this->couleur}, Puissance:{$this->puissance} ";
    }
}
?>
