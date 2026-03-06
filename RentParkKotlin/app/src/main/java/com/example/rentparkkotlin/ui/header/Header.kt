package com.example.rentparkkotlin.ui.header

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxHeight
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.rentparkkotlin.ui.theme.BlackTheme
import com.example.rentparkkotlin.ui.theme.Orange

// Header commun à toutes les pages de rentpark (au moins les pages admin)

@Composable
fun Header(title: String) {
    Column() {
        Spacer(modifier = Modifier.fillMaxWidth().height(22.dp).background(color = Orange))
        Surface(
            color = BlackTheme,
            shadowElevation = 4.dp // Ajoute une légère ombre pour décoller du contenu
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(64.dp), // Hauteur standard Android pour les TopBar
                verticalAlignment = Alignment.CenterVertically
            ) {
                // Section gauche : Menu
                Box(modifier = Modifier.weight(1f), contentAlignment = Alignment.CenterStart) {
                    MenuBurger()
                }

                // Section centrale : Titre (Réellement centré)
                Box(modifier = Modifier.weight(3f), contentAlignment = Alignment.Center) {
                    Text(
                        text = title,
                        color = Color.White,
                        fontSize = 24.sp, // 35sp était trop gros, ça va tronquer sur les petits écrans
                        fontWeight = FontWeight.ExtraBold,
                        textAlign = TextAlign.Center
                    )
                }

                // Section droite : Vide (pour équilibrer le titre au milieu)
                Spacer(modifier = Modifier.weight(1f))
            }
        }
    }

}

@Preview(showBackground = true)
@Composable
fun HeaderPreview() {
    Header("RentPark")
}