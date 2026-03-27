package com.example.rentparkkotlin.ui.dashboard

import androidx.compose.animation.core.Animatable
import androidx.compose.animation.core.LinearEasing
import androidx.compose.animation.core.tween
import androidx.compose.foundation.Canvas
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.drawscope.Stroke
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.rentparkkotlin.ui.header.Header
import com.example.rentparkkotlin.viewmodel.DashboardViewModel

data class DashboardCard(val title: String, val items: List<String> = emptyList())

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DashboardScreen(viewModel: DashboardViewModel = viewModel()) {

    val cards = viewModel.cards
    val isLoading = viewModel.isLoading

    // État pour afficher ou cacher la modale d'ajout de rappel
    var showAddRappelDialog by remember { mutableStateOf(false) }

    Scaffold(
        // Ajout du bouton flottant en bas à droite
        floatingActionButton = {
            FloatingActionButton(
                onClick = { showAddRappelDialog = true },
                containerColor = Color(0xFFFF5722),
                contentColor = Color.White
            ) {
                Icon(Icons.Default.Add, contentDescription = "Ajouter un rappel")
            }
        }
    ) { innerPadding ->

        Column(modifier = Modifier.fillMaxSize()) {
            Header("Tableau de bord", onMenuClick = {})

            if (isLoading) {
                Box(modifier = Modifier.fillMaxSize().padding(innerPadding), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = Color(0xFFFF5722))
                }
            } else {
                LazyColumn(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(top = 16.dp)
                        .padding(innerPadding)
                        .padding(horizontal = 30.dp),
                    verticalArrangement = Arrangement.spacedBy(16.dp),
                    horizontalAlignment = Alignment.CenterHorizontally
                ) {
                    items(cards) { card ->
                        Card(modifier = Modifier
                            .fillMaxWidth()
                            .heightIn(min = 150.dp),
                            shape = RoundedCornerShape(16.dp),
                            elevation = CardDefaults.cardElevation(8.dp)
                        ) {
                            Column(
                                modifier = Modifier
                                    .fillMaxSize()
                                    .background(Color.White.copy(alpha = 0.7f))
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
                                    card.items.size == 1 && card.title in listOf("Nombre d'utilisateurs", "Revenus mensuels") -> {
                                        val numberValue = card.items.first().replace("€", "")
                                        CircularIndicatorAnimated(
                                            number = card.items.first(),
                                            maxValue = if (card.title == "Revenus mensuels") 300000f else 100f,
                                            actualValue = numberValue.toFloatOrNull() ?: 0f
                                        )
                                    }

                                    card.title in listOf("Rappels", "Planning Proche") -> {
                                        Box(
                                            modifier = Modifier.heightIn(max = 120.dp)
                                        ) {
                                            LazyColumn(
                                                modifier = Modifier.fillMaxWidth(),
                                                verticalArrangement = Arrangement.spacedBy(4.dp)
                                            ) {
                                                items(card.items) { item ->
                                                    Text(
                                                        text = item,
                                                        fontSize = 14.sp,
                                                        modifier = Modifier
                                                            .fillMaxWidth()
                                                            .background(
                                                                if (item.contains("✅")) Color(0xFFE8F5E9) else Color(0xFFFFF3E0),
                                                                shape = RoundedCornerShape(8.dp)
                                                            )
                                                            .padding(8.dp)
                                                    )
                                                }
                                            }
                                        }
                                    }

                                    else -> {
                                        card.items.forEach { item ->
                                            Text(
                                                text = item,
                                                fontSize = 16.sp,
                                                modifier = Modifier.padding(vertical = 2.dp)
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
    }

    // Affichage de la modale si l'état est à true
    if (showAddRappelDialog) {
        AddRappelDialog(
            onDismiss = { showAddRappelDialog = false },
            onConfirm = { titre, description, date ->
                viewModel.addRappel(titre, description, date)
                showAddRappelDialog = false
            }
        )
    }
}

// --- NOUVEAU : Composant de la modale d'ajout ---
@Composable
fun AddRappelDialog(
    onDismiss: () -> Unit,
    onConfirm: (String, String, String) -> Unit
) {
    var titre by remember { mutableStateOf("") }
    var description by remember { mutableStateOf("") }
    var date by remember { mutableStateOf("") } // Format AAAA-MM-JJ attendu

    AlertDialog(
        onDismissRequest = onDismiss,
        title = { Text("Nouveau Rappel", fontWeight = FontWeight.Bold) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                OutlinedTextField(
                    value = titre,
                    onValueChange = { titre = it },
                    label = { Text("Titre") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = description,
                    onValueChange = { description = it },
                    label = { Text("Description") },
                    modifier = Modifier.fillMaxWidth()
                )
                OutlinedTextField(
                    value = date,
                    onValueChange = { date = it },
                    label = { Text("Date (AAAA-MM-JJ)") },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth()
                )
            }
        },
        confirmButton = {
            Button(
                onClick = { onConfirm(titre, description, date) },
                // Le bouton n'est cliquable que si tous les champs sont remplis
                enabled = titre.isNotBlank() && description.isNotBlank() && date.isNotBlank(),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFF5722))
            ) {
                Text("Ajouter", color = Color.White)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("Annuler", color = Color.Gray)
            }
        }
    )
}

@Composable
fun CircularIndicatorAnimated(
    number: String,
    maxValue: Float,
    actualValue: Float,
    size: Int = 160,
    color: Color = Color(0xFFFF5722),
    strokeWidth: Float = 10f,
    duration: Int = 1500
) {
    val progress = remember { Animatable(0f) }

    val targetProgress = (actualValue / maxValue).coerceIn(0f, 1f)

    LaunchedEffect(Unit) {
        progress.animateTo(
            targetValue = targetProgress,
            animationSpec = tween(durationMillis = duration, easing = LinearEasing)
        )
    }

    Box(
        contentAlignment = Alignment.Center,
        modifier = Modifier.size(size.dp)
    ) {
        Canvas(modifier = Modifier.fillMaxSize()) {
            drawArc(
                color = Color.LightGray.copy(alpha = 0.3f),
                startAngle = -90f,
                sweepAngle = 360f,
                useCenter = false,
                style = Stroke(width = strokeWidth)
            )
            drawArc(
                color = color,
                startAngle = -90f,
                sweepAngle = 360f * progress.value,
                useCenter = false,
                style = Stroke(width = strokeWidth)
            )
        }
        Text(
            text = number,
            fontSize = (size / 6f).sp,
            fontWeight = FontWeight.Bold,
        )
    }
}