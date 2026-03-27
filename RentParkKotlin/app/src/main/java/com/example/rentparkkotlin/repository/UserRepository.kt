package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.RegisterRequest
import com.example.rentparkkotlin.model.UpdateUserRequest
import com.example.rentparkkotlin.model.User
import retrofit2.Response

class UserRepository {

    suspend fun getUsers(): List<User> {
        return RetrofitInstance.apiUser.getUsers()
    }

    suspend fun addUser(request: RegisterRequest): Response<com.example.rentparkkotlin.model.RegisterResponse> {
        return RetrofitInstance.apiUser.register(request)
    }

    suspend fun updateUser(id: Int, request: UpdateUserRequest): Response<Unit> {
        return RetrofitInstance.apiUser.updateUser(id, request)
    }

    suspend fun deleteUser(id: Int): Response<Unit> {
        return RetrofitInstance.apiUser.deleteUser(id)
    }

    suspend fun getUser(id: Int): User {
        return RetrofitInstance.apiUser.getUser(id)
    }
}