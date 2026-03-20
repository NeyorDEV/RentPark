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
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.*
import androidx.compose.ui.window.Dialog
import coil.compose.AsyncImage
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.viewmodel.CarViewModel
import com.example.rentparkkotlin.ui.header.Header

// 1. ÉCRAN PRINCIPAL (L'ARCHITECTE)

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
        Header("Flotte Automobile", onMenuClick = {})

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

// 2. LOGIQUE MÉTIER (LE FILTRAGE)

fun filtrerLaFlotte(
    liste: List<Voiture>,
    query: String,
    boites: Set<String>,
    energies: Set<String>,
    prixRanges: Set<String>
): List<Voiture> {
    return liste.filter { v ->
        val matchSearch = v.Marque.contains(query, true) ||
                v.Nom.contains(query, true)

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

// 3. SECTIONS DE L'INTERFACE

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
            placeholder = { Text("Rechercher...") },
            modifier = Modifier.weight(1f).clip(CircleShape),
            singleLine = true,
            colors = TextFieldDefaults.colors(
                focusedIndicatorColor = Color.Transparent,
                unfocusedIndicatorColor = Color.Transparent
            )
        )
        IconButton(
            onClick = onAddClick,
            modifier = Modifier.size(50.dp).background(Color(0xFFFF5A19), CircleShape)
        ) {
            Icon(Icons.Default.Add, null, tint = Color.White)
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
        FilterBadge(if (prixSize > 0) "Prix ($prixSize)" else "Prix") { onFilterClick("Prix") }
        FilterBadge(if (boiteSize > 0) "Boîte ($boiteSize)" else "Boîte") { onFilterClick("Boîte") }
        FilterBadge(if (energieSize > 0) "Énergie ($energieSize)" else "Énergie") { onFilterClick("Énergie") }

        if (prixSize + boiteSize + energieSize > 0) {
            TextButton(onClick = onClear) { Text("Effacer", color = Color(0xFFFF5A19)) }
        }
    }
}

@Composable
fun CarGrid(isLoading: Boolean, error: String?, voitures: List<Voiture>, onCarClick: (Voiture) -> Unit, onEdit: (Voiture) -> Unit, onDelete: (Voiture) -> Unit) {
    if (isLoading) {
        Box(Modifier.fillMaxSize(), Alignment.Center) { CircularProgressIndicator(color = Color(0xFFFF5A19)) }
    } else {
        LazyVerticalGrid(
            columns = GridCells.Fixed(1),
            contentPadding = PaddingValues(16.dp),
            verticalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            items(voitures) { voiture ->
                Box(Modifier.clickable { onCarClick(voiture) }) {
                    CarCard(voiture, { onDelete(voiture) }, { onEdit(voiture) })
                }
            }
        }
    }
}

// 4. COMPOSANTS GRAPHIQUES (UI ELEMENTS)

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
fun InfoRow(label: String, value: String) {
    Row(Modifier.fillMaxWidth().padding(vertical = 4.dp), horizontalArrangement = Arrangement.SpaceBetween) {
        Text(label, color = Color.Gray, fontSize = 14.sp)
        Text(value, fontWeight = FontWeight.Bold, fontSize = 14.sp)
    }
}

