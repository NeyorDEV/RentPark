package com.example.rentparkkotlin.model

data class Voiture(
    val NumSerie: String,
    val Energie: String,
    val NbPlaces: String,
    val Categorie: String,
    val Transmission: String,
    val Boite: String,
    val Etat: String,
    val Puissance: String,
    val DateAchat: String,
    val DateExpirationControleTech: String,
    val DateDernierControleTech: String,
    val Marque: String,
    val Nom: String,
    val Annee: String,
    val IdAssureur: Int,
    val IdFournisseur: Int,
    val ImagePath: String,
    val Couleur: String,
    val Prix: String
)