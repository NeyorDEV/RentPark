package com.example.rentparkkotlin.ui.carsPage

import androidx.compose.foundation.background
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.ArrowDropDown
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog
import coil.compose.AsyncImage
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.viewmodel.CarViewModel
import com.example.rentparkkotlin.ui.header.Header

@Composable
fun CarListScreen(
    viewModel: CarViewModel = androidx.lifecycle.viewmodel.compose.viewModel()
) {
    val voitures = viewModel.voitures
    val isLoading = viewModel.isLoading
    val error = viewModel.error

    // État pour la recherche
    var searchQuery by remember { mutableStateOf("") }

    // Logique de filtrage local
    val filteredVoitures = remember(searchQuery, voitures) {
        if (searchQuery.isEmpty()) {
            voitures
        } else {
            voitures.filter { voiture ->
                voiture.Marque.contains(searchQuery, ignoreCase = true) ||
                        voiture.Nom.contains(searchQuery, ignoreCase = true) ||
                        voiture.NumSerie.contains(searchQuery, ignoreCase = true)
            }
        }
    }

    // États pour les modales
    var showAddDialog by remember { mutableStateOf(false) }
    var carToDelete by remember { mutableStateOf<Voiture?>(null) }
    var carToEdit by remember { mutableStateOf<Voiture?>(null) }

    val orangeColor = Color(0xFFFF5A19)

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF8F9FA))
    ) {
        Header("Flotte Automobile", onMenuClick = {})

        // 1. Barre de Recherche + Bouton Ajouter
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            Box(
                modifier = Modifier
                    .weight(1f)
                    .clip(CircleShape)
                    .background(Color.White)
                    .padding(horizontal = 16.dp, vertical = 2.dp),
                contentAlignment = Alignment.CenterStart
            ) {
                TextField(
                    value = searchQuery,
                    onValueChange = { searchQuery = it },
                    placeholder = { Text("Rechercher...", color = Color.Gray) },
                    singleLine = true,
                    modifier = Modifier.fillMaxWidth(),
                    colors = TextFieldDefaults.colors(
                        focusedContainerColor = Color.Transparent,
                        unfocusedContainerColor = Color.Transparent,
                        disabledContainerColor = Color.Transparent,
                        focusedIndicatorColor = Color.Transparent,
                        unfocusedIndicatorColor = Color.Transparent,
                    )
                )
            }

            IconButton(
                onClick = { showAddDialog = true },
                modifier = Modifier
                    .size(54.dp)
                    .background(orangeColor, CircleShape)
            ) {
                Icon(Icons.Default.Add, contentDescription = "Ajouter", tint = Color.White)
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        // 2. Filtres
        Row(
            modifier = Modifier.fillMaxWidth().padding(horizontal = 16.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            FilterBadge(text = "Prix", icon = "€")
            FilterBadge(text = "Boîte", icon = "⚙️")
            FilterBadge(text = "Énergie", icon = "⚡")
        }

        Spacer(modifier = Modifier.height(20.dp))

        // 3. Liste des voitures
        when {
            isLoading -> {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    CircularProgressIndicator(color = orangeColor)
                }
            }
            error != null -> {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Text(text = "Erreur : $error", color = Color.Red)
                }
            }
            else -> {
                LazyVerticalGrid(
                    columns = GridCells.Fixed(1),
                    verticalArrangement = Arrangement.spacedBy(12.dp),
                    contentPadding = PaddingValues(16.dp),
                    modifier = Modifier.fillMaxSize()
                ) {
                    items(filteredVoitures, key = { it.NumSerie }) { voiture ->
                        // AJOUT DU CLIC POUR LES DÉTAILS
                        Box(modifier = Modifier.clickable {
                            viewModel.getVoitureDetails(voiture.NumSerie)
                        }) {
                            CarCard(
                                voiture = voiture,
                                onDeleteClick = { carToDelete = voiture },
                                onEditClick = { carToEdit = voiture }
                            )
                        }
                    }
                }
            }
        }
    }

    // --- AFFICHAGE DES MODALES ---

    // Modale Détails (Appelée via le clic sur la carte)
    viewModel.selectedVoiture?.let { selected ->
        CarDetailsDialog(
            voiture = selected,
            onDismiss = { viewModel.clearSelectedVoiture() }
        )
    }

    // Modale Ajout
    if (showAddDialog) {
        AddEditCarForm(
            title = "Nouveau Véhicule",
            onDismiss = { showAddDialog = false },
            onConfirm = {
                viewModel.addVoiture(it)
                showAddDialog = false
            }
        )
    }

    // Modale Édition
    if (carToEdit != null) {
        AddEditCarForm(
            title = "Modifier le véhicule",
            voiture = carToEdit,
            onDismiss = { carToEdit = null },
            onConfirm = {
                viewModel.updateVoiture(it)
                carToEdit = null
            }
        )
    }

    // Modale Suppression
    if (carToDelete != null) {
        AlertDialog(
            onDismissRequest = { carToDelete = null },
            title = { Text("Supprimer le véhicule") },
            text = { Text("Voulez-vous vraiment supprimer la ${carToDelete?.Marque} ${carToDelete?.Nom} ?") },
            confirmButton = {
                Button(
                    onClick = {
                        carToDelete?.let { viewModel.deleteVoiture(it.NumSerie) }
                        carToDelete = null
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = Color.Red)
                ) {
                    Text("Supprimer", color = Color.White)
                }
            },
            dismissButton = {
                TextButton(onClick = { carToDelete = null }) { Text("Annuler") }
            }
        )
    }
}

