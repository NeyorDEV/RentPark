package com.example.rentparkkotlin.data
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.model.LoginRequest
import com.example.rentparkkotlin.model.LoginResponse
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST


interface ApiService {

    @GET("voitures")
    suspend fun getVoitures(): List<Voiture>

    @GET("contrat")
    suspend fun getContrats(): List<Contrat>

    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<LoginResponse>
}
