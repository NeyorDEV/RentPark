package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.Client
import com.example.rentparkkotlin.model.ClientRequest
import com.example.rentparkkotlin.model.ClientResponse
import retrofit2.Response

class ClientRepository {
    suspend fun getClients(): List<Client> {
        return RetrofitInstance.api.getClients()
    }

    suspend fun addClient(request: ClientRequest): Response<ClientResponse> {
        return RetrofitInstance.api.addClient(request)
    }

    suspend fun updateClient(id: Int, request: ClientRequest): Response<Unit> {
        return RetrofitInstance.api.updateClient(id, request)
    }
}