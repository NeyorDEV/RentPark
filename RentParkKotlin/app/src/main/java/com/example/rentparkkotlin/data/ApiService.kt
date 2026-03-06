package com.example.rentparkkotlin.data
import com.example.rentparkkotlin.model.Voiture
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path


interface ApiService {

    @GET("voitures")
    suspend fun getVoitures(): List<Voiture>

    @POST("vehicule")
    suspend fun addVoiture(@Body newVoiture: Voiture) : retrofit2.Response<Unit>

    @DELETE("delete/voitures/{id}")
    suspend fun deleteVoiture(@Path("id") id: String): retrofit2.Response<Unit>
}
