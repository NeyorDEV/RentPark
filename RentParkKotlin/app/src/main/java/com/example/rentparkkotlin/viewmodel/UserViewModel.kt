package com.example.rentparkkotlin.viewmodel

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.model.RegisterRequest
import com.example.rentparkkotlin.model.UpdateUserRequest
import com.example.rentparkkotlin.model.User
import com.example.rentparkkotlin.repository.UserRepository
import kotlinx.coroutines.launch

class UserViewModel(private val repository: UserRepository) : ViewModel() {

    var users by mutableStateOf<List<User>>(emptyList())
        private set

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    init {
        fetchUsers()
    }

    fun fetchUsers() {
        viewModelScope.launch {
            isLoading = true
            try {
                users = repository.getUsers()
                error = null
            } catch (e: Exception) {
                error = "Erreur de chargement: ${e.localizedMessage}"
            }
            isLoading = false
        }
    }

    fun addUser(username: String, role: String, mdp: String) {
        viewModelScope.launch {
            try {
                val response = repository.addUser(RegisterRequest(username, mdp, role))
                if (response.isSuccessful) {
                    fetchUsers()
                } else {
                    error = "Erreur lors de l'ajout"
                }
            } catch (e: Exception) {
                error = "Erreur réseau: ${e.localizedMessage}"
            }
        }
    }

    fun updateUser(id: Int, username: String, role: String, mdp: String?) {
        viewModelScope.launch {
            try {
                val request = UpdateUserRequest(username, role, if (mdp.isNullOrBlank()) null else mdp)
                val response = repository.updateUser(id, request)
                if (response.isSuccessful) {
                    fetchUsers()
                } else {
                    error = "Erreur lors de la modification"
                }
            } catch (e: Exception) {
                error = "Erreur réseau: ${e.localizedMessage}"
            }
        }
    }

    fun deleteUser(id: Int) {
        viewModelScope.launch {
            try {
                val response = repository.deleteUser(id)
                if (response.isSuccessful) {
                    fetchUsers()
                } else {
                    error = "Erreur lors de la suppression"
                }
            } catch (e: Exception) {
                error = "Erreur réseau: ${e.localizedMessage}"
            }
        }
    }
}