package com.example.rentparkkotlin.ui.carsPage

import androidx.compose.foundation.background
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
import androidx.compose.material.icons.filled.Delete
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

    // États pour les modales
    var showAddDialog by remember { mutableStateOf(false) }
    var carToDelete by remember { mutableStateOf<Voiture?>(null) }

    val orangeColor = Color(0xFFFF5A19)


    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFFF8F9FA))

    ) {
        Header("Voitures", onMenuClick = {})
        // 1. Barre de Recherche + Bouton Ajouter
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(horizontal = 16.dp, vertical = 10.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(8.dp)
        ) {
            Box(
                modifier = Modifier
                    .weight(1f)
                    .clip(CircleShape)
                    .background(Color.White)
                    .padding(15.dp)
            ) {
                Text("Rechercher une voiture...", color = Color.Gray)
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
                    items(voitures) { voiture ->
                        CarCard(
                            voiture = voiture,
                            onDeleteClick = { carToDelete = voiture }
                        )
                    }
                }
            }
        }
    }

    // Modale d'ajout
    if (showAddDialog) {
        AddCarsForm(
            onDismiss = { showAddDialog = false },
            onConfirm = { newVoiture ->
                viewModel.addVoiture(newVoiture)
                showAddDialog = false
            }
        )
    }

    // Modale de confirmation de suppression
    if (carToDelete != null) {
        AlertDialog(
            onDismissRequest = { carToDelete = null },
            title = { Text("Supprimer le véhicule") },
            text = { Text("Voulez-vous vraiment supprimer la ${carToDelete?.Marque} ${carToDelete?.Nom} ? Cette action est irréversible.") },
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
                TextButton(onClick = { carToDelete = null }) {
                    Text("Annuler")
                }
            }
        )
    }
}

@Composable
fun AddCarsForm(onDismiss: () -> Unit, onConfirm: (Voiture) -> Unit) {
    // 1. IDENTIFICATION
    var numSerie by remember { mutableStateOf("") }
    var marque by remember { mutableStateOf("") }
    var nom by remember { mutableStateOf("") }
    var annee by remember { mutableStateOf("") }
    var couleur by remember { mutableStateOf("") }

    // 2. TECHNIQUE
    var energie by remember { mutableStateOf("") }
    var puissance by remember { mutableStateOf("") }
    var nbPlaces by remember { mutableStateOf("5") }
    var categorie by remember { mutableStateOf("") }

    val transmissions = listOf("Propulsion", "Traction", "Intégrale")
    val boites = listOf("Manuelle", "Automatique", "Semi-Manuelle")
    val etats = listOf("Libre", "Louée", "Vendue", "Réparation")

    var transmission by remember { mutableStateOf(transmissions[1]) }
    var boite by remember { mutableStateOf(boites[0]) }
    var etat by remember { mutableStateOf(etats[0]) }

    // 3. DATES (Format AAAA-MM-JJ attendu par SQL)
    var dateAchat by remember { mutableStateOf("") }
    var dateDerCT by remember { mutableStateOf("") }
    var dateExpCT by remember { mutableStateOf("") }

    // 4. ADMIN & FINANCE
    var prix by remember { mutableStateOf("") }
    var idAssureur by remember { mutableStateOf("1") }
    var idFournisseur by remember { mutableStateOf("1") }
    var imagePath by remember { mutableStateOf("default_car.jpg") }

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
                Text("Nouveau Véhicule", fontSize = 22.sp, fontWeight = FontWeight.Bold)

                // --- IDENTITÉ ---
                FormSectionTitle("Identité", orangePark)
                SimpleField(numSerie, { numSerie = it }, "Numéro de Série (Obligatoire)")
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(marque, { marque = it }, "Marque", Modifier.weight(1f))
                    SimpleField(nom, { nom = it }, "Modèle", Modifier.weight(1f))
                }
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(annee, { annee = it }, "Année", Modifier.weight(1f))
                    SimpleField(couleur, { couleur = it }, "Couleur", Modifier.weight(1f))
                }

                // --- TECHNIQUE ---
                FormSectionTitle("Caractéristiques", orangePark)
                StableDropDown("Boîte", boites, boite) { boite = it }
                StableDropDown("Transmission", transmissions, transmission) { transmission = it }

                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(puissance, { puissance = it }, "Puissance (ch)", Modifier.weight(1f))
                    SimpleField(energie, { energie = it }, "Énergie", Modifier.weight(1f))
                }
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(nbPlaces, { nbPlaces = it }, "Places", Modifier.weight(1f))
                    SimpleField(categorie, { categorie = it }, "Catégorie", Modifier.weight(1f))
                }

                // --- STATUT ET PRIX ---
                FormSectionTitle("Prix & Disponibilité", orangePark)
                StableDropDown("État actuel", etats, etat) { etat = it }
                SimpleField(prix, { prix = it }, "Prix / jour (€)")

                // --- DATES (Note : Assure-toi que l'utilisateur saisit YYYY-MM-DD) ---
                FormSectionTitle("Suivi Technique", orangePark)
                SimpleField(dateAchat, { dateAchat = it }, "Date Achat (YYYY-MM-DD)")
                SimpleField(dateDerCT, { dateDerCT = it }, "Dernier CT (YYYY-MM-DD)")
                SimpleField(dateExpCT, { dateExpCT = it }, "Expiration CT (YYYY-MM-DD)")

                // --- ADMIN ---
                FormSectionTitle("Administration", orangePark)
                SimpleField(imagePath, { imagePath = it }, "Nom de l'image")
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    SimpleField(idAssureur, { idAssureur = it }, "ID Assureur", Modifier.weight(1f))
                    SimpleField(idFournisseur, { idFournisseur = it }, "ID Fournisseur", Modifier.weight(1f))
                }

                Spacer(modifier = Modifier.height(20.dp))

                Button(
                    onClick = {
                        // On vérifie le champ obligatoire défini dans ton PHP
                        if (numSerie.isNotBlank()) {
                            val v = Voiture(
                                NumSerie = numSerie,
                                Energie = energie,
                                NbPlaces = nbPlaces,
                                Categorie = categorie,
                                Transmission = transmission,
                                Boite = boite,
                                Etat = etat,
                                Puissance = puissance,
                                DateAchat = dateAchat,
                                DateExpirationControleTech = dateExpCT,
                                DateDernierControleTech = dateDerCT,
                                Marque = marque,
                                Nom = nom,
                                Annee = annee,
                                IdAssureur = idAssureur.toIntOrNull() ?: 1,
                                IdFournisseur = idFournisseur.toIntOrNull() ?: 1,
                                ImagePath = imagePath,
                                Couleur = couleur,
                                Prix = prix
                            )
                            onConfirm(v)
                        }
                    },
                    modifier = Modifier.fillMaxWidth(),
                    colors = ButtonDefaults.buttonColors(containerColor = orangePark),
                    shape = RoundedCornerShape(12.dp),
                    enabled = numSerie.isNotBlank() // Désactivé si NumSerie est vide
                ) {
                    Text("Enregistrer", fontWeight = FontWeight.Bold)
                }
            }
        }
    }
}

