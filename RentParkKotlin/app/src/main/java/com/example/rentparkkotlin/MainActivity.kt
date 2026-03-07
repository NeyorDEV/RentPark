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
import com.example.rentparkkotlin.ui.dashboard.DashboardScreen
import com.example.rentparkkotlin.ui.homeCustomer.RentParkHomeScreen
import com.example.rentparkkotlin.ui.login.LoginScreen
import com.example.rentparkkotlin.ui.theme.RentParkKotlinTheme
import com.example.rentparkkotlin.ui.theme.ThemePrefs
import com.example.rentparkkotlin.data.AuthPrefs
import com.example.rentparkkotlin.ui.register.RegisterScreen
import com.example.rentparkkotlin.ui.carsPage.CarListScreen

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val themePrefs = ThemePrefs(this)
        val authPrefs = AuthPrefs(this)

        setContent {
            var isDarkMode by remember { mutableStateOf(themePrefs.getDarkMode()) }

            RentParkKotlinTheme(darkTheme = isDarkMode) {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    val navController = rememberNavController()

                    // Logic d'origine :
                    val startDestination = if (authPrefs.isLoggedIn()) {
                        val role = authPrefs.getRole()
                        if (role == "admin" || role == "employe") "dashboard" else "homeCustomer"
                    } else {
                        "login"
                    }

                    NavHost(navController = navController, startDestination = startDestination) {

                        // Route : Liste des voitures (Page de Test actuelle)
                        composable("cars") {
                            CarListScreen()
                        }

                        // Route : Connexion
                        composable("login") {
                            LoginScreen(
                                onLoginSuccess = {
                                    val role = authPrefs.getRole()
                                    val destination = if (role == "admin" || role == "employe") "dashboard" else "homeCustomer"
                                    navController.navigate(destination) {
                                        popUpTo("login") { inclusive = true }
                                    }
                                },
                                onNavigateToRegister = {
                                    navController.navigate("register")
                                }
                            )
                        }

                        // Route : Inscription
                        composable("register") {
                            RegisterScreen(
                                onNavigateToLogin = {
                                    navController.popBackStack()
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
                    }
                }
            }
        }
    }
}