package com.example.rentparkkotlin.viewmodel

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.model.RegisterRequest
import com.example.rentparkkotlin.repository.AuthRepository
import kotlinx.coroutines.launch
import org.json.JSONObject

class RegisterViewModel(private val repository: AuthRepository) : ViewModel() {

    var username by mutableStateOf("")
    var password by mutableStateOf("")
    var confirmPassword by mutableStateOf("")

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    var registerSuccess by mutableStateOf(false)
        private set

    fun register() {
        if (username.isBlank() || password.isBlank() || confirmPassword.isBlank()) {
            error = "Veuillez remplir tous les champs"
            return
        }

        if (password != confirmPassword) {
            error = "Les mots de passe ne correspondent pas"
            return
        }

        viewModelScope.launch {
            isLoading = true
            error = null
            try {
                val request = RegisterRequest(username, password, "user")
                val response = repository.register(request)

                if (response.isSuccessful) {
                    registerSuccess = true
                } else {
                    val errorBody = response.errorBody()?.string()
                    if (errorBody != null) {
                        try {
                            val jsonError = JSONObject(errorBody)
                            error = jsonError.optString("error", "Erreur lors de l'inscription")
                        } catch (e: Exception) {
                            error = "Erreur lors de l'inscription"
                        }
                    } else {
                        error = "Erreur serveur"
                    }
                }
            } catch (e: Exception) {
                error = "Erreur réseau : ${e.localizedMessage}"
            }
            isLoading = false
        }
    }
}