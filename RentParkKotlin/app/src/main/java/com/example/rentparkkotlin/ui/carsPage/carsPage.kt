package com.example.rentparkkotlin.ui.carsPage

import androidx.compose.foundation.*
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.*
import androidx.compose.foundation.shape.*
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.*
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.res.stringResource
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.*
import androidx.compose.ui.window.Dialog
import coil.compose.AsyncImage
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.viewmodel.CarViewModel
import com.example.rentparkkotlin.R
import com.example.rentparkkotlin.ui.header.Header

// Couleur thématique
val orangePark = Color(0xFFFF5A19)

@Composable
fun CarListScreen(
    viewModel: CarViewModel = androidx.lifecycle.viewmodel.compose.viewModel()
) {
    var searchQuery by remember { mutableStateOf("") }
    var selectedBoites by remember { mutableStateOf(setOf<String>()) }
    var selectedEnergies by remember { mutableStateOf(setOf<String>()) }
    var selectedPrixRanges by remember { mutableStateOf(setOf<String>()) }
    var filterTypeToOpen by remember { mutableStateOf<String?>(null) }

    val filteredVoitures = remember(
        searchQuery,
        viewModel.voitures,
        selectedBoites,
        selectedEnergies,
        selectedPrixRanges
    ) {
        filtrerLaFlotte(
            viewModel.voitures,
            searchQuery,
            selectedBoites,
            selectedEnergies,
            selectedPrixRanges
        )
    }

    var showAddDialog by remember { mutableStateOf(false) }
    var carToDelete by remember { mutableStateOf<Voiture?>(null) }
    var carToEdit by remember { mutableStateOf<Voiture?>(null) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF8F9FA))
    ) {
        SearchSection(
            query = searchQuery,
            onValueChange = { searchQuery = it },
            onAddClick = { showAddDialog = true }
        )

        FilterBar(
            prixSize = selectedPrixRanges.size,
            boiteSize = selectedBoites.size,
            energieSize = selectedEnergies.size,
            onFilterClick = { filterTypeToOpen = it },
            onClear = {
                selectedBoites = emptySet()
                selectedEnergies = emptySet()
                selectedPrixRanges = emptySet()
            }
        )

        CarGrid(
            isLoading = viewModel.isLoading,
            error = viewModel.error,
            voitures = filteredVoitures,
            onCarClick = { viewModel.getVoitureDetails(it.NumSerie) },
            onEdit = { carToEdit = it },
            onDelete = { carToDelete = it }
        )
    }

    // Gestion centralisée des fenêtres surgissantes
    CarListModals(
        viewModel = viewModel,
        filterTypeToOpen = filterTypeToOpen,
        showAddDialog = showAddDialog,
        carToEdit = carToEdit,
        carToDelete = carToDelete,
        filters = Triple(selectedPrixRanges, selectedBoites, selectedEnergies),
        onFilterDismiss = { filterTypeToOpen = null },
        onFilterConfirm = { type, set ->
            when(type) {
                "Prix" -> selectedPrixRanges = set
                "Boîte" -> selectedBoites = set
                "Énergie" -> selectedEnergies = set
            }
            filterTypeToOpen = null
        },
        onAddDismiss = { showAddDialog = false },
        onEditDismiss = { carToEdit = null },
        onDeleteDismiss = { carToDelete = null }
    )
}

// --- LOGIQUE DE FILTRAGE ---
fun filtrerLaFlotte(
    liste: List<Voiture>,
    query: String,
    boites: Set<String>,
    energies: Set<String>,
    prixRanges: Set<String>
): List<Voiture> {
    return liste.filter { v ->
        val matchSearch = v.Marque.contains(query, true) || v.Nom.contains(query, true)
        val matchBoite = boites.isEmpty() || boites.contains(v.Boite)
        val matchEnergie = energies.isEmpty() || energies.contains(v.Energie)

        val p = v.Prix.toDoubleOrNull() ?: 0.0
        val matchPrix = prixRanges.isEmpty() || prixRanges.any { r ->
            when(r) {
                "0 - 50€" -> p <= 50
                "50 - 100€" -> p in 50.0..100.0
                "100€ +" -> p > 100
                else -> true
            }
        }
        matchSearch && matchBoite && matchEnergie && matchPrix
    }
}