@Composable
fun AddEditCarForm(
    title: String,
    voiture: Voiture? = null,
    onDismiss: () -> Unit,
    onConfirm: (Voiture) -> Unit
) {
    // Initialisation avec les valeurs de la voiture si elle existe (Edit), sinon vide (Add)
    var numSerie by remember { mutableStateOf(voiture?.NumSerie ?: "") }
    var marque by remember { mutableStateOf(voiture?.Marque ?: "") }
    var nom by remember { mutableStateOf(voiture?.Nom ?: "") }
    var annee by remember { mutableStateOf(voiture?.Annee ?: "") }
    var couleur by remember { mutableStateOf(voiture?.Couleur ?: "") }
    var energie by remember { mutableStateOf(voiture?.Energie ?: "") }
    var puissance by remember { mutableStateOf(voiture?.Puissance ?: "") }
    var nbPlaces by remember { mutableStateOf(voiture?.NbPlaces ?: "5") }
    var categorie by remember { mutableStateOf(voiture?.Categorie ?: "") }
    var dateAchat by remember { mutableStateOf(voiture?.DateAchat ?: "") }
    var dateDerCT by remember { mutableStateOf(voiture?.DateDernierControleTech ?: "") }
    var dateExpCT by remember { mutableStateOf(voiture?.DateExpirationControleTech ?: "") }
    var prix by remember { mutableStateOf(voiture?.Prix ?: "") }
    var idAssureur by remember { mutableStateOf(voiture?.IdAssureur?.toString() ?: "1") }
    var idFournisseur by remember { mutableStateOf(voiture?.IdFournisseur?.toString() ?: "1") }
    var imagePath by remember { mutableStateOf(voiture?.ImagePath ?: "default_car.jpg") }

    val transmissions = listOf("Propulsion", "Traction", "Intégrale")
    val boites = listOf("Manuelle", "Automatique", "Semi-Manuelle")
    val etats = listOf("Libre", "Louée", "Vendue", "Réparation")

    var transmission by remember { mutableStateOf(voiture?.Transmission ?: transmissions[1]) }
    var boite by remember { mutableStateOf(voiture?.Boite ?: boites[0]) }
    var etat by remember { mutableStateOf(voiture?.Etat ?: etats[0]) }

    val orangePark = Color(0xFFFF5A19)

    Dialog(onDismissRequest = onDismiss) {
        Surface(
            shape = RoundedCornerShape(24.dp),
            color = Color.White,
            modifier = Modifier.fillMaxWidth().fillMaxHeight(0.9f)
        ) {
            Column(
                modifier = Modifier
                    .padding(16.dp)
                    .verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                Text(title, fontSize = 22.sp, fontWeight = FontWeight.Bold)

                FormSectionTitle("Identité", orangePark)
                SimpleField(numSerie, { numSerie = it }, "Numéro de Série", readOnly = voiture != null)
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(marque, { marque = it }, "Marque", Modifier.weight(1f))
                    SimpleField(nom, { nom = it }, "Modèle", Modifier.weight(1f))
                }

                FormSectionTitle("Caractéristiques", orangePark)
                StableDropDown("Boîte", boites, boite) { boite = it }
                StableDropDown("Transmission", transmissions, transmission) { transmission = it }

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(puissance, { puissance = it }, "Puissance", Modifier.weight(1f))
                    SimpleField(energie, { energie = it }, "Énergie", Modifier.weight(1f))
                }

                FormSectionTitle("Prix & Disponibilité", orangePark)
                StableDropDown("État actuel", etats, etat) { etat = it }
                SimpleField(prix, { prix = it }, "Prix / jour (€)")

                FormSectionTitle("Suivi Technique", orangePark)
                SimpleField(dateAchat, { dateAchat = it }, "Date Achat (YYYY-MM-DD)")
                SimpleField(dateDerCT, { dateDerCT = it }, "Dernier CT")
                SimpleField(dateExpCT, { dateExpCT = it }, "Expiration CT")

                Spacer(modifier = Modifier.height(20.dp))

                Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.End) {
                    TextButton(onClick = onDismiss) { Text("Annuler") }
                    Button(
                        onClick = {
                            if (numSerie.isNotBlank()) {
                                onConfirm(Voiture(
                                    NumSerie = numSerie, Energie = energie, NbPlaces = nbPlaces,
                                    Categorie = categorie, Transmission = transmission, Boite = boite,
                                    Etat = etat, Puissance = puissance, DateAchat = dateAchat,
                                    DateExpirationControleTech = dateExpCT, DateDernierControleTech = dateDerCT,
                                    Marque = marque, Nom = nom, Annee = annee,
                                    IdAssureur = idAssureur.toIntOrNull() ?: 1,
                                    IdFournisseur = idFournisseur.toIntOrNull() ?: 1,
                                    ImagePath = imagePath, Couleur = couleur, Prix = prix
                                ))
                            }
                        },
                        colors = ButtonDefaults.buttonColors(containerColor = orangePark),
                        shape = RoundedCornerShape(12.dp)
                    ) {
                        Text("Enregistrer", fontWeight = FontWeight.Bold)
                    }
                }
            }
        }
    }
}

