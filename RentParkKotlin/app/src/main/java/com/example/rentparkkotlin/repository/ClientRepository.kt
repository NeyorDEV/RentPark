package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.Client
import com.example.rentparkkotlin.model.ClientRequest
import com.example.rentparkkotlin.model.ClientResponse
import retrofit2.Response

class ClientRepository {
    suspend fun getClients(): List<Client> {
        return RetrofitInstance.apiClient.getClients()
    }

    suspend fun addClient(request: ClientRequest): Response<ClientResponse> {
        return RetrofitInstance.apiClient.addClient(request)
    }

    suspend fun updateClient(id: Int, request: ClientRequest): Response<Unit> {
        return RetrofitInstance.apiClient.updateClient(id, request)
    }

    suspend fun getClient(id: Int): Client {
        return RetrofitInstance.apiClient.getClient(id)
    }
}