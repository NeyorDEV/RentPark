package com.example.rentparkkotlin.ui.contratsPage

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.BasicTextField
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.ArrowDropDown
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.rentparkkotlin.model.Client
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.ui.components.DatePickerField
import com.example.rentparkkotlin.viewmodel.CarViewModel
import com.example.rentparkkotlin.viewmodel.ClientViewModel
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

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ContractsScreen(
    viewModel: ContratViewModel = viewModel(),
    clientViewModel: ClientViewModel = viewModel(),
    carViewModel: CarViewModel = viewModel()
) {
    val contrats = viewModel.contrats
    val isLoading = viewModel.isLoading
    val error = viewModel.error
    val clients = clientViewModel.clients
    val voitures = carViewModel.voitures

    var searchQuery by remember { mutableStateOf("") }
    var selectedFilter by remember { mutableStateOf("Tous") }

    var showDeleteDialog by remember { mutableStateOf(false) }
    var contratToDelete by remember { mutableStateOf<Contrat?>(null) }
    var showSheet by remember { mutableStateOf(false) }
    var contratToEdit by remember { mutableStateOf<Contrat?>(null) }
    var contratDetails by remember { mutableStateOf<Contrat?>(null) }

    val sheetState = rememberModalBottomSheetState()

    // --- LOGIQUE DE FILTRAGE AMÉLIORÉE ---
    val filteredContrats = contrats.filter { contrat ->
        // On cherche par ID contrat, ID véhicule OU nom du client s'il est trouvé dans la liste
        val clientName = clients.find { it.idClient == contrat.idClient }?.nom ?: ""
        val matchesSearch = contrat.idVehicule?.contains(searchQuery, ignoreCase = true) == true ||
                contrat.idContrat.toString().contains(searchQuery) ||
                clientName.contains(searchQuery, ignoreCase = true)

        val matchesFilter = if (selectedFilter == "Tous") true else contrat.statut == selectedFilter
        matchesSearch && matchesFilter
    }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0F0F0F))
            .padding(16.dp)
    ) {
        FilterHeader(
            searchQuery = searchQuery,
            onSearchChange = { searchQuery = it },
            selectedFilter = selectedFilter,
            onFilterChange = { selectedFilter = it },
            onAddClick = {
                contratToEdit = null
                showSheet = true
            }
        )

        Spacer(modifier = Modifier.height(24.dp))

        if (error != null) {
            Text(text = error, color = Color(0xFFE74C3C), fontWeight = FontWeight.Bold, modifier = Modifier.padding(8.dp))
        }

        if (isLoading) {
            Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                CircularProgressIndicator(color = Color(0xFFE67E22))
            }
        } else {
            LazyColumn(verticalArrangement = Arrangement.spacedBy(16.dp)) {
                items(filteredContrats) { contrat ->
                    ContratCard(
                        contrat = contrat,
                        onEditRequest = {
                            contratToEdit = contrat
                            showSheet = true
                        },
                        onDeleteRequest = {
                            contratToDelete = contrat
                            showDeleteDialog = true
                        },
                        onCardClick = { contratDetails = contrat },
                        onValidateRequest = { viewModel.updateContratStatut(contrat.idContrat, "Validé") },
                        onCancelRequest = { viewModel.updateContratStatut(contrat.idContrat, "Annulé") }
                    )
                }
            }
        }
    }

    // --- DIALOGUES ---
    if (showDeleteDialog && contratToDelete != null) {
        AlertDialog(
            onDismissRequest = { showDeleteDialog = false },
            containerColor = Color(0xFF1A1A1A),
            title = { Text("Confirmer la suppression", color = Color.White, fontSize = 18.sp) },
            text = { Text("Voulez-vous vraiment supprimer le contrat n°${contratToDelete?.idContrat} ?", color = Color.LightGray) },
            confirmButton = {
                TextButton(onClick = {
                    contratToDelete?.let { viewModel.deleteContrat(it.idContrat) }
                    showDeleteDialog = false
                }) { Text("Supprimer", color = Color(0xFFCB4335), fontWeight = FontWeight.Bold) }
            },
            dismissButton = {
                TextButton(onClick = { showDeleteDialog = false }) { Text("Annuler", color = Color.White) }
            }
        )
    }

    if (showSheet) {
        ModalBottomSheet(
            onDismissRequest = { showSheet = false; contratToEdit = null },
            sheetState = sheetState,
            containerColor = Color(0xFF151515),
            dragHandle = { BottomSheetDefaults.DragHandle(color = Color.Gray) }
        ) {
            ContratForm(
                initialContrat = contratToEdit,
                clients = clients,
                voitures = voitures,
                onDismiss = { showSheet = false; contratToEdit = null },
                onSave = { saved ->
                    if (contratToEdit == null) viewModel.addContrat(saved)
                    else viewModel.updateContrat(saved.idContrat, saved)
                    showSheet = false
                }
            )
        }
    }

    contratDetails?.let { ContratDetailsDialog(contrat = it, onDismiss = { contratDetails = null }) }
}

