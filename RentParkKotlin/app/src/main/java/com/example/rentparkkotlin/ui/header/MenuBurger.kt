package com.example.rentparkkotlin.ui.header

import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp

import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Menu
import androidx.compose.material3.*


// Menu burger pour la navigation dans l'application

@Composable
fun MenuBurger() {
    IconButton(
        onClick = { /* Action Menu */ },
        modifier = Modifier.padding(start = 8.dp)
    ) {
        Icon(
            imageVector = Icons.Default.Menu,
            contentDescription = "Menu",
            tint = Color.White,
            modifier = Modifier.size(32.dp) // Taille beaucoup plus équilibrée
        )
    }
}

@Preview(showBackground = true)
@Composable
fun MenuBurgerPreview() {
    MenuBurger()
}