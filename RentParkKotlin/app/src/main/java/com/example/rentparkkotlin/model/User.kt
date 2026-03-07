package com.example.rentparkkotlin.model

import com.google.gson.annotations.SerializedName

data class User(
    @SerializedName("id") val id: Int,
    @SerializedName("username") val username: String,
    @SerializedName("role") val role: String
)

data class UpdateUserRequest(
    @SerializedName("username") val username: String,
    @SerializedName("role") val role: String,
    @SerializedName("password") val password: String? = null
)