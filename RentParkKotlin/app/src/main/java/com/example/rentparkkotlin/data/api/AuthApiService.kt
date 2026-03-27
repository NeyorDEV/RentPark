package com.example.rentparkkotlin.data.api

import com.example.rentparkkotlin.model.LoginRequest
import com.example.rentparkkotlin.model.LoginResponse
import com.example.rentparkkotlin.model.RegisterRequest
import com.example.rentparkkotlin.model.RegisterResponse
import retrofit2.Response
import retrofit2.http.Body
import retrofit2.http.POST

interface AuthApiService {
    @POST("login")
    suspend fun login(@Body request: LoginRequest): Response<LoginResponse>

    @POST("register")
    suspend fun register(@Body request: RegisterRequest): Response<RegisterResponse>
}