@Composable
fun FormSectionTitle(text: String, color: Color) {
    Text(text, color = color, fontWeight = FontWeight.SemiBold, fontSize = 14.sp, modifier = Modifier.padding(top = 8.dp))
}
@Composable
fun StableDropDown(label: String, options: List<String>, selected: String, onSelect: (String) -> Unit) {
    var expanded by remember { mutableStateOf(false) }

    Box(modifier = Modifier.fillMaxWidth()) {
        OutlinedTextField(
            value = selected,
            onValueChange = {},
            readOnly = true,
            label = { Text(label) },
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(12.dp),
            trailingIcon = {
                // Icône simple pour indiquer le menu
                TextButton(onClick = { expanded = true }) {
                    Text("▼", color = Color.Gray)
                }
            }
        )
        // Le clic sur le champ ouvre aussi le menu
        TextButton(
            onClick = { expanded = true },
            modifier = Modifier.matchParentSize()
        ) { }

        DropdownMenu(
            expanded = expanded,
            onDismissRequest = { expanded = false },
            modifier = Modifier.fillMaxWidth(0.7f) // Un peu moins large que l'écran
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
fun SimpleField(value: String, onValueChange: (String) -> Unit, label: String, modifier: Modifier = Modifier) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        singleLine = true
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
fun CarCard(voiture: Voiture, onDeleteClick: () -> Unit) {
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

        // Bouton Supprimer (Icône Poubelle)
        IconButton(
            onClick = onDeleteClick,
            modifier = Modifier
                .align(Alignment.TopEnd)
                .padding(12.dp)
                .background(Color.White.copy(alpha = 0.7f), CircleShape)
        ) {
            Icon(
                imageVector = Icons.Default.Delete,
                contentDescription = "Supprimer",
                tint = Color.Red
            )
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
                    Text(text = "${voiture.NbPlaces} places • ${voiture.Energie}", fontSize = 13.sp, color = Color.Gray)
                }
                Text(text = "${voiture.Prix} €/j", color = Color(0xFFFF5A19), fontWeight = FontWeight.ExtraBold, fontSize = 18.sp)
            }
        }
    }
}