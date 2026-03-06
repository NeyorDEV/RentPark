package com.example.rentparkkotlin.data
import com.example.rentparkkotlin.model.Voiture

import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.model.LoginRequest
import com.example.rentparkkotlin.model.LoginResponse
import retrofit2.Response

interface ApiService {

    @GET("voitures")
    suspend fun getVoitures(): List<Voiture>

    @POST("vehicule")
    suspend fun addVoiture(@Body newVoiture: Voiture) : retrofit2.Response<Unit>

    @DELETE("delete/voitures/{id}")
    suspend fun deleteVoiture(@Path("id") id: String): retrofit2.Response<Unit>
    @GET("contrat")
    suspend fun getContrats(): List<Contrat>

    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<LoginResponse>
}
