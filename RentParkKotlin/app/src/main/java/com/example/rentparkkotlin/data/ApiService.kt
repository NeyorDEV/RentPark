package com.example.rentparkkotlin.data
import com.example.rentparkkotlin.model.*
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
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

    @GET("stats/total-users")
    suspend fun getTotalUsers(): TotalUsersResponse

    @GET("stats/voiture-plus-louee")
    suspend fun getMostRentedCar(): MostRentedCarResponse

    @GET("stats/revenus-mensuel")
    suspend fun getMonthlyIncome(): MonthlyIncomeResponse

    @GET("rappels")
    suspend fun getRappels(): List<Rappel>

    @GET("stats/controle-technique-bientot-expire")
    suspend fun getCTAlerts(): CTExpireResponse

    @GET("stats/contrats-prochains")
    suspend fun getContratsProchains(): ContratsProchainsResponse
}
