package com.example.rentparkkotlin

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController

// Imports de vos pages
import com.example.rentparkkotlin.ui.dashboard.DashboardScreen
import com.example.rentparkkotlin.ui.homeCustomer.RentParkHomeScreen
import com.example.rentparkkotlin.ui.login.LoginScreen
import com.example.rentparkkotlin.ui.theme.RentParkKotlinTheme
import com.example.rentparkkotlin.ui.theme.ThemePrefs
import com.example.rentparkkotlin.data.AuthPrefs // N'oubliez pas cet import !

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val themePrefs = ThemePrefs(this)
        val authPrefs = AuthPrefs(this) // 1. Initialisation des préférences d'authentification

        setContent {
            var isDarkMode by remember { mutableStateOf(themePrefs.getDarkMode()) }

            RentParkKotlinTheme(darkTheme = isDarkMode) {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    val navController = rememberNavController()

                    // 2. LOGIQUE D'AUTO-CONNEXION
                    // On choisit la page de départ selon l'état de connexion et le rôle
                    val startDestination = if (authPrefs.isLoggedIn()) {
                        val role = authPrefs.getRole()
                        // Si c'est un admin ou employé -> Dashboard, sinon -> Accueil Client
                        if (role == "admin" || role == "employe") "dashboard" else "homeCustomer"
                    } else {
                        "login"
                    }

                    // 3. Configuration du routeur (NavHost)
                    NavHost(navController = navController, startDestination = startDestination) {

                        // Route : Connexion
                        composable("login") {
                            LoginScreen(
                                onLoginSuccess = {
                                    // Après connexion, on lit le rôle et on redirige
                                    val role = authPrefs.getRole()
                                    val destination = if (role == "admin" || role == "employe") "dashboard" else "homeCustomer"

                                    navController.navigate(destination) {
                                        // On vide l'historique pour ne pas revenir sur le login avec la touche retour
                                        popUpTo("login") { inclusive = true }
                                    }
                                }
                            )
                        }

                        // Route : Dashboard (Admin / Employé)
                        composable("dashboard") {
                            DashboardScreen()
                        }

                        // Route : Accueil Client
                        composable("homeCustomer") {
                            RentParkHomeScreen()
                        }

                        // Ajoutez vos autres routes ici plus tard
                        // composable("cars") { CarListScreen() }
                        // composable("planning") { PlanningScreen() }
                        // composable("settings") { SettingsPage(...) }

                        //SettingsPage(
                        //    isDarkMode = isDarkMode,
                        //    onThemeChange = { newValue ->
                        //        isDarkMode = newValue
                        //        themePrefs.saveDarkMode(newValue)
                        //    }
                        //)
                    }
                }
            }
        }
    }
}