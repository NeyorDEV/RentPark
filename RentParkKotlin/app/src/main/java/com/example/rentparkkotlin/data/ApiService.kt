package com.example.rentparkkotlin.data
import com.example.rentparkkotlin.model.Voiture
import retrofit2.http.GET


interface ApiService {

    @GET("voitures")
    suspend fun getVoitures(): List<Voiture>
}