// --- COMPOSANTS DE L'INTERFACE ---
@Composable
fun SearchSection(query: String, onValueChange: (String) -> Unit, onAddClick: () -> Unit) {
    Row(
        modifier = Modifier.fillMaxWidth().padding(16.dp),
        verticalAlignment = Alignment.CenterVertically,
        horizontalArrangement = Arrangement.spacedBy(8.dp)
    ) {
        TextField(
            value = query,
            onValueChange = onValueChange,
            placeholder = { Text(stringResource(R.string.car_search_placeholder)) },
            modifier = Modifier.weight(1f).clip(CircleShape),
            singleLine = true,
            colors = TextFieldDefaults.colors(
                focusedIndicatorColor = Color.Transparent,
                unfocusedIndicatorColor = Color.Transparent
            )
        )
        IconButton(
            onClick = onAddClick,
            modifier = Modifier.size(50.dp).background(orangePark, CircleShape)
        ) {
            Icon(Icons.Default.Add, contentDescription = stringResource(R.string.add_car_title), tint = Color.White)
        }
    }
}

@Composable
fun FilterBar(prixSize: Int, boiteSize: Int, energieSize: Int, onFilterClick: (String) -> Unit, onClear: () -> Unit) {
    Row(
        modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        FilterBadge(if (prixSize > 0) "${stringResource(R.string.filter_price)} ($prixSize)" else stringResource(R.string.filter_price)) { onFilterClick("Prix") }
        FilterBadge(if (boiteSize > 0) "${stringResource(R.string.filter_gearbox)} ($boiteSize)" else stringResource(R.string.filter_gearbox)) { onFilterClick("Boîte") }
        FilterBadge(if (energieSize > 0) "${stringResource(R.string.filter_energy)} ($energieSize)" else stringResource(R.string.filter_energy)) { onFilterClick("Énergie") }

        if (prixSize + boiteSize + energieSize > 0) {
            TextButton(onClick = onClear) { Text("Effacer", color = orangePark) }
        }
    }
}

@Composable
fun CarGrid(isLoading: Boolean, error: String?, voitures: List<Voiture>, onCarClick: (Voiture) -> Unit, onEdit: (Voiture) -> Unit, onDelete: (Voiture) -> Unit) {
    if (isLoading) {
        Box(Modifier.fillMaxSize(), Alignment.Center) { CircularProgressIndicator(color = orangePark) }
    } else if (error != null) {
        Box(Modifier.fillMaxSize(), Alignment.Center) {
            Text(text = stringResource(R.string.car_loading_error, error), color = Color.Red)
        }
    } else {
        LazyVerticalGrid(
            columns = GridCells.Fixed(1),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            items(voitures) { voiture ->
                Box(Modifier.clickable { onCarClick(voiture) }) {
                    CarCard(voiture, onDelete = { onDelete(voiture) }, onEdit = { onEdit(voiture) })
                }
            }
        }
    }
}

