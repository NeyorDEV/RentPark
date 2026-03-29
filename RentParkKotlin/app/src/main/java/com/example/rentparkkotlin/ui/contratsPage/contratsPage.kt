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
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.viewmodel.ContratViewModel

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

// --- ÉCRAN PRINCIPAL ---
@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ContractsScreen(viewModel: ContratViewModel = viewModel()) { // Injection du ViewModel ici

    // Récupération des données depuis l'API
    val contrats = viewModel.contrats
    val isLoading = viewModel.isLoading

    // États pour la suppression et l'édition
    var showDeleteDialog by remember { mutableStateOf(false) }
    var contratToDelete by remember { mutableStateOf<Contrat?>(null) }

    var showSheet by remember { mutableStateOf(false) }
    var contratToEdit by remember { mutableStateOf<Contrat?>(null) }

    val sheetState = rememberModalBottomSheetState()

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0F0F0F))
    ) {
        FilterHeader(onAddClick = {
            contratToEdit = null
            showSheet = true
        })

        Spacer(modifier = Modifier.height(32.dp))

        if (isLoading) {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = Color(0xFFE67E22))
            }
        } else {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(16.dp)) {
                items(contrats) { contrat ->
                    ContratCard(
                        contrat = contrat,
                        onEditRequest = {
                            contratToEdit = contrat
                            showSheet = true
                        },
                        onDeleteRequest = {
                            contratToDelete = contrat
                            showDeleteDialog = true
                        }
                    )
                }
            }
        }
    }

    // --- DIALOGUE DE CONFIRMATION DE SUPPRESSION ---
    if (showDeleteDialog && contratToDelete != null) {
        AlertDialog(
            onDismissRequest = { showDeleteDialog = false },
            containerColor = Color(0xFF1A1A1A),
            title = { Text("Confirmer la suppression", color = Color.White, fontSize = 18.sp) },
            text = {
                Text(
                    "Voulez-vous vraiment supprimer le contrat n°${contratToDelete?.idContrat} ?",
                    color = Color.LightGray
                )
            },
            confirmButton = {
                TextButton(
                    onClick = {
                        contratToDelete?.let { viewModel.deleteContrat(it.idContrat) }
                        showDeleteDialog = false
                        contratToDelete = null
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
                contratToEdit = null
            },
            sheetState = sheetState,
            containerColor = Color(0xFF151515),
            dragHandle = { BottomSheetDefaults.DragHandle(color = Color.Gray) }
        ) {
            ContratForm(
                initialContrat = contratToEdit,
                onDismiss = {
                    showSheet = false
                    contratToEdit = null
                },
                onSave = { savedContrat ->
                    if (contratToEdit == null) {
                        viewModel.addContrat(savedContrat) // Ajout API
                    } else {
                        viewModel.updateContrat(savedContrat.idContrat, savedContrat) // Modif API
                    }
                    showSheet = false
                    contratToEdit = null
                }
            )
        }
    }
}

// --- COMPOSANTS DE L'INTERFACE ---

@Composable
fun FilterHeader(onAddClick: () -> Unit) {
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

@Composable
fun ContratForm(
    initialContrat: Contrat?,
    onDismiss: () -> Unit,
    onSave: (Contrat) -> Unit
) {
    // Pré-remplir les champs si on est en mode édition
    var clientId by remember { mutableStateOf(initialContrat?.idClient?.toString() ?: "") }
    var vehiculeId by remember { mutableStateOf(initialContrat?.idVehicule ?: "") }
    var debut by remember { mutableStateOf(initialContrat?.dateDebut ?: "") }
    var fin by remember { mutableStateOf(initialContrat?.dateFin ?: "") }
    var statut by remember { mutableStateOf(initialContrat?.statut ?: "En cours") }

    val isEditing = initialContrat != null

    Column(
        modifier = Modifier.padding(24.dp).navigationBarsPadding(),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        Text(
            text = if (isEditing) "Modifier le Contrat" else "Nouveau Contrat",
            color = Color.White,
            fontSize = 20.sp,
            fontWeight = FontWeight.Bold
        )

        CustomTextField(value = clientId, onValueChange = { clientId = it }, label = "ID Client")
        CustomTextField(value = vehiculeId, onValueChange = { vehiculeId = it }, label = "Véhicule (Num Série)")
        CustomTextField(value = statut, onValueChange = { statut = it }, label = "Statut (ex: En cours, Terminé)")

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
                        val id = initialContrat?.idContrat ?: 0 // 0 si c'est un ajout (l'API générera l'ID)
                        val nouveauContrat = Contrat(
                            idContrat = id,
                            dateDebut = debut,
                            dateFin = fin,
                            statut = statut,
                            idClient = clientId.toIntOrNull() ?: 1,
                            idVehicule = vehiculeId
                        )
                        onSave(nouveauContrat)
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

@Composable
fun ContratCard(contrat: Contrat, onEditRequest: () -> Unit, onDeleteRequest: () -> Unit) {
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
                Text("Contrat n°${contrat.idContrat}", color = Color(0xFFE67E22), fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Spacer(modifier = Modifier.height(8.dp))
                InfoLabel("Client (ID)", contrat.idClient?.toString() ?: "Inconnu")
                InfoLabel("Véhicule", contrat.idVehicule ?: "Inconnu")
                InfoLabel("Statut", contrat.statut ?: "Non défini")
                InfoLabel("Période", "${contrat.dateDebut.take(10)} au ${contrat.dateFin.take(10)}")
            }
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                ActionButton("Modifier", Color(0xFFEBF5FB), Color(0xFF2E86C1), onClick = onEditRequest)
                ActionButton("Supprimer", Color(0xFFFDEDEC), Color(0xFFCB4335), onClick = onDeleteRequest)
            }
        }
    }
}

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