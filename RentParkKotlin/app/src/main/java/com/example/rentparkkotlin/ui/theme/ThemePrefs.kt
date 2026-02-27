package com.example.rentparkkotlin.ui.theme

import android.content.Context
import android.content.SharedPreferences

class ThemePrefs(context: Context) {
    private val sharedPreferences: SharedPreferences =
        context.getSharedPreferences("user_settings", Context.MODE_PRIVATE)

    fun saveDarkMode(isDark: Boolean) {
        sharedPreferences.edit().putBoolean("is_dark_mode", isDark).apply()
    }

    fun getDarkMode(): Boolean {
        return sharedPreferences.getBoolean("is_dark_mode", false)
    }
}