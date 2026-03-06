package com.example.rentparkkotlin.data
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.model.Contrat
import retrofit2.http.GET


interface ApiService {

    @GET("voitures")
    suspend fun getVoitures(): List<Voiture>

    @GET("contrat")
    suspend fun getContrats(): List<Contrat>
}