@Composable
fun CarCard(voiture: Voiture, onDelete: () -> Unit, onEdit: () -> Unit) {
    Card(
        modifier = Modifier.fillMaxWidth().height(280.dp),
        shape = RoundedCornerShape(20.dp),
        elevation = CardDefaults.cardElevation(4.dp)
    ) {
        Box {
            AsyncImage(
                model = "http://10.0.2.2:9990/${voiture.ImagePath}",
                contentDescription = null,
                contentScale = ContentScale.Crop,
                modifier = Modifier.fillMaxSize()
            )
            Row(
                modifier = Modifier.align(Alignment.TopEnd).padding(8.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                ActionIcon(Icons.Default.Edit, Color(0xFF2196F3), onEdit)
                ActionIcon(Icons.Default.Delete, Color.Red, onDelete)
            }
            Column(
                modifier = Modifier.align(Alignment.BottomCenter).fillMaxWidth()
                    .background(Color.White.copy(alpha = 0.9f)).padding(12.dp)
            ) {
                Text("${voiture.Marque} ${voiture.Nom}", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                    Text("${voiture.Boite} • ${voiture.Energie}", color = Color.Gray, fontSize = 12.sp)
                    Text("${voiture.Prix} €/j", color = orangePark, fontWeight = FontWeight.ExtraBold)
                }
            }
        }
    }
}

@Composable
fun ActionIcon(icon: androidx.compose.ui.graphics.vector.ImageVector, color: Color, onClick: () -> Unit) {
    IconButton(
        onClick = onClick,
        modifier = Modifier.background(Color.White, CircleShape).size(36.dp)
    ) {
        Icon(icon, null, tint = color, modifier = Modifier.size(20.dp))
    }
}

@Composable
fun FilterBadge(text: String, onClick: () -> Unit) {
    Surface(
        modifier = Modifier.clickable { onClick() },
        shape = RoundedCornerShape(12.dp),
        color = Color.White,
        shadowElevation = 2.dp
    ) {
        Text(text, Modifier.padding(horizontal = 12.dp, vertical = 8.dp), fontSize = 13.sp)
    }
}

@Composable
fun CarListModals(
    viewModel: CarViewModel,
    filterTypeToOpen: String?,
    showAddDialog: Boolean,
    carToEdit: Voiture?,
    carToDelete: Voiture?,
    filters: Triple<Set<String>, Set<String>, Set<String>>,
    onFilterDismiss: () -> Unit,
    onFilterConfirm: (String, Set<String>) -> Unit,
    onAddDismiss: () -> Unit,
    onEditDismiss: () -> Unit,
    onDeleteDismiss: () -> Unit
) {
    if (filterTypeToOpen != null) {
        val options = when(filterTypeToOpen) {
            "Prix" -> listOf("0 - 50€", "50 - 100€", "100€ +")
            "Boîte" -> listOf("Manuelle", "Automatique", "Semi-Manuelle")
            else -> listOf("Essence", "Diesel", "Électrique", "Hybride")
        }
        FilterSelectionDialog(
            title = "Filtrer par $filterTypeToOpen",
            options = options,
            initialSelection = when(filterTypeToOpen) {
                "Prix" -> filters.first
                "Boîte" -> filters.second
                else -> filters.third
            },
            onDismiss = onFilterDismiss,
            onConfirm = { onFilterConfirm(filterTypeToOpen, it) }
        )
    }

    viewModel.selectedVoiture?.let {
        CarDetailsDialog(it) { viewModel.clearSelectedVoiture() }
    }

    if (showAddDialog) {
        AddEditCarForm(stringResource(R.string.add_car_title), onDismiss = onAddDismiss, onConfirm = { viewModel.addVoiture(it); onAddDismiss() })
    }

    if (carToEdit != null) {
        AddEditCarForm("Modifier", carToEdit, onDismiss = onEditDismiss, onConfirm = { viewModel.updateVoiture(it); onEditDismiss() })
    }

    if (carToDelete != null) {
        AlertDialog(
            onDismissRequest = onDeleteDismiss,
            title = { Text(stringResource(R.string.delete_car_title)) },
            text = { Text(stringResource(R.string.delete_car_message, carToDelete.Marque, carToDelete.Nom)) },
            confirmButton = {
                Button(onClick = { viewModel.deleteVoiture(carToDelete.NumSerie); onDeleteDismiss() }, colors = ButtonDefaults.buttonColors(containerColor = Color.Red)) {
                    Text(stringResource(R.string.delete_confirm))
                }
            },
            dismissButton = { TextButton(onClick = onDeleteDismiss) { Text(stringResource(R.string.delete_cancel)) } }
        )
    }
}

@Composable
fun CarDetailsDialog(voiture: Voiture, onDismiss: () -> Unit) {
    Dialog(onDismissRequest = onDismiss) {
        Surface(shape = RoundedCornerShape(24.dp), color = Color.White, modifier = Modifier.fillMaxWidth().fillMaxHeight(0.85f)) {
            Column(Modifier.padding(24.dp).verticalScroll(rememberScrollState())) {
                Text("${voiture.Marque} ${voiture.Nom}", fontSize = 24.sp, fontWeight = FontWeight.Bold)
                Text("S/N: ${voiture.NumSerie}", fontSize = 14.sp, color = Color.Gray)
                Spacer(Modifier.height(20.dp))
                DetailSectionTitle("Fiche Technique")
                InfoRow("Année", voiture.Annee)
                InfoRow("Couleur", voiture.Couleur)
                InfoRow("Énergie", voiture.Energie)
                InfoRow("Puissance", "${voiture.Puissance} CV")
                InfoRow("Boîte", voiture.Boite)
                InfoRow("Transmission", voiture.Transmission)
                InfoRow("Places", voiture.NbPlaces)
                InfoRow("Catégorie", voiture.Categorie)
                Spacer(Modifier.height(20.dp))
                DetailSectionTitle("Gestion & Tarifs")
                InfoRow("État actuel", voiture.Etat)
                InfoRow("Prix journalier", "${voiture.Prix} €")
                Spacer(Modifier.height(20.dp))
                DetailSectionTitle("Maintenance")
                InfoRow("Date d'achat", voiture.DateAchat)
                InfoRow("Dernier CT", voiture.DateDernierControleTech)
                InfoRow("Expiration CT", voiture.DateExpirationControleTech)
                Spacer(Modifier.height(24.dp))
                Button(onClick = onDismiss, modifier = Modifier.fillMaxWidth(), colors = ButtonDefaults.buttonColors(containerColor = orangePark)) {
                    Text("Fermer", fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}

@Composable
fun FilterSelectionDialog(title: String, options: List<String>, initialSelection: Set<String>, onDismiss: () -> Unit, onConfirm: (Set<String>) -> Unit) {
    var temp by remember { mutableStateOf(initialSelection) }
    Dialog(onDismissRequest = onDismiss) {
        Surface(shape = RoundedCornerShape(24.dp), color = Color.White) {
            Column(Modifier.padding(20.dp)) {
                Text(title, fontWeight = FontWeight.Bold, fontSize = 18.sp)
                Spacer(Modifier.height(12.dp))
                options.forEach { opt ->
                    Row(Modifier.fillMaxWidth().clickable { temp = if(temp.contains(opt)) temp - opt else temp + opt }.padding(vertical = 8.dp), verticalAlignment = Alignment.CenterVertically) {
                        Checkbox(checked = temp.contains(opt), onCheckedChange = null)
                        Text(opt, Modifier.padding(start = 8.dp))
                    }
                }
                Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.End) {
                    TextButton(onClick = onDismiss) { Text("Annuler") }
                    Button(onClick = { onConfirm(temp) }, colors = ButtonDefaults.buttonColors(containerColor = orangePark)) { Text("Appliquer") }
                }
            }
        }
    }
}

@Composable
fun AddEditCarForm(
    title: String,
    voiture: Voiture? = null,
    onDismiss: () -> Unit,
    onConfirm: (Voiture) -> Unit
) {
    // --- ÉTATS DES CHAMPS (Initialisés avec l'objet si modification) ---
    var numSerie by remember { mutableStateOf(voiture?.NumSerie ?: "") }
    var marque by remember { mutableStateOf(voiture?.Marque ?: "") }
    var nom by remember { mutableStateOf(voiture?.Nom ?: "") }
    var annee by remember { mutableStateOf(voiture?.Annee ?: "") }
    var couleur by remember { mutableStateOf(voiture?.Couleur ?: "") }
    var nbPlaces by remember { mutableStateOf(voiture?.NbPlaces ?: "5") }
    var categorie by remember { mutableStateOf(voiture?.Categorie ?: "") }

    var energie by remember { mutableStateOf(voiture?.Energie ?: "") }
    var puissance by remember { mutableStateOf(voiture?.Puissance ?: "") }
    var transmission by remember { mutableStateOf(voiture?.Transmission ?: "") }
    var boite by remember { mutableStateOf(voiture?.Boite ?: "Manuelle") }

    var prix by remember { mutableStateOf(voiture?.Prix ?: "") }
    var etat by remember { mutableStateOf(voiture?.Etat ?: "Libre") }

    var dateAchat by remember { mutableStateOf(voiture?.DateAchat ?: "") }
    var dateCT by remember { mutableStateOf(voiture?.DateDernierControleTech ?: "") }
    var dateExpCT by remember { mutableStateOf(voiture?.DateExpirationControleTech ?: "") }

    // IDs (Numériques)
    var idAssureur by remember { mutableStateOf(voiture?.IdAssureur?.toString() ?: "") }
    var idFournisseur by remember { mutableStateOf(voiture?.IdFournisseur?.toString() ?: "") }

    val optionsBoite = listOf("Manuelle", "Automatique", "Semi-Manuelle")
    val optionsEtat = listOf("Libre", "Louée", "En réparation", "Vendue")

    Dialog(onDismissRequest = onDismiss) {
        Surface(
            shape = RoundedCornerShape(24.dp),
            color = Color.White,
            modifier = Modifier.fillMaxWidth().fillMaxHeight(0.9f)
        ) {
            Column(
                modifier = Modifier
                    .padding(20.dp)
                    .verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                Text(title, fontSize = 22.sp, fontWeight = FontWeight.Bold, color = Color.Black)

                // --- SECTION 1 : IDENTIFICATION ---
                FormSectionTitle("Identification & Style")
                SimpleField(numSerie, { numSerie = it }, "N° Série (VIN)", readOnly = (voiture != null))

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(marque, { marque = it }, "Marque", Modifier.weight(1f))
                    SimpleField(nom, { nom = it }, "Modèle", Modifier.weight(1f))
                }

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(annee, { annee = it }, "Année", Modifier.weight(1f))
                    SimpleField(couleur, { couleur = it }, "Couleur", Modifier.weight(1f))
                }

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(nbPlaces, { nbPlaces = it }, "Places", Modifier.weight(1f))
                    SimpleField(categorie, { categorie = it }, "Catégorie", Modifier.weight(1f))
                }

                // --- SECTION 2 : TECHNIQUE ---
                FormSectionTitle("Moteur & Transmission")
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(energie, { energie = it }, "Énergie", Modifier.weight(1f))
                    SimpleField(puissance, { puissance = it }, "Puissance (CV)", Modifier.weight(1f))
                }
                SimpleField(transmission, { transmission = it }, "Transmission (ex: Intégrale)")
                StableDropDown("Boîte de vitesse", optionsBoite, boite) { boite = it }

                // --- SECTION 3 : COMMERCIAL & IDS ---
                FormSectionTitle("Gestion & Partenaires")
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(prix, { prix = it }, "Prix / jour (€)", Modifier.weight(1f))
                    Box(Modifier.weight(1f)) {
                        StableDropDown("État", optionsEtat, etat) { etat = it }
                    }
                }

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    // Filtre pour n'accepter que des chiffres
                    SimpleField(idAssureur, { if (it.all { c -> c.isDigit() }) idAssureur = it }, "ID Assureur", Modifier.weight(1f))
                    SimpleField(idFournisseur, { if (it.all { c -> c.isDigit() }) idFournisseur = it }, "ID Fournisseur", Modifier.weight(1f))
                }

                // --- SECTION 4 : MAINTENANCE ---
                FormSectionTitle("Dates de suivi")
                SimpleField(dateAchat, { dateAchat = it }, "Date d'achat (AAAA-MM-JJ)")
                SimpleField(dateCT, { dateCT = it }, "Dernier Contrôle Technique")
                SimpleField(dateExpCT, { dateExpCT = it }, "Expiration Contrôle Technique")

                Spacer(Modifier.height(20.dp))

                // --- ACTIONS ---
                Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.End) {
                    TextButton(onClick = onDismiss) { Text("Annuler") }
                    Button(
                        onClick = {
                            if (numSerie.isNotBlank()) {
                                onConfirm(Voiture(
                                    NumSerie = numSerie,
                                    Marque = marque,
                                    Nom = nom,
                                    Energie = energie,
                                    Puissance = puissance,
                                    Prix = prix,
                                    Boite = boite,
                                    Etat = etat,
                                    Annee = annee,
                                    Couleur = couleur,
                                    NbPlaces = nbPlaces,
                                    Categorie = categorie,
                                    Transmission = transmission,
                                    DateAchat = dateAchat,
                                    DateDernierControleTech = dateCT,
                                    DateExpirationControleTech = dateExpCT,
                                    ImagePath = voiture?.ImagePath ?: "default_car.jpg",
                                    // Conversion sécurisée vers Int
                                    IdAssureur = idAssureur.toIntOrNull() ?: 1,
                                    IdFournisseur = idFournisseur.toIntOrNull() ?: 1
                                ))
                            }
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = orangePark),
                        shape = RoundedCornerShape(12.dp)
                    ) {
                        Text("Enregistrer", color = Color.White, fontWeight = FontWeight.Bold)
                    }
                }
            }
        }
    }
}

