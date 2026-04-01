package com.example.rentparkkotlin.data.api

import com.example.rentparkkotlin.model.Rappel
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST

interface RappelApiService {
    @GET("rappels")
    suspend fun getRappels(): List<Rappel>

    @POST("rappels")
    suspend fun addRappel(@Body rappel: Rappel): Rappel
}