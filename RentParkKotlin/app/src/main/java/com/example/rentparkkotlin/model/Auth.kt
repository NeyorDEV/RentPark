package com.example.rentparkkotlin.model

import com.google.gson.annotations.SerializedName

data class LoginRequest(
    val username: String,
    val password: String
)

data class LoginResponse(
    @SerializedName("status") val status: String?,
    @SerializedName("token") val token: String?,
    @SerializedName("user") val user: UserInfo?,
    @SerializedName("message") val message: String?,
    @SerializedName("error") val error: String?
)

data class UserInfo(
    @SerializedName("username") val username: String?,
    @SerializedName("role") val role: String?
)

data class RegisterRequest(
    val username: String,
    val password: String,
    val role: String
)

data class RegisterResponse(
    @SerializedName("message") val message: String?,
    @SerializedName("error") val error: Boolean
)