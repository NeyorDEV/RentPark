package com.example.rentparkkotlin.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.launch
import androidx.compose.runtime.*
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.repository.CarRepository

/*
    Si tu lances un test tel quel, ton ViewModel va essayer de contacter la vraie API sur internet.
    Si l'API est hors ligne ou si tu n'as pas de réseau, ton test échoue.
    Un test unitaire doit être isolé et déterministe.
    
    La solution : L'Injection de Dépendances
    Tu dois passer le repository au constructeur du ViewModel.
    Ainsi, pendant le test, on pourra lui donner un "Faux" repository (Mock) qui simule des réponses
    (succès, erreur 500, liste vide, etc.).
*/

class CarViewModel(private val repository: CarRepository = CarRepository()) : ViewModel() {

    var voitures by mutableStateOf<List<Voiture>>(emptyList())
        private set

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    var selectedVoiture by mutableStateOf<Voiture?>(null)
        private set

    var deleteError by mutableStateOf<String?>(null)
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
            isLoading = true
            try {
                val response = repository.addVoiture(voiture)
                if (response.isSuccessful) {
                    fetchVoitures()
                } else {
                    val serverError = response.errorBody()?.string() ?: "Erreur inconnue"
                    error = "Erreur serveur (${response.code()}) : $serverError"
                }
            } catch (e: Exception) {
                error = "Impossible d'ajouter le véhicule : ${e.message}"
            } finally {
                isLoading = false
            }
        }
    }

    fun deleteVoiture(id: String) {
        viewModelScope.launch {
            isLoading = true
            try {
                val response = repository.deleteVoiture(id)
                if (response.isSuccessful) {
                    fetchVoitures()
                } else {
                    deleteError = "Impossible de supprimer ce véhicule : il est actuellement relié à un ou plusieurs contrats."
                }
            } catch (e: Exception) {
                deleteError = "Erreur réseau : impossible de joindre le serveur."
            } finally {
                isLoading = false
            }
        }
    }

    fun updateVoiture(voiture: Voiture) {
        viewModelScope.launch {
            isLoading = true
            try {
                val response = repository.updateVoiture(voiture.NumSerie, voiture)
                if (response.isSuccessful) {
                    fetchVoitures() // Recharger la liste
                }
            } catch (e: Exception) {
            } finally {
                isLoading = false
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

    fun clearDeleteError() {
        deleteError = null
    }
}

