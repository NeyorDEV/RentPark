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
import com.example.rentparkkotlin.ui.photoDevice.PhotoDeviceScreen
import com.example.rentparkkotlin.ui.login.LoginScreen
import com.example.rentparkkotlin.ui.theme.RentParkKotlinTheme
import com.example.rentparkkotlin.ui.theme.ThemePrefs
import com.example.rentparkkotlin.data.AuthPrefs // N'oubliez pas cet import !
import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.ui.carsPage.CarListScreen
import com.example.rentparkkotlin.ui.register.RegisterScreen
import com.example.rentparkkotlin.ui.planning.PlanningScreen
import com.example.rentparkkotlin.ui.settings.SettingsPage
import com.example.rentparkkotlin.ui.userPage.UserManagementScreen
import  com.example.rentparkkotlin.ui.contratsPage.ContractsScreen



class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        RetrofitInstance.init(this)

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
                    val startDestination : Any = if (authPrefs.isLoggedIn()) {
                        val role = authPrefs.getRole()
                        // Si c'est un admin ou employé -> Dashboard, sinon -> Accueil Client
                        if (role == "admin" || role == "employe") Routes.DashBoardRoute else Routes.HomeCustomerRoute
                    } else {
                        Routes.LoginRoute
                    }

                    // 3. Configuration du routeur (NavHost)
                    NavHost(navController = navController, startDestination = startDestination) {

                        // Route : Connexion
                        composable<Routes.LoginRoute> {
                            LoginScreen(
                                onLoginSuccess = {
                                    val role = authPrefs.getRole()
                                    val destination = if (role == "admin" || role == "employe") Routes.DashBoardRoute else Routes.HomeCustomerRoute
                                    navController.navigate(destination) {
                                        popUpTo(Routes.LoginRoute) { inclusive = true }
                                    }
                                },
                                onNavigateToRegister = {
                                    navController.navigate(Routes.RegisterRoute)
                                }
                            )
                        }

                        composable<Routes.CarRoute> {
                            CarListScreen()
                        }


                        // Route : Inscription
                        composable<Routes.DashBoardRoute> {
                            RegisterScreen(
                                onNavigateToLogin = {
                                    // Retourne à la page de connexion (dépile la route register)
                                    navController.popBackStack()
                                }
                            )
                        }

                        // Route : Dashboard (Admin / Employé)
                        composable<Routes.DashBoardRoute> {
                            DashboardScreen()
                        }

                        // Route : Accueil Client
                        composable<Routes.HomeCustomerRoute> {
                            RentParkHomeScreen()
                        }
                        composable<Routes.PhotoRoute> {
                            PhotoDeviceScreen(
                                onNavigateBack = { navController.popBackStack() }
                            )
                        }

                        composable<Routes.PlanningRoute> {
                            PlanningScreen()
                        }

                        composable<Routes.UserRoute> {
                            UserManagementScreen()
                        }
                        composable<Routes.ContratRoute> {
                            ContractsScreen()
                        }


                        composable<Routes.SettingsRoute> {
                            SettingsPage(
                                isDarkMode = isDarkMode,
                                onThemeChange = { newValue ->
                                    isDarkMode = newValue
                                    themePrefs.saveDarkMode(newValue)
                                }
                            )
                        }


                    }
                }
            }
        }
    }
}