@Composable
fun FilterHeader(
    searchQuery: String,
    onSearchChange: (String) -> Unit,
    selectedFilter: String,
    onFilterChange: (String) -> Unit,
    onAddClick: () -> Unit
) {
    var expanded by remember { mutableStateOf(false) }
    val filterOptions = listOf("Tous", "Validé", "Annulé", "EnCoursValidation")

    Surface(color = Color(0xFF151515), shape = RoundedCornerShape(24.dp), modifier = Modifier.fillMaxWidth()) {
        Row(
            modifier = Modifier.padding(horizontal = 12.dp, vertical = 8.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            Surface(color = Color(0xFF222222), shape = RoundedCornerShape(12.dp), modifier = Modifier.weight(1f)) {
                Row(verticalAlignment = Alignment.CenterVertically, modifier = Modifier.padding(horizontal = 12.dp, vertical = 8.dp)) {
                    Icon(Icons.Default.Search, null, tint = Color.Gray, modifier = Modifier.size(18.dp))
                    BasicTextField(
                        value = searchQuery,
                        onValueChange = onSearchChange,
                        textStyle = TextStyle(color = Color.White, fontSize = 14.sp),
                        modifier = Modifier.padding(start = 8.dp).fillMaxWidth(),
                        decorationBox = { inner ->
                            if (searchQuery.isEmpty()) Text("Rechercher...", color = Color.DarkGray, fontSize = 14.sp)
                            inner()
                        }
                    )
                }
            }

            Box {
                Surface(color = Color(0xFF222222), shape = RoundedCornerShape(12.dp), modifier = Modifier.clickable { expanded = true }) {
                    Row(modifier = Modifier.padding(horizontal = 12.dp, vertical = 8.dp), verticalAlignment = Alignment.CenterVertically) {
                        Text("Filtrer: ", color = Color.Gray, fontSize = 12.sp)
                        Text(selectedFilter, color = Color(0xFFE67E22), fontSize = 12.sp, fontWeight = FontWeight.Bold)
                        Icon(Icons.Default.ArrowDropDown, null, tint = Color.Gray)
                    }
                }
                DropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }, modifier = Modifier.background(Color(0xFF222222))) {
                    filterOptions.forEach { option ->
                        DropdownMenuItem(
                            text = { Text(option, color = Color.White) },
                            onClick = { onFilterChange(option); expanded = false }
                        )
                    }
                }
            }

            IconButton(onClick = onAddClick, modifier = Modifier.size(40.dp).background(Color(0xFF7DCEA0), RoundedCornerShape(12.dp))) {
                Text("+", color = Color.Black, fontWeight = FontWeight.Bold, fontSize = 20.sp)
            }
        }
    }
}

@Composable
fun ContratForm(
    initialContrat: Contrat?,
    onDismiss: () -> Unit,
    onSave: (Contrat) -> Unit,
    clients: List<Client>,
    voitures: List<Voiture>
) {
    var clientId by remember { mutableStateOf(initialContrat?.idClient?.toString() ?: "") }
    var vehiculeId by remember { mutableStateOf(initialContrat?.idVehicule ?: "") }
    var debut by remember { mutableStateOf(initialContrat?.dateDebut ?: "") }
    var fin by remember { mutableStateOf(initialContrat?.dateFin ?: "") }
    var statut by remember { mutableStateOf(initialContrat?.statut ?: "EnCoursValidation") }

    val optionsStatut = listOf("Validé", "Annulé", "EnCoursValidation")

    Column(modifier = Modifier.padding(24.dp).navigationBarsPadding().verticalScroll(rememberScrollState()), verticalArrangement = Arrangement.spacedBy(16.dp)) {
        Text(if (initialContrat != null) "Modifier le Contrat" else "Nouveau Contrat", color = Color.White, fontSize = 20.sp, fontWeight = FontWeight.Bold)

        DropdownSelector("Client", clients, clientId, { clientId = it }, { "${it.nom.uppercase()} ${it.prenom}" }, { it.idClient.toString() })
        DropdownSelector("Véhicule", voitures, vehiculeId, { vehiculeId = it }, { "${it.Marque} ${it.Nom} (${it.NumSerie})" }, { it.NumSerie })
        DropdownSelector("Statut", optionsStatut, statut, { statut = it }, { it }, { it })

        Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
            Box(Modifier.weight(1f)) { DatePickerField(debut, { debut = it }, "Date de début") }
            Box(Modifier.weight(1f)) { DatePickerField(fin, { fin = it }, "Date de fin") }
        }

        Row(modifier = Modifier.fillMaxWidth().padding(top = 16.dp), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
            OutlinedButton(onClick = onDismiss, modifier = Modifier.weight(1f), shape = RoundedCornerShape(12.dp)) {
                Text("Annuler", color = Color.White)
            }
            Button(onClick = {
                if (clientId.isNotEmpty() && vehiculeId.isNotEmpty()) {
                    onSave(Contrat(initialContrat?.idContrat ?: 0, debut, fin, statut, clientId.toIntOrNull() ?: 1, vehiculeId))
                }
            }, modifier = Modifier.weight(1f), colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF7DCEA0)), shape = RoundedCornerShape(12.dp)) {
                Text("Confirmer", color = Color.Black, fontWeight = FontWeight.Bold)
            }
        }
        Spacer(modifier = Modifier.height(24.dp))
    }
}

