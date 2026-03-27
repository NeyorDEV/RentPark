package com.example.rentparkkotlin.ui.contratsPage

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.rentparkkotlin.ui.header.Header

// --- 1. MODÈLE DE DONNÉES ---
data class Reservation(
    val id: Int,
    val clientId: Int,
    val vehiculeId: String,
    val debut: String,
    val fin: String
)

class ContratsActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            Surface(modifier = Modifier.fillMaxSize(), color = Color.Black) {
                ContractsScreen()
            }
        }
    }
}

// --- 2. ÉCRAN PRINCIPAL ---
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ContractsScreen() {
    // État de la liste des réservations
    var reservations by remember {
        mutableStateOf(listOf(
            Reservation(36, 50, "TEST22335564", "2026-02-27", "2026-03-01"),
            Reservation(38, 44, "TEST22335564", "2026-02-27", "2026-02-28")
        ))
    }

    // États pour la suppression
    var showDeleteDialog by remember { mutableStateOf(false) }
    var reservationToDelete by remember { mutableStateOf<Reservation?>(null) }

    // État pour afficher/masquer le formulaire et gérer la modification
    var showSheet by remember { mutableStateOf(false) }
    var reservationToEdit by remember { mutableStateOf<Reservation?>(null) } // NOUVEAU: État pour la modification

    val sheetState = rememberModalBottomSheetState()

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0F0F0F))
    ) {
        // NOUVEAU: Réinitialiser reservationToEdit lors d'un ajout
        FilterHeader(onAddClick = {
            reservationToEdit = null
            showSheet = true
        })

        Spacer(modifier = Modifier.height(32.dp))

        LazyColumn(verticalArrangement = Arrangement.spacedBy(16.dp)) {
            items(reservations) { res ->
                ReservationCard(
                    reservation = res,
                    onEditRequest = { // NOUVEAU: Gérer le clic sur "Modifier"
                        reservationToEdit = res
                        showSheet = true
                    },
                    onDeleteRequest = {
                        reservationToDelete = res
                        showDeleteDialog = true
                    }
                )
            }
        }
    }

    // --- DIALOGUE DE CONFIRMATION DE SUPPRESSION ---
    if (showDeleteDialog && reservationToDelete != null) {
        AlertDialog(
            // ... (Code identique pour la suppression)
            onDismissRequest = { showDeleteDialog = false },
            containerColor = Color(0xFF1A1A1A),
            title = { Text("Confirmer la suppression", color = Color.White, fontSize = 18.sp) },
            text = {
                Text(
                    "Voulez-vous vraiment supprimer la réservation n°${reservationToDelete?.id} ?",
                    color = Color.LightGray
                )
            },
            confirmButton = {
                TextButton(
                    onClick = {
                        reservations = reservations.filter { it.id != reservationToDelete?.id }
                        showDeleteDialog = false
                        reservationToDelete = null
                    }
                ) {
                    Text("Supprimer", color = Color(0xFFCB4335), fontWeight = FontWeight.Bold)
                }
            },
            dismissButton = {
                TextButton(onClick = { showDeleteDialog = false }) {
                    Text("Annuler", color = Color.White)
                }
            }
        )
    }

    // --- LE FORMULAIRE (BOTTOM SHEET) ---
    if (showSheet) {
        ModalBottomSheet(
            onDismissRequest = {
                showSheet = false
                reservationToEdit = null // Réinitialiser à la fermeture
            },
            sheetState = sheetState,
            containerColor = Color(0xFF151515),
            dragHandle = { BottomSheetDefaults.DragHandle(color = Color.Gray) }
        ) {
            ReservationForm( // RENOMMÉ ET MIS À JOUR
                initialReservation = reservationToEdit,
                onDismiss = {
                    showSheet = false
                    reservationToEdit = null
                },
                onSave = { savedRes ->
                    if (reservationToEdit == null) {
                        // Ajout
                        reservations = reservations + savedRes
                    } else {
                        // Modification
                        reservations = reservations.map {
                            if (it.id == savedRes.id) savedRes else it
                        }
                    }
                    showSheet = false
                    reservationToEdit = null
                }
            )
        }
    }
}

// --- 3. COMPOSANTS DE L'INTERFACE ---

@Composable
fun FilterHeader(onAddClick: () -> Unit) {
    // ... (Code identique)
    Surface(
        color = Color(0xFF151515),
        shape = RoundedCornerShape(50),
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier.padding(horizontal = 16.dp, vertical = 10.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Text("RECHERCHER :", color = Color.White, fontSize = 10.sp, fontWeight = FontWeight.ExtraBold)

            Surface(color = Color(0xFF222222), shape = RoundedCornerShape(8.dp), modifier = Modifier.weight(1f)) {
                Text("Rechercher...", color = Color.Gray, modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp), fontSize = 12.sp)
            }

            Icon(Icons.Default.Search, contentDescription = null, tint = Color(0xFFE67E22))

            Button(
                onClick = onAddClick,
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF7DCEA0)),
                contentPadding = PaddingValues(horizontal = 16.dp),
                shape = RoundedCornerShape(20.dp),
                modifier = Modifier.height(36.dp)
            ) {
                Text("+ Ajouter", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 12.sp)
            }
        }
    }
}

