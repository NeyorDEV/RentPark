package com.example.rentparkkotlin.viewmodel

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.model.Client
import com.example.rentparkkotlin.model.ClientRequest
import com.example.rentparkkotlin.repository.ClientRepository
import kotlinx.coroutines.launch

class ClientViewModel : ViewModel() {

    private val repository = ClientRepository()

    var clients by mutableStateOf<List<Client>>(emptyList())
        private set

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    init {
        fetchClients()
    }

    fun fetchClients() {
        viewModelScope.launch {
            isLoading = true
            try {
                clients = repository.getClients()
                error = null
            } catch (e: Exception) {
                error = "Erreur de chargement: ${e.localizedMessage}"
            }
            isLoading = false
        }
    }

    fun addClient(request: ClientRequest) {
        viewModelScope.launch {
            try {
                val response = repository.addClient(request)
                if (response.isSuccessful) {
                    fetchClients()
                } else {
                    error = "Erreur lors de l'ajout du client"
                }
            } catch (e: Exception) {
                error = "Erreur réseau: ${e.localizedMessage}"
            }
        }
    }

    fun updateClient(id: Int, request: ClientRequest) {
        viewModelScope.launch {
            try {
                val response = repository.updateClient(id, request)
                if (response.isSuccessful) {
                    fetchClients()
                } else {
                    error = "Erreur lors de la modification du client"
                }
            } catch (e: Exception) {
                error = "Erreur réseau: ${e.localizedMessage}"
            }
        }
    }
}