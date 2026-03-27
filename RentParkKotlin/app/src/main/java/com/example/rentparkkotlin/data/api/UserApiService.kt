package com.example.rentparkkotlin.data.api

import com.example.rentparkkotlin.model.RegisterRequest
import com.example.rentparkkotlin.model.RegisterResponse
import com.example.rentparkkotlin.model.UpdateUserRequest
import com.example.rentparkkotlin.model.User
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.DELETE
import retrofit2.http.GET
import retrofit2.http.PUT
import retrofit2.http.Path
import retrofit2.http.POST

interface UserApiService {
    @GET("utilisateurs")
    suspend fun getUsers(): List<User>

    @PUT("utilisateurs/{id}")
    suspend fun updateUser(@Path("id") id: Int, @Body request: UpdateUserRequest): Response<Unit>

    @DELETE("utilisateurs/{id}")
    suspend fun deleteUser(@Path("id") id: Int): Response<Unit>

    @GET("utilisateurs/{id}")
    suspend fun getUser(@Path("id") id: Int): User
    
    @POST("utilisateurs")
    suspend fun register(@Body request: RegisterRequest): Response<RegisterResponse>
}