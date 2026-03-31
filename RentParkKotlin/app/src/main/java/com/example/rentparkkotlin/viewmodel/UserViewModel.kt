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
import kotlinx.coroutines.delay
import kotlinx.coroutines.launch
import org.json.JSONObject

class UserViewModel(private val repository: UserRepository = UserRepository()) : ViewModel() {

    var users by mutableStateOf<List<User>>(emptyList())
        private set

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    init {
        fetchUsers()
    }

    fun fetchUsers(showLoading: Boolean = true) {
        viewModelScope.launch {
            if (showLoading) isLoading = true
            try {
                users = repository.getUsers()
                error = null
            } catch (e: Exception) {
                error = "Erreur de chargement: ${e.localizedMessage}"
            }
            if (showLoading) isLoading = false
        }
    }

    fun addUser(username: String, role: String, mdp: String) {
        viewModelScope.launch {
            isLoading = true
            error = null

            try {
                val response = repository.addUser(RegisterRequest(username, mdp, role))

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.error == true) {
                        error = "Erreur serveur"
                    } else {
                        fetchUsers(showLoading = false)
                    }
                } else {
                    val errorBody = response.errorBody()?.string()
                    if (errorBody != null) {
                        try {
                            val jsonError = JSONObject(errorBody)
                            error = jsonError.optString("error", jsonError.optString("message", "Erreur lors de l'ajout"))
                        } catch (e: Exception) {
                            error = "Erreur lors de l'ajout"
                        }
                    } else {
                        error = "Erreur serveur"
                    }
                }
            } catch (e: Exception) {
                error = "Erreur réseau: ${e.localizedMessage}"
            } finally {
                isLoading = false
            }
        }
    }

    fun updateUser(id: Int, username: String, role: String, mdp: String?) {
        viewModelScope.launch {
            isLoading = true
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
            } finally {
                isLoading = false
            }
        }
    }

    fun deleteUser(id: Int) {
        viewModelScope.launch {
            isLoading = true
            try {
                val response = repository.deleteUser(id)
                if (response.isSuccessful) {
                    fetchUsers()
                } else {
                    error = "Erreur lors de la suppression"
                }
            } catch (e: Exception) {
                error = "Erreur réseau: ${e.localizedMessage}"
            } finally {
                isLoading = false
            }
        }
    }
}