// NOUVEAU: Le formulaire gère maintenant l'ajout ET la modification
@Composable
fun ReservationForm(
    initialReservation: Reservation?,
    onDismiss: () -> Unit,
    onSave: (Reservation) -> Unit
) {
    // Pré-remplir les champs si on est en mode édition
    var clientId by remember { mutableStateOf(initialReservation?.clientId?.toString() ?: "") }
    var vehiculeId by remember { mutableStateOf(initialReservation?.vehiculeId ?: "") }
    var debut by remember { mutableStateOf(initialReservation?.debut ?: "") }
    var fin by remember { mutableStateOf(initialReservation?.fin ?: "") }

    val isEditing = initialReservation != null

    Column(
        modifier = Modifier.padding(24.dp).navigationBarsPadding(),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {

        Text(
            text = if (isEditing) "Modifier la Réservation" else "Nouvelle Réservation",
            color = Color.White,
            fontSize = 20.sp,
            fontWeight = FontWeight.Bold
        )

        CustomTextField(value = clientId, onValueChange = { clientId = it }, label = "ID Client")
        CustomTextField(value = vehiculeId, onValueChange = { vehiculeId = it }, label = "Véhicule (Immatriculation)")

        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            Box(modifier = Modifier.weight(1f)) {
                CustomTextField(value = debut, onValueChange = { debut = it }, label = "Début (AAAA-MM-JJ)")
            }
            Box(modifier = Modifier.weight(1f)) {
                CustomTextField(value = fin, onValueChange = { fin = it }, label = "Fin (AAAA-MM-JJ)")
            }
        }

        Spacer(modifier = Modifier.height(8.dp))

        Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            OutlinedButton(
                onClick = onDismiss,
                modifier = Modifier.weight(1f),
                border = androidx.compose.foundation.BorderStroke(1.dp, Color.Gray),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("Annuler", color = Color.White)
            }
            Button(
                onClick = {
                    if (clientId.isNotEmpty() && vehiculeId.isNotEmpty()) {
                        // Conserver l'ID existant si édition, sinon générer un nouveau
                        val id = initialReservation?.id ?: (100..999).random()
                        onSave(Reservation(id, clientId.toIntOrNull() ?: 0, vehiculeId, debut, fin))
                    }
                },
                modifier = Modifier.weight(1f),
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF7DCEA0)),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text(if (isEditing) "Mettre à jour" else "Confirmer", color = Color.Black, fontWeight = FontWeight.Bold)
            }
        }
        Spacer(modifier = Modifier.height(24.dp))
    }
}

// ... (CustomTextField reste identique)
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun CustomTextField(value: String, onValueChange: (String) -> Unit, label: String) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label, color = Color.Gray) },
        modifier = Modifier.fillMaxWidth(),
        textStyle = androidx.compose.ui.text.TextStyle(color = Color.White),
        shape = RoundedCornerShape(12.dp)
    )
}

// NOUVEAU: Ajout du paramètre onEditRequest
@Composable
fun ReservationCard(reservation: Reservation, onEditRequest: () -> Unit, onDeleteRequest: () -> Unit) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(containerColor = Color(0xFF181818)),
        shape = RoundedCornerShape(20.dp)
    ) {
        Row(
            modifier = Modifier.padding(20.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text("Reservation n°${reservation.id}", color = Color(0xFFE67E22), fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Spacer(modifier = Modifier.height(8.dp))
                InfoLabel("Client", reservation.clientId.toString())
                InfoLabel("Véhicule", reservation.vehiculeId)
                InfoLabel("Période", "${reservation.debut} au ${reservation.fin}")
            }
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                ActionButton("Modifier", Color(0xFFEBF5FB), Color(0xFF2E86C1), onClick = onEditRequest)
                ActionButton("Supprimer", Color(0xFFFDEDEC), Color(0xFFCB4335), onClick = onDeleteRequest)
            }
        }
    }
}

// ... (InfoLabel et ActionButton restent identiques)
@Composable
fun InfoLabel(label: String, value: String) {
    Text("$label : $value", color = Color.LightGray, fontSize = 13.sp)
}

@Composable
fun ActionButton(text: String, bgColor: Color, textColor: Color, onClick: () -> Unit) {
    Button(
        onClick = onClick,
        colors = ButtonDefaults.buttonColors(containerColor = bgColor),
        shape = RoundedCornerShape(8.dp),
        contentPadding = PaddingValues(horizontal = 12.dp),
        modifier = Modifier.width(100.dp).height(32.dp)
    ) {
        Text(text, color = textColor, fontSize = 12.sp, fontWeight = FontWeight.Bold)
    }
}