package com.example.rentparkkotlin.ui.header

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.aspectRatio
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp

// Menu burger pour la navigation dans l'application

@Composable
fun MenuBurger() {
    Box(modifier = Modifier.size(75.dp)
        .background(Color.Gray)
        ,contentAlignment = Alignment.Center){
        Column() {
            Barre()
            Spacer(Modifier.height(10.dp))
            Barre()
            Spacer(Modifier.height(10.dp))
            Barre()
        }
    }
}

@Composable
fun Barre(){
    Box(
        modifier = Modifier
            .size(55.dp, 8.dp)
            .background(
                color = Color.Black,
                shape = RoundedCornerShape(50.dp) // L'arrondi est appliqué ici
            )
    )
}

@Preview(showBackground = true)
@Composable
fun MenuBurgerPreview() {
    MenuBurger()
}