package com.example.rentparkkotlin.ui.planning

import android.os.Build
import androidx.annotation.RequiresApi
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
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
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.ui.theme.Orange
import com.example.rentparkkotlin.viewmodel.PlanningViewModel
import java.time.YearMonth
import java.time.format.TextStyle
import java.util.Locale

// Classe pour stocker le contexte de la modale de liste
data class DayListContext(
    val title: String,
    val events: List<Contrat>,
    val color: Color,
    val isDepart: Boolean
)

@RequiresApi(Build.VERSION_CODES.O)
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun PlanningScreen(planningViewModel: PlanningViewModel = viewModel()) {

    val contrats by planningViewModel.contrats.collectAsState()

    // État pour la modale listant les événements spécifiques (départs OU retours)
    var selectedDayList by remember { mutableStateOf<DayListContext?>(null) }

    // État pour la modale des détails complets d'un contrat
    var selectedContrat by remember { mutableStateOf<Contrat?>(null) }

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
                        val currentDateStr = String.format(Locale.US, "%04d-%02d-%02d",
                            currentYearMonth.year, currentYearMonth.monthValue, day)

                        // Filtre sécurisé avec ? == true pour éviter les crashs si dateDebut/Fin est null
                        val contratsDepart = contrats.filter { it.dateDebut?.startsWith(currentDateStr) == true }
                        val contratsRetour = contrats.filter { it.dateFin?.startsWith(currentDateStr) == true }

                        DayCell(
                            day = day,
                            departs = contratsDepart,
                            retours = contratsRetour,
                            onDepartClick = {
                                selectedDayList = DayListContext("Départs", contratsDepart, Orange, true)
                            },
                            onRetourClick = {
                                selectedDayList = DayListContext("Retours", contratsRetour, Color.Blue, false)
                            }
                        )
                    }
                }
            }
        }
    }

    // 1. Modale intermédiaire : Liste filtrée (Départs OU Retours)
    selectedDayList?.let { context ->
        DayEventsListModal(
            context = context,
            onEventClick = { contrat ->
                selectedDayList = null // On ferme la liste
                selectedContrat = contrat // On ouvre les détails du contrat sélectionné
            },
            onDismiss = { selectedDayList = null }
        )
    }

    // 2. Modale finale : Détails du contrat
    selectedContrat?.let { contrat ->
        EventDetailModal(
            contrat = contrat,
            onDismiss = { selectedContrat = null }
        )
    }
}

@Composable
fun DayCell(
    day: Int,
    departs: List<Contrat>,
    retours: List<Contrat>,
    onDepartClick: () -> Unit,
    onRetourClick: () -> Unit
) {
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
            // S'il y a au moins 1 départ, on affiche la pastille Orange "D" classique
            if (departs.isNotEmpty()) {
                EventBadge(letter = "D", color = Orange, onClick = onDepartClick)
            }

            // S'il y a au moins 1 retour, on affiche la pastille Bleue "R" classique
            if (retours.isNotEmpty()) {
                EventBadge(letter = "R", color = Color.Blue, onClick = onRetourClick)
            }
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
            .size(28.dp) // On force une belle taille fixe (tu peux ajuster cette valeur)
            .background(color, CircleShape)
            .clickable(onClick = onClick),
        contentAlignment = Alignment.Center
    ) {
        Text(
            text = letter,
            color = Color.White,
            fontSize = 12.sp, // J'ai remonté un peu la police pour que ce soit lisible
            fontWeight = FontWeight.Bold
        )
    }
}

// ---- MODALE POUR LISTER LES ÉVÈNEMENTS DU JOUR (Départs ou Retours) ---- //
@Composable
fun DayEventsListModal(
    context: DayListContext,
    onEventClick: (Contrat) -> Unit,
    onDismiss: () -> Unit
) {
    Dialog(onDismissRequest = onDismiss) {
        Card(
            modifier = Modifier.fillMaxWidth().padding(16.dp),
            shape = RoundedCornerShape(16.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(modifier = Modifier.padding(20.dp)) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text("${context.title} du jour", fontWeight = FontWeight.Bold, fontSize = 18.sp, color = context.color)
                    IconButton(onClick = onDismiss, modifier = Modifier.size(24.dp)) {
                        Icon(Icons.Default.Close, contentDescription = "Fermer")
                    }
                }

                HorizontalDivider(modifier = Modifier.padding(vertical = 12.dp))

                LazyColumn(
                    modifier = Modifier.heightIn(max = 350.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    items(context.events) { contrat ->
                        val typeLabel = if (context.isDepart) "Départ" else "Retour"

                        Row(
                            modifier = Modifier
                                .fillMaxWidth()
                                .background(Color(0xFFF5F5F5), RoundedCornerShape(8.dp))
                                .clickable { onEventClick(contrat) }
                                .padding(12.dp),
                            verticalAlignment = Alignment.CenterVertically
                        ) {
                            Box(
                                modifier = Modifier.size(12.dp).background(context.color, CircleShape)
                            )
                            Spacer(modifier = Modifier.width(12.dp))
                            Column {
                                Text(text = typeLabel, fontWeight = FontWeight.Bold, color = context.color, fontSize = 14.sp)
                                Text(text = "Contrat N°${contrat.idContrat}", fontSize = 14.sp)
                            }
                            Spacer(modifier = Modifier.weight(1f))
                            Icon(Icons.Default.KeyboardArrowRight, contentDescription = "Voir détails", tint = Color.Gray)
                        }
                    }
                }
            }
        }
    }
}

// ---- MODALE EXISTANTE DES DÉTAILS ---- //
@Composable
fun EventDetailModal(contrat: Contrat, onDismiss: () -> Unit) {
    Dialog(onDismissRequest = onDismiss) {
        Card(
            modifier = Modifier.fillMaxWidth().padding(16.dp),
            shape = RoundedCornerShape(16.dp),
            colors = CardDefaults.cardColors(containerColor = Color.White)
        ) {
            Column(modifier = Modifier.padding(20.dp)) {
                Row(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.SpaceBetween,
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text("Détails réservation", fontWeight = FontWeight.Bold, fontSize = 18.sp)
                    IconButton(onClick = onDismiss, modifier = Modifier.size(24.dp)) {
                        Icon(Icons.Default.Close, contentDescription = "Fermer")
                    }
                }

                HorizontalDivider(modifier = Modifier.padding(vertical = 12.dp))

                val details = listOf(
                    "ID Contrat" to contrat.idContrat.toString(),
                    "Client (ID)" to (contrat.idClient?.toString() ?: "N/A"),
                    "Véhicule" to (contrat.idVehicule ?: "N/A"),
                    "Du" to (contrat.dateDebut?.substringBefore(" ") ?: "N/A"),
                    "Au" to (contrat.dateFin?.substringBefore(" ") ?: "N/A"),
                    "Statut" to (contrat.statut ?: "N/A")
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