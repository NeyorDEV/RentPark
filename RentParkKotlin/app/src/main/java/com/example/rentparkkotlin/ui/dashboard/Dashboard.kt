package com.example.rentparkkotlin.ui.dashboard

import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.CenterAlignedTopAppBar
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.shadow
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.PaintingStyle.Companion.Stroke
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.sp

// Modèle simple pour les cartes du dashboard
data class DashboardCard(val title: String, val items: List<String> = emptyList())

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen() {

    // Exemple de contenu
    val cards = listOf(
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Voiture la plus louée", listOf("BMW Série 3", "Louée 1 fois")),
        DashboardCard("Revenus mensuels", listOf("360 000€")),
        DashboardCard("Rappels", listOf("Contrôle technique – BMW Série 3", "Contrôle technique – BMW Série 3")),
        DashboardCard("Planning Proche", listOf("13/02/2026 – Location : BMW Série 3", "14/02/2026 – Retour : BMW Série 3"))
    )

    Scaffold(
        topBar = {
            CenterAlignedTopAppBar(
                title = { Text("Tableau de bord") },
                navigationIcon = { Text("retour") }
            )
        }
    ) { innerPadding ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(innerPadding)
                .padding(horizontal = 30.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            items(cards) { card ->
                Card(
                    modifier = Modifier.size(300.dp),
                    shape = RoundedCornerShape(16.dp),
                    elevation = CardDefaults.cardElevation(8.dp)
                ) {
                    Column(
                        modifier = Modifier
                            .fillMaxSize()
                            .background(Color.White.copy(alpha = 0.9f))
                            .padding(16.dp),
                        verticalArrangement = Arrangement.Top,
                        horizontalAlignment = Alignment.CenterHorizontally
                    ) {
                        Text(text = card.title)
                        Spacer(modifier = Modifier.height(16.dp))
                        if (card.items.size == 1) {
                            CircularIndicator(number = card.items.first())
                        } else {
                            card.items.forEach { itemText ->
                                Text(text = itemText)
                            }
                        }
                    }
                }

            }
        }
    }
}

@Composable
fun CircularIndicator(
    number: String,
    size: Int = 200,
    color: Color = Color(0xFFFF5722),
    strokeWidth: Float = 4f
) {
    Box(
        contentAlignment = Alignment.Center,
        modifier = Modifier.size(size.dp)
    ) {
        Canvas(modifier = Modifier.fillMaxSize()) {
            val radius = size - strokeWidth  // pour que le contour ne soit pas coupé

            // Cercle uniquement en contour
            drawCircle(
                color = color,
                radius = radius,
                style = Stroke(width = strokeWidth)
            )
        }

        // Texte centré
        Text(
            text = number,
            fontSize = (size / 6).sp,
            fontWeight = FontWeight.Bold
        )
    }
}



@Preview(showBackground = true)
@Composable
fun DashboardScreenPreview() {
    DashboardScreen()
}

