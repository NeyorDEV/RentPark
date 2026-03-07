package com.example.rentparkkotlin.model

import com.google.gson.annotations.SerializedName

data class TotalUsersResponse(
    @SerializedName("total_users") val totalUsers: Int
)

data class MostRentedCar(
    @SerializedName("Marque") val marque: String,
    @SerializedName("Modele") val modele: String,
    @SerializedName("nb_locations") val nbLocations: Int
)

data class MostRentedCarResponse(
    @SerializedName("voiture_plus_louee") val voiturePlusLouee: MostRentedCar?
)

data class MonthlyIncomeResponse(
    @SerializedName("monthlyIncome") val monthlyIncome: Float
)

data class Rappel(
    @SerializedName("Titre") val titre: String,
    @SerializedName("Description") val description: String,
    @SerializedName("Date") val date: String
)

data class CTExpire(
    @SerializedName("Marque") val marque: String,
    @SerializedName("Modele") val modele: String,
    @SerializedName("DateExpirationControleTech") val dateExpiration: String
)

data class CTExpireResponse(
    @SerializedName("vehicules_controle_technique_bientot_expire") val vehicules: List<CTExpire>
)

data class ContratProchain(
    @SerializedName("DateDebut") val dateDebut: String,
    @SerializedName("DateFin") val dateFin: String,
    @SerializedName("Marque") val marque: String,
    @SerializedName("Modele") val modele: String
)

data class ContratsProchainsResponse(
    @SerializedName("contrats_prochains") val contratsProchains: List<ContratProchain>
)