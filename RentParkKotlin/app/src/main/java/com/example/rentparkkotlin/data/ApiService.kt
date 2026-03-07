package com.example.rentparkkotlin.data
import com.example.rentparkkotlin.model.*
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

    @POST("add/users")
    suspend fun register(@Body request: RegisterRequest): Response<RegisterResponse>
}
