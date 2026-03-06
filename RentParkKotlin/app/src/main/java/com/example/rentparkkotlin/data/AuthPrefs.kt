package com.example.rentparkkotlin.data

import android.content.Context
import android.content.SharedPreferences

class AuthPrefs(context: Context) {
    private val sharedPreferences: SharedPreferences =
        context.getSharedPreferences("auth_settings", Context.MODE_PRIVATE)

    fun saveAuthInfo(token: String, role: String, username: String, id: Int) {
        sharedPreferences.edit()
            .putString("auth_token", token)
            .putString("user_role", role)
            .putString("username", username)
            .putInt("user_id", id)
            .apply()
    }

    fun getToken(): String? = sharedPreferences.getString("auth_token", null)

    fun getRole(): String? = sharedPreferences.getString("user_role", null)

    fun getUsername(): String? = sharedPreferences.getString("username", null)

    fun clearAuth() {
        sharedPreferences.edit().clear().apply()
    }

    fun isLoggedIn(): Boolean = getToken() != null
}