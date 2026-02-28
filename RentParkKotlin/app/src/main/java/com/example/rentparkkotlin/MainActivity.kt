package com.example.rentparkkotlin

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import com.example.rentparkkotlin.ui.dashboard.DashboardScreen
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.tooling.preview.Preview
import com.example.rentparkkotlin.ui.carsPage.CarListScreen
import com.example.rentparkkotlin.ui.homeCustomer.RentParkHomeScreen
import com.example.rentparkkotlin.ui.userPage.UserManagementScreen
import com.example.rentparkkotlin.ui.settings.SettingsPage

import com.example.rentparkkotlin.ui.theme.RentParkKotlinTheme
import com.example.rentparkkotlin.ui.theme.ThemePrefs

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
                    //SettingsPage(
                    //    isDarkMode = isDarkMode,
                    //    onThemeChange = { newValue ->
                    //        isDarkMode = newValue
                    //        themePrefs.saveDarkMode(newValue)
                    //    }
                    //)
                    UserManagementScreen()
                }
            }
        }
    }
}
