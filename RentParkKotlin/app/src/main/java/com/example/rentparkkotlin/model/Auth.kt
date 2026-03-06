package com.example.rentparkkotlin.model

import com.google.gson.annotations.SerializedName

data class LoginRequest(
    val username: String,
    val password: String
)

data class LoginResponse(
    @SerializedName("success") val success: Boolean?,
    @SerializedName("token") val token: String?,
    @SerializedName("role") val role: String?,
    @SerializedName("username") val username: String?,
    @SerializedName("id") val id: Int?,
    @SerializedName("message") val message: String?,
    @SerializedName("error") val error: String?
)