@Composable
fun CarCard(voiture: Voiture, onDeleteClick: () -> Unit, onEditClick: () -> Unit) {
    val imageUrl = "http://10.0.2.2:9990/${voiture.ImagePath}"

    Box(
        modifier = Modifier
            .height(300.dp)
            .fillMaxWidth()
            .clip(RoundedCornerShape(25.dp))
    ) {
        AsyncImage(
            model = imageUrl,
            contentDescription = null,
            contentScale = ContentScale.Crop,
            modifier = Modifier.fillMaxSize()
        )

        // Boutons d'action en haut
        Row(
            modifier = Modifier.align(Alignment.TopEnd).padding(12.dp),
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            IconButton(
                onClick = onEditClick,
                modifier = Modifier.background(Color.White.copy(alpha = 0.8f), CircleShape).size(40.dp)
            ) {
                Icon(Icons.Default.Edit, contentDescription = "Modifier", tint = Color(0xFF2196F3))
            }

            IconButton(
                onClick = onDeleteClick,
                modifier = Modifier.background(Color.White.copy(alpha = 0.8f), CircleShape).size(40.dp)
            ) {
                Icon(Icons.Default.Delete, contentDescription = "Supprimer", tint = Color.Red)
            }
        }

        // Barre d'infos basse
        Column(
            modifier = Modifier
                .align(Alignment.BottomCenter)
                .fillMaxWidth()
                .background(Color.White.copy(alpha = 0.85f))
                .padding(12.dp)
        ) {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Column {
                    Text(text = "${voiture.Marque} ${voiture.Nom}", fontWeight = FontWeight.Bold, fontSize = 18.sp)
                    Text(text = "${voiture.NbPlaces} places • ${voiture.Energie} • ${voiture.Boite}", fontSize = 13.sp, color = Color.Gray)
                }
                Text(text = "${voiture.Prix} €/j", color = Color(0xFFFF5A19), fontWeight = FontWeight.ExtraBold, fontSize = 18.sp)
            }
        }
    }
}

// Les composants utilitaires (Badge, Field, DropDown, Section)
@Composable
fun FormSectionTitle(text: String, color: Color) {
    Text(
        text = text,
        color = color,
        fontWeight = FontWeight.SemiBold,
        fontSize = 14.sp,
        modifier = Modifier.padding(top = 8.dp, bottom = 4.dp)
    )
}