@Composable
fun SimpleField(value: String, onValueChange: (String) -> Unit, label: String, modifier: Modifier = Modifier, readOnly: Boolean = false) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        singleLine = true,
        readOnly = readOnly,
        colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = orangePark, unfocusedBorderColor = Color.LightGray)
    )
}

@Composable
fun StableDropDown(label: String, options: List<String>, selected: String, onSelect: (String) -> Unit) {
    var expanded by remember { mutableStateOf(false) }
    Box(modifier = Modifier.fillMaxWidth().padding(vertical = 4.dp)) {
        OutlinedTextField(
            value = selected,
            onValueChange = {},
            readOnly = true,
            label = { Text(label) },
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(12.dp),
            trailingIcon = { Icon(Icons.Default.ArrowDropDown, null, Modifier.clickable { expanded = !expanded }) },
            colors = OutlinedTextFieldDefaults.colors(focusedBorderColor = orangePark, unfocusedBorderColor = Color.LightGray)
        )
        Box(modifier = Modifier.matchParentSize().clickable { expanded = true })
        DropdownMenu(expanded = expanded, onDismissRequest = { expanded = false }) {
            options.forEach { option ->
                DropdownMenuItem(text = { Text(option) }, onClick = { onSelect(option); expanded = false })
            }
        }
    }
}

@Composable
fun FormSectionTitle(text: String) {
    Text(text = text, color = orangePark, fontWeight = FontWeight.Bold, fontSize = 14.sp, modifier = Modifier.padding(top = 8.dp, bottom = 4.dp))
}

@Composable
fun DetailSectionTitle(text: String) {
    Text(text = text.uppercase(), color = orangePark, fontSize = 12.sp, fontWeight = FontWeight.ExtraBold, letterSpacing = 1.sp, modifier = Modifier.padding(top = 16.dp, bottom = 8.dp))
}

@Composable
fun InfoRow(label: String, value: String) {
    Row(Modifier.fillMaxWidth().padding(vertical = 4.dp), horizontalArrangement = Arrangement.SpaceBetween) {
        Text(label, color = Color.Gray, fontSize = 14.sp)
        Text(value, fontWeight = FontWeight.Bold, fontSize = 14.sp)
    }
}