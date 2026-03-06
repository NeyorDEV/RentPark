package com.example.rentparkkotlin.ui.carsPage

import androidx.compose.foundation.Image
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.CircularProgressIndicator
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

import coil.compose.AsyncImage
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.viewmodel.CarViewModel

import com.example.rentparkkotlin.ui.header.Header


// 1. Modèle de données


// 2. Ta liste de données (Équivalent de ton $results en PHP)


@Composable
fun CarListScreen(
    viewModel: CarViewModel = androidx.lifecycle.viewmodel.compose.viewModel()
) {
    val voitures = viewModel.voitures
    val isLoading = viewModel.isLoading
    val error = viewModel.error

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

        Spacer(modifier = Modifier.height(16.dp))

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


        when {
            isLoading -> {
                Box(
                    modifier = Modifier.fillMaxSize(),
                    contentAlignment = Alignment.Center
                ) {
                    CircularProgressIndicator()
                }
            }

            error != null -> {
                Text(
                    text = "Erreur : $error",
                    color = Color.Red
                )
            }

            else -> {
                LazyVerticalGrid(
                    columns = GridCells.Fixed(1),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                    modifier = Modifier.fillMaxSize()
                ) {
                    items(voitures) { voiture ->
                        CarCard(voiture)
                    }
                }
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

    val imageUrl = "http://10.0.2.2:9990/${voiture.ImagePath}"

    Box(
        modifier = Modifier
            .height(300.dp)
            .fillMaxWidth()
            .clip(RoundedCornerShape(25.dp))
    ) {

        AsyncImage(
            model = imageUrl,
            contentDescription = null,
            contentScale = ContentScale.Crop,
            modifier = Modifier.fillMaxSize()
        )

        Column(
            modifier = Modifier
                .align(Alignment.BottomCenter)
                .fillMaxWidth()
                .background(Color.White.copy(alpha = 0.85f))
                .padding(12.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Column {
                    Text(
                        text = "${voiture.Marque} ${voiture.Nom}",
                        fontWeight = FontWeight.Bold,
                        fontSize = 18.sp
                    )

                    Text(
                        text = "${voiture.NbPlaces} places • ${voiture.Energie}",
                        fontSize = 13.sp,
                        color = Color.Gray
                    )
                }

                Text(
                    text = "${voiture.Prix} €/j",
                    color = Color(0xFFFF5A19),
                    fontWeight = FontWeight.Bold,
                    fontSize = 18.sp
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