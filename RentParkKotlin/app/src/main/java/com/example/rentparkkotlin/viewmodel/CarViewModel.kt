package com.example.rentparkkotlin.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.launch
import androidx.compose.runtime.*
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.repository.CarRepository

class CarViewModel : ViewModel() {

    private val repository = CarRepository()

    var voitures by mutableStateOf<List<Voiture>>(emptyList())
        private set

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    init {
        fetchVoitures()
    }

    private fun fetchVoitures() {
        viewModelScope.launch {
            isLoading = true
            try {
                voitures = repository.getVoitures()
            } catch (e: Exception) {
                error = e.message
            }
            isLoading = false
        }
    }

    fun addVoiture(voiture: Voiture) {
        viewModelScope.launch {
            try {
                val response = repository.addVoiture(voiture)
                if (response.isSuccessful) {
                    fetchVoitures()
                } else {
                    error = "Erreur serveur : ${response.code()}"
                }
            } catch (e: Exception) {
                error = "Impossible d'ajouter le véhicule : ${e.message}"
            }
        }
    }

    fun deleteVoiture(id: String) {
        viewModelScope.launch {
            try {
                val response = repository.deleteVoiture(id)
                if (response.isSuccessful) {
                    fetchVoitures()
                } else {
                    error = "Erreur lors de la suppression"
                }
            } catch (e: Exception) {
                error = "Erreur réseau : ${e.message}"
            }
        }
    }
}