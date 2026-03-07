package com.example.rentparkkotlin.model

import com.google.gson.annotations.SerializedName

data class Client(
    @SerializedName("IdClient") val idClient: Int,
    @SerializedName("Nom") val nom: String,
    @SerializedName("Prenom") val prenom: String,
    @SerializedName("Email") val email: String,
    @SerializedName("NumTel") val numTel: String?,
    @SerializedName("NumPermis") val numPermis: String,
    @SerializedName("DateNaiss") val dateNaiss: String?,
    @SerializedName("Nationalite") val nationalite: String?
)

data class ClientRequest(
    @SerializedName("Nom") val nom: String,
    @SerializedName("Prenom") val prenom: String,
    @SerializedName("Email") val email: String,
    @SerializedName("NumTel") val numTel: String?,
    @SerializedName("NumPermis") val numPermis: String,
    @SerializedName("DateNaiss") val dateNaiss: String?,
    @SerializedName("Nationalite") val nationalite: String?
)

data class ClientResponse(
    @SerializedName("message") val message: String?,
    @SerializedName("idClient") val idClient: Int?,
    @SerializedName("error") val error: String?
)