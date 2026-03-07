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

    var selectedVoiture by mutableStateOf<Voiture?>(null)
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

    fun updateVoiture(voiture: Voiture) {
        viewModelScope.launch {
            try {
                val response = repository.updateVoiture(voiture)
                if (response.isSuccessful) {
                    fetchVoitures() // Recharger la liste
                }
            } catch (e: Exception) {
            }
        }
    }

    fun getVoitureDetails(numSerie: String) {
        viewModelScope.launch {
            isLoading = true
            try {
                val response = repository.getVoitureByNumSerie(numSerie)
                if (response.isSuccessful) {
                    selectedVoiture = response.body()
                }
            } catch (e: Exception) {
                error = "Impossible de charger les détails"
            } finally {
                isLoading = false
            }
        }
    }
    fun clearSelectedVoiture() {
        selectedVoiture = null
    }

}