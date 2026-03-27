package com.example.rentparkkotlin.viewmodel

import android.app.Application
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.AndroidViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.data.AuthPrefs
import com.example.rentparkkotlin.model.LoginRequest
import com.example.rentparkkotlin.repository.AuthRepository
import kotlinx.coroutines.launch
import org.json.JSONObject

class LoginViewModel(application: Application) : AndroidViewModel(application) {

    private val repository = AuthRepository()
    private val authPrefs = AuthPrefs(application)

    var username by mutableStateOf("")
    var password by mutableStateOf("")

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    var loginSuccess by mutableStateOf(false)
        private set

    fun login() {
        if (username.isBlank() || password.isBlank()) {
            error = "Veuillez remplir tous les champs"
            return
        }

        viewModelScope.launch {
            isLoading = true
            error = null
            try {
                val response = repository.login(LoginRequest(username, password))

                if (response.isSuccessful) {
                    val body = response.body()
                    if (body?.status == "success") {

                        val token = body.token ?: ""

                        val role = body.user?.role ?: "user"
                        val userStr = body.user?.username ?: username

                        val id = -1

                        authPrefs.saveAuthInfo(token, role, userStr, id)

                        loginSuccess = true
                    } else {
                        error = body?.message ?: body?.error ?: "Erreur de connexion"
                    }
                } else {
                    val errorBody = response.errorBody()?.string()
                    if (errorBody != null) {
                        try {
                            val jsonError = JSONObject(errorBody)
                            error = jsonError.optString("message", jsonError.optString("error", "Identifiants incorrects"))
                        } catch (e: Exception) {
                            error = "Identifiants incorrects"
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