package com.example.rentparkkotlin

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import com.example.rentparkkotlin.ui.carsPage.CarListScreen
import com.example.rentparkkotlin.ui.contratsPage.ContratsScreen
import com.example.rentparkkotlin.ui.header.Header
import com.example.rentparkkotlin.ui.dashboard.DashboardScreen
import com.example.rentparkkotlin.ui.homeCustomer.RentParkHomeScreen
import com.example.rentparkkotlin.ui.planning.PlanningScreen


import com.example.rentparkkotlin.ui.theme.RentParkKotlinTheme
import com.example.rentparkkotlin.ui.theme.ThemePrefs
import com.example.rentparkkotlin.ui.userPage.UserManagementScreen

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        val themePrefs = ThemePrefs(this)

        setContent {
            var isDarkMode by remember { mutableStateOf(themePrefs.getDarkMode()) }

            RentParkKotlinTheme(darkTheme = isDarkMode) {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {

                    //Mettez ici la fonction pour afficher votre page et commentez les autres
                    //Ne rien casser svp sinon la matraque du policier


                    //CarListScreen()
                    //ContratsScreen()
                    //DashboardScreen()
                    //RentParkHomeScreen()
                    //PlanningScreen()
                    //UserManagementScreen()
                    //SettingsPage(
                    //    isDarkMode = isDarkMode,
                    //    onThemeChange = { newValue ->
                    //        isDarkMode = newValue
                    //        themePrefs.saveDarkMode(newValue)
                    //    }
                    //)

                    CarListScreen()
                    //UserManagementScreen()
                    //ContratsScreen()

                }
            }
        }
    }
}
