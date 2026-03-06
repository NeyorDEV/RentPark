package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.LoginRequest
import com.example.rentparkkotlin.model.LoginResponse
import retrofit2.Response

class AuthRepository {
    suspend fun login(request: LoginRequest): Response<LoginResponse> {
        return RetrofitInstance.api.login(request)
    }
}