@Composable
fun SimpleField(
    value: String,
    onValueChange: (String) -> Unit,
    label: String,
    modifier: Modifier = Modifier,
    readOnly: Boolean = false
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        singleLine = true,
        readOnly = readOnly,
        colors = OutlinedTextFieldDefaults.colors(
            focusedBorderColor = Color(0xFFFF5A19),
            unfocusedBorderColor = Color.LightGray
        )
    )
}

@Composable
fun FilterBadge(text: String, icon: String) {
    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(12.dp))
            .background(Color.White)
            .padding(horizontal = 12.dp, vertical = 8.dp)
    ) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            Text(text = "$icon ", fontSize = 12.sp)
            Text(text = text, fontSize = 13.sp, fontWeight = FontWeight.Medium, color = Color.Black)
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

    // La Box sert de point d'ancrage pour le menu
    Box(modifier = Modifier.fillMaxWidth()) {
        OutlinedTextField(
            value = selected,
            onValueChange = {},
            readOnly = true,
            label = { Text(label) },
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(12.dp),
            trailingIcon = {
                Icon(Icons.Default.ArrowDropDown, contentDescription = null)
            }
        )

        // Zone cliquable sur tout le champ
        Box(
            modifier = Modifier
                .matchParentSize()
                .clickable { expanded = true }
        )

        // LE MENU DOIT ÊTRE ICI (DANS LA BOX)
        DropdownMenu(
            expanded = expanded,
            onDismissRequest = { expanded = false },
            modifier = Modifier.fillMaxWidth(0.8f)
        ) {
            options.forEach { option ->
                DropdownMenuItem(
                    text = { Text(option) },
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
fun CarDetailsDialog(voiture: Voiture, onDismiss: () -> Unit) {
    Dialog(onDismissRequest = onDismiss) {
        Surface(
            shape = RoundedCornerShape(24.dp),
            color = Color.White,
            modifier = Modifier
                .fillMaxWidth()
                .wrapContentHeight()
        ) {
            Column(
                modifier = Modifier
                    .padding(24.dp)
                    .verticalScroll(rememberScrollState())
            ) {
                // En-tête
                Text(
                    text = "${voiture.Marque} ${voiture.Nom}",
                    fontSize = 24.sp,
                    fontWeight = FontWeight.Bold,
                    color = Color.Black
                )
                Text(
                    text = "Numéro de Série : ${voiture.NumSerie}",
                    fontSize = 14.sp,
                    color = Color.Gray,
                    modifier = Modifier.padding(bottom = 20.dp)
                )

                // Section Caractéristiques
                DetailSectionTitle("Caractéristiques", Color(0xFFFF5A19))
                InfoRow("Énergie", voiture.Energie)
                InfoRow("Boîte", voiture.Boite)
                InfoRow("Puissance", "${voiture.Puissance} CV")
                InfoRow("Places", "${voiture.NbPlaces} places")

                Spacer(modifier = Modifier.height(16.dp))

                // Section État & Prix
                DetailSectionTitle("État & Tarification", Color(0xFFFF5A19))
                InfoRow("Statut actuel", voiture.Etat)
                InfoRow("Prix / Jour", "${voiture.Prix} €")

                Spacer(modifier = Modifier.height(16.dp))

                // Section Technique
                DetailSectionTitle("Suivi Technique", Color(0xFFFF5A19))
                InfoRow("Dernier CT", voiture.DateDernierControleTech)
                InfoRow("Expiration CT", voiture.DateExpirationControleTech)

                Spacer(modifier = Modifier.height(24.dp))

                // Bouton de fermeture
                Button(
                    onClick = onDismiss,
                    modifier = Modifier.fillMaxWidth(),
                    colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFFF5A19)),
                    shape = RoundedCornerShape(12.dp)
                ) {
                    Text("Fermer", fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}

@Composable
fun DetailSectionTitle(text: String, color: Color) {
    Text(
        text = text.uppercase(),
        color = color,
        fontSize = 12.sp,
        fontWeight = FontWeight.ExtraBold,
        letterSpacing = 1.sp,
        modifier = Modifier.padding(bottom = 8.dp)
    )
}

@Composable
fun InfoRow(label: String, value: String) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 6.dp),
        horizontalArrangement = Arrangement.SpaceBetween,
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(
            text = label,
            color = Color.DarkGray,
            fontSize = 15.sp
        )
        Text(
            text = value,
            fontWeight = FontWeight.SemiBold,
            fontSize = 15.sp,
            color = Color.Black
        )
    }
}