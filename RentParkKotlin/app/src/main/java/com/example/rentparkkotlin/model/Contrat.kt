package com.example.rentparkkotlin.model

import com.google.gson.annotations.SerializedName

data class Contrat(
    @SerializedName("IdContrat") val idContrat: Int,
    @SerializedName("DateDebut") val dateDebut: String,
    @SerializedName("DateFin") val dateFin: String,
    @SerializedName("Statut") val statut: String?,
    @SerializedName("IdClient") val idClient: Int?,
    @SerializedName("IdVehicule") val idVehicule: String?
)