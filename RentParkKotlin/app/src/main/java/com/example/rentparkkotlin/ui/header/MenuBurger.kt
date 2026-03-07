package com.example.rentparkkotlin.ui.header

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
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

@Composable
fun Barre() {
    Box(
        modifier = Modifier
            .size(width = 28.dp, height = 3.dp)
            .background(
                color = Color.White,
                shape = RoundedCornerShape(50.dp)
            )
    )
}

@Composable
fun MenuBurger() {
    Column(
        modifier = Modifier.size(32.dp),
        horizontalAlignment = Alignment.CenterHorizontally,
        verticalArrangement = Arrangement.Center
    ) {
        Barre()
        Spacer(Modifier.height(5.dp))
        Barre()
        Spacer(Modifier.height(5.dp))
        Barre()
    }
}

@Preview(showBackground = true)
@Composable
fun MenuBurgerPreview() {
    MenuBurger()
}