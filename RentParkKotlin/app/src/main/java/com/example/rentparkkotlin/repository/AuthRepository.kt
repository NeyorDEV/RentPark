package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.LoginRequest
import com.example.rentparkkotlin.model.LoginResponse
import com.example.rentparkkotlin.model.RegisterRequest
import com.example.rentparkkotlin.model.RegisterResponse
import retrofit2.Response

class AuthRepository {
    suspend fun login(request: LoginRequest): Response<LoginResponse> {
        return RetrofitInstance.api.login(request)
    }

    suspend fun register(request: RegisterRequest): Response<RegisterResponse> {
        return RetrofitInstance.api.register(request)
    }
}