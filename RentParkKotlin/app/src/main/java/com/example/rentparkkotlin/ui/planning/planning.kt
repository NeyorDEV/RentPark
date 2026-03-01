package com.example.rentparkkotlin.ui.planning

import android.os.Build
import androidx.annotation.RequiresApi
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import androidx.compose.ui.text.style.TextAlign
import com.example.rentparkkotlin.ui.theme.Orange
import java.time.YearMonth
import java.time.format.TextStyle
import java.util.Locale

@RequiresApi(Build.VERSION_CODES.O)
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PlanningScreen() {
    var showModal by remember { mutableStateOf(false) }

    var currentYearMonth by remember { mutableStateOf(YearMonth.now()) }

    val monthTitle = remember(currentYearMonth) {
        val monthName = currentYearMonth.month.getDisplayName(TextStyle.FULL, Locale.FRANCE)
            .replaceFirstChar { if (it.isLowerCase()) it.titlecase(Locale.getDefault()) else it.toString() }
        "$monthName ${currentYearMonth.year}"
    }

    Scaffold(
        topBar = {
            CenterAlignedTopAppBar(
                title = { Text("Planning Mensuel", fontWeight = FontWeight.Bold) },
                colors = TopAppBarDefaults.centerAlignedTopAppBarColors(
                    containerColor = Color(0xFFF5F5F5)
                )
            )
        }
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .padding(paddingValues)
                .fillMaxSize()
                .background(Color(0xFFFDFDFD))
                .padding(16.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                TextButton(onClick = { currentYearMonth = currentYearMonth.minusMonths(1) }) {
                    Text("← Précédent", color = Color.Gray)
                }

                Text(monthTitle, fontWeight = FontWeight.Bold, fontSize = 18.sp)

                TextButton(onClick = { currentYearMonth = currentYearMonth.plusMonths(1) }) {
                    Text("Suivant →", color = Color.Gray)
                }
            }

            Spacer(modifier = Modifier.height(16.dp))

            Card(
                elevation = CardDefaults.cardElevation(4.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                shape = RoundedCornerShape(12.dp)
            ) {
                val daysInMonth = currentYearMonth.lengthOfMonth()

                LazyVerticalGrid(
                    columns = GridCells.Fixed(7),
                    modifier = Modifier.padding(8.dp),
                    contentPadding = PaddingValues(4.dp)
                ) {
                    items((1..daysInMonth).toList()) { day ->
                        DayCell(day = day, onClick = { showModal = true })
                    }
                }
            }
        }
    }

    if (showModal) {
        EventDetailModal(onDismiss = { showModal = false })
    }
}

@Composable
fun DayCell(day: Int, onClick: () -> Unit) {
    Column(
        modifier = Modifier
            .aspectRatio(0.4f)
            .padding(2.dp)
            .background(Color(0xFFF9F9F9), RoundedCornerShape(4.dp))
            .padding(4.dp),
        horizontalAlignment = Alignment.CenterHorizontally
    ) {
        Text(text = day.toString(), fontSize = 12.sp, fontWeight = FontWeight.Medium)

        Spacer(modifier = Modifier.height(8.dp))

        Column(
            verticalArrangement = Arrangement.spacedBy(4.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            EventBadge(letter = "D", color = Orange, onClick = onClick)
            EventBadge(letter = "R", color = Color.Blue, onClick = onClick)
        }
    }
}

@Composable
fun EventBadge(
    letter: String,
    color: Color,
    onClick: () -> Unit,
    modifier: Modifier = Modifier
) {
    Box(
        modifier = modifier
            .sizeIn(maxWidth = 24.dp, maxHeight = 24.dp)
            .aspectRatio(1f)
            .background(color, CircleShape)
            .clickable(onClick = onClick),
        contentAlignment = Alignment.Center
    ) {
        Text(
            text = letter,
            color = Color.White,
            fontSize = 10.sp,
            fontWeight = FontWeight.Bold
        )
    }
}

@Composable
fun EventDetailModal(onDismiss: () -> Unit) {
    Dialog(onDismissRequest = onDismiss) {
        Card(
            modifier = Modifier.fillMaxWidth().padding(16.dp),
            shape = RoundedCornerShape(16.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(modifier = Modifier.padding(20.dp)) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween
                ) {
                    Text("Détails de la réservation", fontWeight = FontWeight.Bold, fontSize = 18.sp)
                    IconButton(onClick = onDismiss, modifier = Modifier.size(24.dp)) {
                        Icon(Icons.Default.Close, contentDescription = "Fermer")
                    }
                }

                HorizontalDivider(modifier = Modifier.padding(vertical = 12.dp))

                val details = listOf(
                    "ID Contrat" to "12345",
                    "Client" to "Jean Dupont",
                    "Véhicule" to "Tesla Model 3",
                    "Du" to "01/01/2024",
                    "Au" to "05/01/2024",
                    "Statut" to "Confirmé"
                )

                details.forEach { (label, value) ->
                    Row(modifier = Modifier.padding(vertical = 4.dp)) {
                        Text("$label : ", fontWeight = FontWeight.Bold)
                        Text(value)
                    }
                }
            }
        }
    }
}