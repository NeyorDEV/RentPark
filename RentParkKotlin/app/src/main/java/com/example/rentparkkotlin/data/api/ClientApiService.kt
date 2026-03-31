package com.example.rentparkkotlin.data.api

import com.example.rentparkkotlin.model.Client
import com.example.rentparkkotlin.model.ClientRequest
import com.example.rentparkkotlin.model.ClientResponse
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Path

interface ClientApiService {
    @GET("clients")
    suspend fun getClients(): List<Client>

    @POST("clients")
    suspend fun addClient(@Body request: ClientRequest): Response<ClientResponse>

    @PUT("clients/{IdClient}")
    suspend fun updateClient(@Path("IdClient") id: Int, @Body request: ClientRequest): Response<Unit>

    @GET("clients/{IdClient}")
    suspend fun getClient(@Path("IdClient") id: Int): Client
}