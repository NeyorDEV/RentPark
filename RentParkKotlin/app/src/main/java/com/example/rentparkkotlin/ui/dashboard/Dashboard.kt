package com.example.rentparkkotlin.ui.dashboard

import androidx.compose.animation.core.Animatable
import androidx.compose.animation.core.LinearEasing
import androidx.compose.animation.core.tween
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.Button
import androidx.compose.material3.CenterAlignedTopAppBar
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Text
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.remember
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
import com.example.rentparkkotlin.ui.homeCustomer.RentParkScreen

// Modèle simple pour les cartes du dashboard
data class DashboardCard(val title: String, val items: List<String> = emptyList())

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen() {

    val cards = listOf(
        DashboardCard("Nombre d'utilisateurs", listOf("11")),
        DashboardCard("Voiture la plus louée", listOf("BMW Série 3", "Louée 1 fois")),
        DashboardCard("Revenus mensuels", listOf("360 000€")),
        DashboardCard(
            "Rappels",
            listOf(
                "Contrôle technique – BMW Série 3",
                "Assurance – BMW Série 3",
                "Vidange – BMW Série 3",
                "Pneus – BMW Série 3"
            )
        ),
        DashboardCard(
            "Planning Proche",
            listOf(
                "13/02/2026 – Location : BMW Série 3",
                "14/02/2026 – Retour : BMW Série 3",
                "15/02/2026 – Location : Audi A3"
            )
        )
    )

    Scaffold(
        topBar = { CenterAlignedTopAppBar(title = { Text("Tableau de bord") }) }
    ) { innerPadding ->
        LazyColumn(
            modifier = Modifier
                .fillMaxSize()
                .padding(top = 30.dp)
                .padding(innerPadding)
                .padding(horizontal = 30.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            items(cards) { card ->
                Card(
                    modifier = Modifier
                        .fillMaxWidth()
                        .heightIn(min = 150.dp),
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
                        Text(
                            text = card.title,
                            fontSize = 20.sp,
                            fontWeight = FontWeight.Bold
                        )
                        Spacer(modifier = Modifier.height(16.dp))

                        when {
                            card.items.size == 1 -> {
                                CircularIndicatorAnimated(number = card.items.first())
                            }

                            // Seules certaines cartes utilisent la LazyColumn interne
                            card.title in listOf("Rappels", "Planning Proche") -> {
                                Box(
                                    modifier = Modifier
                                        .heightIn(max = 100.dp)
                                ) {
                                    LazyColumn(
                                        modifier = Modifier.fillMaxWidth(),
                                        verticalArrangement = Arrangement.spacedBy(4.dp)
                                    ) {
                                        items(card.items) { item ->
                                            Text(
                                                text = item,
                                                fontSize = 16.sp,
                                                modifier = Modifier
                                                    .fillMaxWidth()
                                                    .background(
                                                        Color(0xFFFFF3E0),
                                                        shape = RoundedCornerShape(8.dp)
                                                    )
                                                    .padding(8.dp)
                                            )
                                        }
                                    }
                                }
                            }

                            else -> {
                                // Carte simple avec plusieurs items
                                card.items.forEach { item ->
                                    Text(
                                        text = item,
                                        fontSize = 16.sp
                                    )
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}



@Composable
fun CircularIndicatorAnimated(
    number: String,
    size: Int = 200,
    color: Color = Color(0xFFFF5722),
    strokeWidth: Float = 6f,
    duration: Int = 1500  // durée de l'animation en ms
) {
    // Animatable pour contrôler le pourcentage du cercle
    val progress = remember { Animatable(0f) }

    // Lancer l'animation au composable
    LaunchedEffect(Unit) {
        progress.animateTo(
            targetValue = 1f,
            animationSpec = tween(durationMillis = duration, easing = LinearEasing)
        )
    }

    Box(
        contentAlignment = Alignment.Center,
        modifier = Modifier.size(size.dp)
    ) {
        Canvas(modifier = Modifier.fillMaxSize()) {
            val radius = (size / 2f) - (strokeWidth / 2f)

            // Cercle “progressif” : on utilise sweepAngle en degrés
            drawArc(
                color = color,
                startAngle = -90f,
                sweepAngle = 360f * progress.value, // animation du cercle
                useCenter = false,
                style = Stroke(width = strokeWidth)
            )
        }

        // Texte centré
        Text(
            text = number,
            fontSize = (size/8f).sp,
            fontWeight = androidx.compose.ui.text.font.FontWeight.Bold,
        )
    }
}



@Preview(showBackground = true)
@Composable
fun DashboardScreenPreview() {
    DashboardScreen()
}

