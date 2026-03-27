package com.example.rentparkkotlin.data
import com.example.rentparkkotlin.model.*
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.Response
import retrofit2.http.PUT

interface ApiService {


    @GET("contrat")
    suspend fun getContrats(): List<Contrat>



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



    @GET("users")
    suspend fun getUsers(): List<User>

    @PUT("users/{id}")
    suspend fun updateUser(@Path("id") id: Int, @Body request: UpdateUserRequest): Response<Unit>

    @DELETE("users/{id}")
    suspend fun deleteUser(@Path("id") id: Int): Response<Unit>

    @GET("clients")
    suspend fun getClients(): List<Client>

    @POST("client")
    suspend fun addClient(@Body request: ClientRequest): Response<ClientResponse>

    @PUT("client/{id}")
    suspend fun updateClient(@Path("id") id: Int, @Body request: ClientRequest): Response<Unit>
}
