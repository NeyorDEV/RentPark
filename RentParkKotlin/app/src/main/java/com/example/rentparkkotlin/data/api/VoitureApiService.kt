package com.example.rentparkkotlin.data.api

import com.example.rentparkkotlin.model.Voiture
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Path

interface VoitureApiService {
    @GET("vehicules")
    suspend fun getVoitures(): List<Voiture>

    @POST("vehicules")
    suspend fun addVoiture(@Body newVoiture: Voiture) : Response<Unit>

    @DELETE("vehicules/{numSerie}")
    suspend fun deleteVoiture(@Path("numSerie") id: String): Response<Unit>

    @GET("vehicules/{numSerie}")
    suspend fun getVoitureByNumSerie(@Path("numSerie") numSerie: String): Response<Voiture>

    @PUT("vehicules/{numSerie}")
    suspend fun updateVoiture(@Path("numSerie") numSerie: String, @Body updatedVoiture: Voiture): Response<Unit>
}