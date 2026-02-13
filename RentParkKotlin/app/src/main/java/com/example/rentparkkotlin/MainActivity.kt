package com.example.rentparkkotlin

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.tooling.preview.Preview
import com.example.rentparkkotlin.ui.homeCustomer.RentParkScreen
import com.example.rentparkkotlin.ui.theme.RentParkKotlinTheme

class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        // Supprime les bordures système pour un look "Full Screen" (Optionnel)
        // WindowCompat.setDecorFitsSystemWindows(window, false)

        setContent {
            // On applique le thème de l'application
            MaterialTheme {
                // Surface occupe tout l'écran
                Surface(color = MaterialTheme.colorScheme.background) {
                    // On appelle la fonction Composable de ton design
                    RentParkScreen()
                }
            }
        }
    }
}

@Composable
fun Greeting(name: String, modifier: Modifier = Modifier) {
    Text(
        text = "Hello $name!",
        modifier = modifier
    )
}

@Preview(showBackground = true)
@Composable
fun GreetingPreview() {
    RentParkKotlinTheme {
        Greeting("Android")
    }
}