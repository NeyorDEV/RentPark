package com.example.rentparkkotlin.ui.carsPage

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.painterResource
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.rentparkkotlin.ui.header.Header

// 1. Modèle de données
data class Voiture(
    val id: String,
    val nom: String,
    val marque: String,
    val prix: Double,
    val boite: String,
    val energie: String,
    val imageRes: Int
)

// 2. Ta liste de données (Équivalent de ton $results en PHP)
val listeDeVoitures = listOf(
    Voiture("1", "Clio 5", "Renault", 45.0, "Manuelle", "Essence", android.R.drawable.ic_menu_gallery),
    Voiture("2", "Model 3", "Tesla", 120.0, "Auto", "Élec", android.R.drawable.ic_menu_gallery),
    Voiture("3", "208", "Peugeot", 50.0, "Manuelle", "Diesel", android.R.drawable.ic_menu_gallery),
    Voiture("4", "A3", "Audi", 85.0, "Auto", "Hybride", android.R.drawable.ic_menu_gallery),
    Voiture("5", "Golf 8", "VW", 70.0, "Auto", "Essence", android.R.drawable.ic_menu_gallery),
    Voiture("6", "Yaris", "Toyota", 40.0, "Auto", "Hybride", android.R.drawable.ic_menu_gallery),
    Voiture("7", "Duster", "Dacia", 35.0, "Manuelle", "GPL", android.R.drawable.ic_menu_gallery),
    Voiture("8", "911", "Porsche", 350.0, "Auto", "Essence", android.R.drawable.ic_menu_gallery)
)

@Composable
fun CarListScreen() {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF8F9FA))
    ) {
        Header("Liste des voitures")
        // 1. Barre de Recherche
        Box(
            modifier = Modifier
                .fillMaxWidth()
                .clip(CircleShape)
                .background(Color.White)
                .padding(15.dp)
        ) {
            Text("Rechercher une voiture...", color = Color.Gray)
        }

        Spacer(modifier = Modifier.height(12.dp))

        // 2. Emplacement pour filtrer (Non fonctionnel, juste visuel)
        Row(
            modifier = Modifier.fillMaxWidth(),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            // On réutilise une version légèrement modifiée du badge pour les filtres
            FilterBadge(text = "Prix", icon = "€")
            FilterBadge(text = "Boîte", icon = "⚙️")
            FilterBadge(text = "Énergie", icon = "⚡")
            FilterBadge(text = "Plus", icon = "＋")
        }

        Spacer(modifier = Modifier.height(20.dp))

        // 3. Liste verticale
        LazyVerticalGrid(
            columns = GridCells.Fixed(1),
            verticalArrangement = Arrangement.spacedBy(12.dp),
            modifier = Modifier.fillMaxSize()
        ) {
            items(listeDeVoitures) { voiture ->
                CarCard(voiture)
            }
        }
    }
}

@Composable
fun FilterBadge(text: String, icon: String) {
    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(12.dp))
            .background(Color.White) // Fond blanc pour ressortir sur le gris clair
            .padding(horizontal = 12.dp, vertical = 8.dp)
    ) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            Text(text = "$icon ", fontSize = 12.sp)
            Text(
                text = text,
                fontSize = 13.sp,
                fontWeight = FontWeight.Medium,
                color = Color.Black
            )
        }
    }
}

@Composable
fun CarCard(voiture: Voiture) {
    Box(
        modifier = Modifier
            .height(300.dp) // Hauteur réduite (moins de vertical)
            .fillMaxWidth()
            .clip(RoundedCornerShape(25.dp))
            .background(Color.LightGray)
    ) {
        // Image de fond
        Image(
            painter = painterResource(id = voiture.imageRes),
            contentDescription = null,
            contentScale = ContentScale.Crop,
            modifier = Modifier.fillMaxSize()
        )

        // Bloc Info Glass ajusté pour le format horizontal
        Column(
            modifier = Modifier
                .align(Alignment.BottomCenter)
                .fillMaxWidth()
                .background(Color.White.copy(alpha = 0.8f))
                .padding(12.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column {
                    Text(
                        text = "${voiture.marque} ${voiture.nom}",
                        style = TextStyle(fontWeight = FontWeight.Bold, fontSize = 18.sp),
                        color = Color.Black
                    )
                    Row(horizontalArrangement = Arrangement.spacedBy(6.dp)) {
                        Badge(voiture.boite)
                        Badge(voiture.energie)
                    }
                }

                Text(
                    text = "${voiture.prix} €/j",
                    color = Color(0xFFFF5A19),
                    style = TextStyle(fontWeight = FontWeight.ExtraBold, fontSize = 20.sp)
                )
            }
        }
    }
}

@Composable
fun Badge(text: String) {
    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(8.dp))
            .background(Color.Black.copy(alpha = 0.1f))
            .padding(horizontal = 8.dp, vertical = 4.dp)
    ) {
        Text(text = text, fontSize = 10.sp, fontWeight = FontWeight.Bold, color = Color.DarkGray)
    }
}