@Composable
fun SimpleField(
    value: String,
    onValueChange: (String) -> Unit,
    label: String,
    modifier: Modifier = Modifier, // Indispensable pour le weight(1f)
    readOnly: Boolean = false
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        // On combine le modifier passé en argument avec le fillMaxWidth
        modifier = modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        singleLine = true,
        readOnly = readOnly,
        colors = OutlinedTextFieldDefaults.colors(
            focusedBorderColor = Color(0xFFFF5A19),
            unfocusedBorderColor = Color.LightGray,
            focusedLabelColor = Color(0xFFFF5A19),
            cursorColor = Color(0xFFFF5A19)
        )
    )
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
    // 1. Sélection des Filtres
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

    // 2. Affichage des Détails
    viewModel.selectedVoiture?.let {
        CarDetailsDialog(it) { viewModel.clearSelectedVoiture() }
    }

    // 3. Formulaires CRUD
    if (showAddDialog) {
        AddEditCarForm("Nouveau Véhicule", onDismiss = onAddDismiss, onConfirm = { viewModel.addVoiture(it); onAddDismiss() })
    }

    if (carToEdit != null) {
        AddEditCarForm("Modifier", carToEdit, onDismiss = onEditDismiss, onConfirm = { viewModel.updateVoiture(it); onEditDismiss() })
    }

    // 4. Confirmation Suppression
    if (carToDelete != null) {
        AlertDialog(
            onDismissRequest = onDeleteDismiss,
            title = { Text("Supprimer") },
            text = { Text("Supprimer la ${carToDelete.Marque} ${carToDelete.Nom} ?") },
            confirmButton = {
                Button(onClick = { viewModel.deleteVoiture(carToDelete.NumSerie); onDeleteDismiss() }, colors = ButtonDefaults.buttonColors(containerColor = Color.Red)) {
                    Text("Supprimer")
                }
            },
            dismissButton = { TextButton(onClick = onDeleteDismiss) { Text("Annuler") } }
        )
    }
}

@Composable
fun CarDetailsDialog(voiture: Voiture, onDismiss: () -> Unit) {
    Dialog(onDismissRequest = onDismiss) {
        Surface(
            shape = RoundedCornerShape(24.dp),
            color = Color.White,
            modifier = Modifier.fillMaxWidth().fillMaxHeight(0.85f)
        ) {
            Column(Modifier.padding(24.dp).verticalScroll(rememberScrollState())) {
                Text("${voiture.Marque} ${voiture.Nom}", fontSize = 24.sp, fontWeight = FontWeight.Bold)
                Text("S/N: ${voiture.NumSerie}", fontSize = 14.sp, color = Color.Gray)

                Spacer(Modifier.height(20.dp))

                DetailSectionTitle("Fiche Technique", Color(0xFFFF5A19))
                InfoRow("Année", voiture.Annee)
                InfoRow("Couleur", voiture.Couleur)
                InfoRow("Énergie", voiture.Energie)
                InfoRow("Puissance", "${voiture.Puissance} CV")
                InfoRow("Boîte", voiture.Boite)
                InfoRow("Transmission", voiture.Transmission)
                InfoRow("Places", voiture.NbPlaces)
                InfoRow("Catégorie", voiture.Categorie)

                Spacer(Modifier.height(20.dp))

                DetailSectionTitle("Gestion & Tarifs", Color(0xFFFF5A19))
                InfoRow("État actuel", voiture.Etat)
                InfoRow("Prix journalier", "${voiture.Prix} €")

                Spacer(Modifier.height(20.dp))

                DetailSectionTitle("Maintenance", Color(0xFFFF5A19))
                InfoRow("Date d'achat", voiture.DateAchat)
                InfoRow("Dernier CT", voiture.DateDernierControleTech)
                InfoRow("Expiration CT", voiture.DateExpirationControleTech)

                Spacer(Modifier.height(24.dp))

                Button(
                    onClick = onDismiss,
                    modifier = Modifier.fillMaxWidth(),
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFF5A19))
                ) { Text("Fermer", fontWeight = FontWeight.Bold) }
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
                    Button(onClick = { onConfirm(temp) }, colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFF5A19))) { Text("Appliquer") }
                }
            }
        }
    }
}

