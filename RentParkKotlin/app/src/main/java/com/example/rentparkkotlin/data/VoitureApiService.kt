package com.example.rentparkkotlin.data

import com.example.rentparkkotlin.model.Voiture
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
    suspend fun addVoiture(@Body newVoiture: Voiture) : retrofit2.Response<Unit>

    @DELETE("vehicules/{numSerie}")
    suspend fun deleteVoiture(@Path("id") id: String): retrofit2.Response<Unit>

    @GET("vehicules/{numSerie}")
    suspend fun getVoiture(@Path("numSerie") numSerie: String): Voiture

    @PUT("vehicules/{numSerie}")
    suspend fun updateVoiture(@Path("numSerie") numSerie: String, @Body updatedVoiture: Voiture): retrofit2.Response<Unit>
}