@Composable
fun ContratCard(contrat: Contrat, onEditRequest: () -> Unit, onDeleteRequest: () -> Unit, onCardClick: () -> Unit, onValidateRequest: () -> Unit, onCancelRequest: () -> Unit) {
    Card(
        modifier = Modifier.fillMaxWidth().clickable { onCardClick() },
        colors = CardDefaults.cardColors(containerColor = Color(0xFF181818)),
        shape = RoundedCornerShape(20.dp)
    ) {
        Row(modifier = Modifier.padding(20.dp), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
            Column(modifier = Modifier.weight(1f)) {
                Text("Contrat n°${contrat.idContrat}", color = Color(0xFFE67E22), fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Spacer(modifier = Modifier.height(8.dp))
                InfoLabel("Client (ID)", contrat.idClient.toString())
                InfoLabel("Véhicule", contrat.idVehicule ?: "Inconnu")
                InfoLabel("Statut", contrat.statut ?: "Non défini")
                InfoLabel("Période", "${contrat.dateDebut.take(10)} au ${contrat.dateFin.take(10)}")
            }
            Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                if (contrat.statut == "EnCoursValidation") {
                    ActionButton("Valider", Color(0xFFE8F8F5), Color(0xFF27AE60), onValidateRequest)
                    ActionButton("Refuser", Color(0xFFFDEDEC), Color(0xFFCB4335), onCancelRequest)
                } else {
                    ActionButton("Modifier", Color(0xFFEBF5FB), Color(0xFF2E86C1), onEditRequest)
                    ActionButton("Supprimer", Color(0xFFFDEDEC), Color(0xFFCB4335), onDeleteRequest)
                }
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
        modifier = Modifier.width(100.dp).height(32.dp),
        contentPadding = PaddingValues(horizontal = 8.dp)
    ) {
        Text(text, color = textColor, fontSize = 11.sp, fontWeight = FontWeight.Bold)
    }
}

@Composable
fun ContratDetailsDialog(contrat: Contrat, onDismiss: () -> Unit) {
    AlertDialog(
        onDismissRequest = onDismiss, containerColor = Color(0xFF151515),
        title = { Text("Détails du Contrat", color = Color.White, fontSize = 20.sp, fontWeight = FontWeight.Bold) },
        text = {
            Column(modifier = Modifier.verticalScroll(rememberScrollState()), verticalArrangement = Arrangement.spacedBy(12.dp)) {
                ContratDetailRow("ID Contrat", contrat.idContrat.toString())
                ContratDetailRow("Client (ID)", contrat.idClient.toString())
                ContratDetailRow("Véhicule (S/N)", contrat.idVehicule ?: "")
                ContratDetailRow("Date Début", contrat.dateDebut.take(10))
                ContratDetailRow("Date Fin", contrat.dateFin.take(10))
                ContratDetailRow("Statut", contrat.statut ?: "")
            }
        },
        confirmButton = { TextButton(onClick = onDismiss) { Text("FERMER", color = Color(0xFFE67E22), fontWeight = FontWeight.Bold) } }
    )
}

@Composable
fun ContratDetailRow(label: String, value: String) {
    Row(Modifier.fillMaxWidth().padding(vertical = 4.dp), horizontalArrangement = Arrangement.SpaceBetween) {
        Text(label, color = Color.Gray, fontSize = 14.sp)
        Text(value, color = Color.White, fontSize = 14.sp, fontWeight = FontWeight.Medium)
    }
}

@Composable
fun <T> DropdownSelector(label: String, items: List<T>, selectedId: String, onItemSelected: (String) -> Unit, itemLabel: (T) -> String, itemId: (T) -> String) {
    var expanded by remember { mutableStateOf(false) }
    val text = items.find { itemId(it) == selectedId }?.let { itemLabel(it) } ?: "Sélectionner..."
    Box(Modifier.fillMaxWidth().padding(vertical = 4.dp)) {
        OutlinedTextField(
            value = text, onValueChange = {}, readOnly = true,
            label = { Text(label, color = Color.Gray) },
            modifier = Modifier.fillMaxWidth(),
            textStyle = TextStyle(color = Color.White),
            shape = RoundedCornerShape(12.dp),
            trailingIcon = { Icon(Icons.Default.ArrowDropDown, null, tint = Color.Gray) },
            colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = Color(0xFFE67E22), unfocusedBorderColor = Color.Gray)
        )
        Box(Modifier.matchParentSize().clickable { expanded = true })
        DropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }, modifier = Modifier.background(Color(0xFF222222))) {
            if (items.isEmpty()) {
                DropdownMenuItem(text = { Text("Aucune donnée", color = Color.Gray) }, onClick = { expanded = false })
            }
            items.forEach { item ->
                DropdownMenuItem(text = { Text(itemLabel(item), color = Color.White) }, onClick = { onItemSelected(itemId(item)); expanded = false })
            }
        }
    }
}