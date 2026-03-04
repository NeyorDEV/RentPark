<?php

namespace App\DTO;
class VehiculeResponseDTO
{
    public int $id;
    public string $marque;
    public string $modele;
    public array $_links;

    public function __construct($vehicule, array $links)
    {
        $this->id = $vehicule['id'];
        $this->marque = $vehicule['marque'];
        $this->modele = $vehicule['modele'];
        $this->_links = $links;
    }
}