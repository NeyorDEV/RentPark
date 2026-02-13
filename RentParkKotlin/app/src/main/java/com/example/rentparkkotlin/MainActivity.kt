package com.example.rentparkkotlin

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import com.example.rentparkkotlin.ui.dashboard.DashboardScreen
import com.example.rentparkkotlin.ui.theme.RentParkKotlinTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        enableEdgeToEdge()
        setContent {
            RentParkKotlinTheme {
                DashboardScreen()
            }
        }
    }
}