@Composable
fun CarCard(
    voiture: Voiture,
    onDelete: () -> Unit,
    onEdit: () -> Unit
) {
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

            // Boutons d'action rapides
            Row(
                modifier = Modifier.align(Alignment.TopEnd).padding(8.dp),
                horizontalArrangement = Arrangement.spacedBy(8.dp)
            ) {
                ActionIcon(Icons.Default.Edit, Color(0xFF2196F3), onEdit)
                ActionIcon(Icons.Default.Delete, Color.Red, onDelete)
            }

            // Bandeau d'infos
            Column(
                modifier = Modifier.align(Alignment.BottomCenter).fillMaxWidth()
                    .background(Color.White.copy(alpha = 0.9f)).padding(12.dp)
            ) {
                Text("${voiture.Marque} ${voiture.Nom}", fontWeight = FontWeight.Bold, fontSize = 16.sp)
                Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween) {
                    Text("${voiture.Boite} • ${voiture.Energie}", color = Color.Gray, fontSize = 12.sp)
                    Text("${voiture.Prix} €/j", color = Color(0xFFFF5A19), fontWeight = FontWeight.ExtraBold)
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
fun AddEditCarForm(
    title: String,
    voiture: Voiture? = null,
    onDismiss: () -> Unit,
    onConfirm: (Voiture) -> Unit
) {
    // --- ÉTATS POUR TOUS LES CHAMPS ---
    var numSerie by remember { mutableStateOf(voiture?.NumSerie ?: "") }
    var marque by remember { mutableStateOf(voiture?.Marque ?: "") }
    var nom by remember { mutableStateOf(voiture?.Nom ?: "") }
    var energie by remember { mutableStateOf(voiture?.Energie ?: "") }
    var puissance by remember { mutableStateOf(voiture?.Puissance ?: "") }
    var prix by remember { mutableStateOf(voiture?.Prix ?: "") }
    var boite by remember { mutableStateOf(voiture?.Boite ?: "Manuelle") }
    var etat by remember { mutableStateOf(voiture?.Etat ?: "Libre") }
    var annee by remember { mutableStateOf(voiture?.Annee ?: "") }
    var couleur by remember { mutableStateOf(voiture?.Couleur ?: "") }
    var nbPlaces by remember { mutableStateOf(voiture?.NbPlaces ?: "5") }
    var categorie by remember { mutableStateOf(voiture?.Categorie ?: "") }
    var transmission by remember { mutableStateOf(voiture?.Transmission ?: "") }
    var dateAchat by remember { mutableStateOf(voiture?.DateAchat ?: "") }
    var dateCT by remember { mutableStateOf(voiture?.DateDernierControleTech ?: "") }
    var dateExpCT by remember { mutableStateOf(voiture?.DateExpirationControleTech ?: "") }

    var idAssureur by remember { mutableStateOf(voiture?.IdAssureur?.toString() ?: "1") }
    var idFournisseur by remember { mutableStateOf(voiture?.IdFournisseur?.toString() ?: "1") }

    val optionsBoite = listOf("Manuelle", "Automatique", "Semi-Manuelle")
    val optionsEtat = listOf("Libre", "Louée", "En réparation", "Vendue")

    Dialog(onDismissRequest = onDismiss) {
        Surface(
            shape = RoundedCornerShape(24.dp),
            color = Color.White,
            modifier = Modifier.fillMaxWidth().fillMaxHeight(0.9f)
        ) {
            Column(
                modifier = Modifier.padding(20.dp).verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                Text(title, fontSize = 20.sp, fontWeight = FontWeight.Bold)

                FormSectionTitle("Identification & Style")

                SimpleField(numSerie, { numSerie = it }, "N° Série", readOnly = (voiture != null))

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(marque, { marque = it }, "Marque", modifier = Modifier.weight(1f))
                    SimpleField(nom, { nom = it }, "Modèle", modifier = Modifier.weight(1f))
                }
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(annee, { annee = it }, "Année", modifier = Modifier.weight(1f))
                    SimpleField(couleur, { couleur = it }, "Couleur", modifier = Modifier.weight(1f))
                }

                FormSectionTitle("Technique")

                StableDropDown("Boîte de vitesse", optionsBoite, boite) { boite = it }

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(puissance, { puissance = it }, "Puissance (CV)", modifier = Modifier.weight(1f))
                    SimpleField(energie, { energie = it }, "Énergie", modifier = Modifier.weight(1f))
                }
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(nbPlaces, { nbPlaces = it }, "Places", modifier = Modifier.weight(1f))
                    SimpleField(transmission, { transmission = it }, "Transmission", modifier = Modifier.weight(1f))
                }
                SimpleField(categorie, { categorie = it }, "Catégorie (Ex: SUV, Citadine)")

                FormSectionTitle("Administration (IDs)")

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(idAssureur, { idAssureur = it }, "ID Assureur", modifier = Modifier.weight(1f))
                    SimpleField(idFournisseur, { idFournisseur = it }, "ID Fournisseur", modifier = Modifier.weight(1f))
                }

                FormSectionTitle("Gestion & Maintenance")

                StableDropDown("État actuel", optionsEtat, etat) { etat = it }
                SimpleField(prix, { prix = it }, "Prix de location / jour (€)")
                SimpleField(dateAchat, { dateAchat = it }, "Date d'achat (AAAA-MM-JJ)")

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(dateCT, { dateCT = it }, "Dernier CT", modifier = Modifier.weight(1f))
                    SimpleField(dateExpCT, { dateExpCT = it }, "Expiration CT", modifier = Modifier.weight(1f))
                }

                Spacer(Modifier.height(16.dp))

                Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.End) {
                    TextButton(onClick = onDismiss) { Text("Annuler") }
                    Button(
                        onClick = {
                            if (numSerie.isNotBlank()) {
                                onConfirm(Voiture(
                                    NumSerie = numSerie, Marque = marque, Nom = nom,
                                    Energie = energie, Puissance = puissance, Prix = prix,
                                    Boite = boite, Etat = etat, Annee = annee,
                                    Couleur = couleur, NbPlaces = nbPlaces, Categorie = categorie,
                                    Transmission = transmission, DateAchat = dateAchat,
                                    DateDernierControleTech = dateCT, DateExpirationControleTech = dateExpCT,
                                    ImagePath = voiture?.ImagePath ?: "default_car.jpg",
                                    IdAssureur = voiture?.IdAssureur ?: 1,
                                    IdFournisseur = voiture?.IdFournisseur ?: 1
                                ))
                            }
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFF5A19))
                    ) { Text("Enregistrer") }
                }
            }
        }
    }
}
@Composable
fun StableDropDown(
    label: String,
    options: List<String>,
    selected: String,
    onSelect: (String) -> Unit
) {
    var expanded by remember { mutableStateOf(false) }

    Box(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 4.dp)
    ) {
        OutlinedTextField(
            value = selected,
            onValueChange = {},
            readOnly = true,
            label = { Text(label) },
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(12.dp),
            trailingIcon = {
                Icon(
                    imageVector = Icons.Default.ArrowDropDown,
                    contentDescription = null,
                    modifier = Modifier.clickable { expanded = !expanded }
                )
            },
            colors = OutlinedTextFieldDefaults.colors(
                focusedBorderColor = Color(0xFFFF5A19),
                unfocusedBorderColor = Color.LightGray
            )
        )

        // Zone invisible par-dessus le champ pour détecter le clic partout
        Box(
            modifier = Modifier
                .matchParentSize()
                .clickable { expanded = true }
        )

        DropdownMenu(
            expanded = expanded,
            onDismissRequest = { expanded = false },
            modifier = Modifier.fillMaxWidth(0.8f)
        ) {
            options.forEach { option ->
                DropdownMenuItem(
                    text = { Text(text = option) },
                    onClick = {
                        onSelect(option)
                        expanded = false
                    }
                )
            }
        }
    }
}

@Composable
fun FormSectionTitle(text: String) {
    Text(
        text = text,
        color = Color(0xFFFF5A19),
        fontWeight = FontWeight.Bold,
        fontSize = 14.sp,
        modifier = Modifier.padding(top = 8.dp, bottom = 4.dp)
    )
}

@Composable
fun DetailSectionTitle(
    text: String,
    color: Color = Color(0xFFFF5A19)
) {
    Text(
        text = text.uppercase(),
        color = color,
        fontSize = 12.sp,
        fontWeight = FontWeight.ExtraBold,
        letterSpacing = 1.sp,
        modifier = Modifier.padding(top = 16.dp, bottom = 8.dp)
    )
}