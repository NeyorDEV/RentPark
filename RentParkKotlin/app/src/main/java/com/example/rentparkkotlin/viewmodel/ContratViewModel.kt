package com.example.rentparkkotlin.viewmodel

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.repository.ContratRepository
import kotlinx.coroutines.launch
import retrofit2.HttpException
import org.json.JSONObject

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
            isLoading = true
            error = null
            try {
                repository.addContrat(contrat)
                fetchContrats()
            } catch (e: HttpException) {
                val errorBody = e.response()?.errorBody()?.string()
                if (errorBody != null) {
                    try {
                        val jsonError = JSONObject(errorBody)
                        error = jsonError.optString("message", jsonError.optString("error", "Erreur API : ${e.code()}"))
                    } catch (jsonEx: Exception) {
                        error = "Erreur serveur : ${e.code()}"
                    }
                } else {
                    error = "Erreur serveur : ${e.code()}"
                }
            } catch (e: Exception) {
                error = "Erreur d'ajout: ${e.localizedMessage}"
            } finally {
                isLoading = false
            }
        }
    }

    fun updateContrat(id: Int, contrat: Contrat) {
        viewModelScope.launch {
            isLoading = true
            error = null
            try {
                repository.updateContrat(id, contrat)
                fetchContrats()
            } catch (e: HttpException) {
                val errorBody = e.response()?.errorBody()?.string()
                if (errorBody != null) {
                    try {
                        val jsonError = JSONObject(errorBody)
                        error = jsonError.optString("message", jsonError.optString("error", "Erreur API : ${e.code()}"))
                    } catch (jsonEx: Exception) {
                        error = "Erreur serveur : ${e.code()}"
                    }
                } else {
                    error = "Erreur serveur : ${e.code()}"
                }
            } catch (e: Exception) {
                error = "Erreur de modification: ${e.localizedMessage}"
            } finally {
                isLoading = false
            }
        }
    }

    fun deleteContrat(id: Int) {
        viewModelScope.launch {
            isLoading = true
            error = null
            try {
                repository.deleteContrat(id)
                fetchContrats()
            } catch (e: HttpException) {
                val errorBody = e.response()?.errorBody()?.string()
                if (errorBody != null) {
                    try {
                        val jsonError = JSONObject(errorBody)
                        error = jsonError.optString("message", jsonError.optString("error", "Erreur API : ${e.code()}"))
                    } catch (jsonEx: Exception) {
                        error = "Erreur serveur : ${e.code()}"
                    }
                } else {
                    error = "Erreur serveur : ${e.code()}"
                }
            } catch (e: Exception) {
                error = "Erreur de suppression: ${e.localizedMessage}"
            } finally {
                isLoading = false
            }
        }
    }
}