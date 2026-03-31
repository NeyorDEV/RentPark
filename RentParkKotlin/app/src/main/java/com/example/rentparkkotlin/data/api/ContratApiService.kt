package com.example.rentparkkotlin.data.api

import com.example.rentparkkotlin.model.Contrat
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.PATCH
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Path

interface ContratApiService {
    @GET("contrat")
    suspend fun getContrats(): List<Contrat>

    @POST("contrat")
    suspend fun addContrat(@Body contrat: Contrat): Contrat

    @PUT("contrat/{id}")
    suspend fun updateContrat(@Path("id") id: Int, @Body contrat: Contrat): Contrat

    @PATCH("contrat/{id}")
    suspend fun partialUpdateContrat(@Path("id") id: Int, @Body contrat: Contrat): Contrat
    
    @PATCH("contrat/{id}/statut")
    suspend fun updateContratStatut(@Path("id") id: Int, @Body statut: String): Contrat

    @DELETE("contrat/{id}")
    suspend fun deleteContrat(@Path("id") id: Int): Contrat

    @GET("contrat/{id}")
    suspend fun getContrat(@Path("id") id: Int): Contrat
}