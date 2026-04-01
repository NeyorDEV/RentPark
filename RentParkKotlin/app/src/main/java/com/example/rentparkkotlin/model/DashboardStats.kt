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