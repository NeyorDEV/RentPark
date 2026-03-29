package com.example.rentparkkotlin.viewmodel

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.repository.ContratRepository
import kotlinx.coroutines.launch

// Modifie l'en-tête de ta classe comme ceci :
class ContratViewModel(private val repository: ContratRepository = ContratRepository()) : ViewModel() {
    var contrats by mutableStateOf<List<Contrat>>(emptyList())
        private set

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    init {
        fetchContrats()
    }

    fun fetchContrats() {
        viewModelScope.launch {
            isLoading = true
            try {
                contrats = repository.getContrats()
                error = null
            } catch (e: Exception) {
                error = "Erreur de chargement: ${e.localizedMessage}"
            }
            isLoading = false
        }
    }

    fun addContrat(contrat: Contrat) {
        viewModelScope.launch {
            try {
                repository.addContrat(contrat)
                fetchContrats()
            } catch (e: Exception) {
                error = "Erreur d'ajout: ${e.localizedMessage}"
            }
        }
    }

    fun updateContrat(id: Int, contrat: Contrat) {
        viewModelScope.launch {
            try {
                repository.updateContrat(id, contrat)
                fetchContrats()
            } catch (e: Exception) {
                error = "Erreur de modification: ${e.localizedMessage}"
            }
        }
    }

    fun deleteContrat(id: Int) {
        viewModelScope.launch {
            try {
                repository.deleteContrat(id)
                fetchContrats()
            } catch (e: Exception) {
                error = "Erreur de suppression: ${e.localizedMessage}"
            }
        }
    }
}