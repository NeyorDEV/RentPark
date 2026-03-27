package com.example.rentparkkotlin.ui.userPage

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material.icons.filled.Person
import androidx.compose.material3.*
import androidx.compose.material3.TabRowDefaults.tabIndicatorOffset
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
import com.example.rentparkkotlin.model.ClientRequest
import com.example.rentparkkotlin.model.User
import com.example.rentparkkotlin.ui.header.Header
import com.example.rentparkkotlin.viewmodel.ClientViewModel
import com.example.rentparkkotlin.viewmodel.UserViewModel

val AccentOrange = Color(0xFFE97451)
val DarkBackground = Color(0xFF0F0F0F)
val SurfaceColor = Color(0xFF1A1A1A)
val CardColor = Color(0xFF1E1E1E)
val DeleteRed = Color(0xFFEF5350)

@Composable
fun UserManagementScreen(
    userViewModel: UserViewModel = viewModel(),
    clientViewModel: ClientViewModel = viewModel()
) {
    var selectedTabIndex by remember { mutableStateOf(0) }
    val tabs = listOf("Utilisateurs", "Clients")

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(DarkBackground)
            .padding(16.dp)
    ) {
        TabRow(
            selectedTabIndex = selectedTabIndex,
            containerColor = Color.Transparent,
            contentColor = AccentOrange,
            indicator = { tabPositions ->
                TabRowDefaults.Indicator(
                    Modifier.tabIndicatorOffset(tabPositions[selectedTabIndex]),
                    color = AccentOrange
                )
            }
        ) {
            tabs.forEachIndexed { index, title ->
                Tab(
                    selected = selectedTabIndex == index,
                    onClick = { selectedTabIndex = index },
                    text = {
                        Text(
                            title,
                            color = if (selectedTabIndex == index) AccentOrange else Color.Gray,
                            fontWeight = FontWeight.Bold
                        )
                    }
                )
            }
        }

        Spacer(modifier = Modifier.height(16.dp))

        when (selectedTabIndex) {
            0 -> UsersContent(userViewModel)
            1 -> ClientsContent(clientViewModel)
        }
    }
}

@Composable
fun UsersContent(viewModel: UserViewModel) {
    val users = viewModel.users
    val isLoading = viewModel.isLoading
    val error = viewModel.error

    var showAddDialog by remember { mutableStateOf(false) }
    var userToEdit by remember { mutableStateOf<User?>(null) }
    var userToDelete by remember { mutableStateOf<User?>(null) }

    Column(modifier = Modifier.fillMaxSize()) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(bottom = 16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text("Liste des Utilisateurs", color = Color.White, fontSize = 20.sp, fontWeight = FontWeight.Bold)

            Button(
                onClick = { showAddDialog = true },
                colors = ButtonDefaults.buttonColors(containerColor = AccentOrange),
                shape = RoundedCornerShape(8.dp)
            ) {
                Icon(Icons.Default.Person, contentDescription = null, modifier = Modifier.size(18.dp))
                Spacer(Modifier.width(6.dp))
                Text("Ajouter", fontSize = 13.sp)
            }
        }

        if (error != null) Text(text = error, color = DeleteRed, modifier = Modifier.padding(bottom = 8.dp))

        Surface(modifier = Modifier.fillMaxSize(), color = SurfaceColor, shape = RoundedCornerShape(12.dp)) {
            if (isLoading) {
                Box(contentAlignment = Alignment.Center) { CircularProgressIndicator(color = AccentOrange) }
            } else {
                Column(modifier = Modifier.padding(horizontal = 12.dp)) {
                    Row(modifier = Modifier.padding(vertical = 12.dp)) {
                        Text("NOM", Modifier.weight(1.2f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        Text("RÔLE", Modifier.weight(1f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        Text("ACT.", Modifier.weight(0.6f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    }
                    HorizontalDivider(color = Color.White.copy(alpha = 0.1f))

                    LazyColumn {
                        items(users) { user ->
                            Row(modifier = Modifier.fillMaxWidth().padding(vertical = 12.dp), verticalAlignment = Alignment.CenterVertically) {
                                Text(user.username, Modifier.weight(1.2f), color = Color.White, fontSize = 14.sp)
                                Box(Modifier.weight(1f)) {
                                    Surface(color = Color(0xFF2A2A2A), shape = RoundedCornerShape(4.dp)) {
                                        Text(user.role, Modifier.padding(horizontal = 8.dp, vertical = 2.dp), color = Color.LightGray, fontSize = 11.sp)
                                    }
                                }
                                Row(modifier = Modifier.weight(0.6f), horizontalArrangement = Arrangement.End) {
                                    IconButton(onClick = { userToEdit = user }, modifier = Modifier.size(24.dp)) {
                                        Icon(Icons.Default.Edit, null, tint = Color.Gray, modifier = Modifier.size(18.dp))
                                    }
                                    Spacer(Modifier.width(8.dp))
                                    IconButton(onClick = { userToDelete = user }, modifier = Modifier.size(24.dp)) {
                                        Icon(Icons.Default.Delete, null, tint = Color.Gray, modifier = Modifier.size(18.dp))
                                    }
                                }
                            }
                            HorizontalDivider(color = Color.White.copy(alpha = 0.05f))
                        }
                    }
                }
            }
        }
    }

    if (showAddDialog) {
        UserFormDialog(title = "Nouvel Utilisateur", confirmLabel = "AJOUTER", onDismiss = { showAddDialog = false }) { username, role, mdp ->
            viewModel.addUser(username, role, mdp)
            showAddDialog = false
        }
    }
    userToEdit?.let { user ->
        UserFormDialog(title = "Modifier", confirmLabel = "ENREGISTRER", initialUser = user, onDismiss = { userToEdit = null }) { username, role, mdp ->
            viewModel.updateUser(user.id, username, role, mdp)
            userToEdit = null
        }
    }
    userToDelete?.let { user ->
        AlertDialog(
            onDismissRequest = { userToDelete = null }, containerColor = CardColor,
            title = { Text("Confirmer", color = Color.White) },
            text = { Text("Supprimer ${user.username} ?", color = Color.LightGray) },
            confirmButton = { TextButton(onClick = { viewModel.deleteUser(user.id); userToDelete = null }) { Text("SUPPRIMER", color = DeleteRed) } },
            dismissButton = { TextButton(onClick = { userToDelete = null }) { Text("ANNULER", color = Color.Gray) } }
        )
    }
}

@Composable
fun ClientsContent(viewModel: ClientViewModel) {
    val clients = viewModel.clients
    val isLoading = viewModel.isLoading
    val error = viewModel.error

    var showClientDialog by remember { mutableStateOf(false) }
    var clientToEdit by remember { mutableStateOf<Client?>(null) }

    Column(modifier = Modifier.fillMaxSize()) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(bottom = 16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text("Liste des Clients", color = Color.White, fontSize = 20.sp, fontWeight = FontWeight.Bold)

            Button(
                onClick = { showClientDialog = true },
                colors = ButtonDefaults.buttonColors(containerColor = AccentOrange),
                shape = RoundedCornerShape(8.dp)
            ) {
                Icon(Icons.Default.Person, contentDescription = null, modifier = Modifier.size(18.dp))
                Spacer(Modifier.width(6.dp))
                Text("Nouveau", fontSize = 13.sp)
            }
        }

        if (error != null) Text(text = error, color = DeleteRed, modifier = Modifier.padding(bottom = 8.dp))

        Surface(modifier = Modifier.fillMaxSize(), color = SurfaceColor, shape = RoundedCornerShape(12.dp)) {
            if (isLoading) {
                Box(contentAlignment = Alignment.Center) { CircularProgressIndicator(color = AccentOrange) }
            } else {
                Column(modifier = Modifier.padding(horizontal = 12.dp)) {
                    Row(modifier = Modifier.padding(vertical = 12.dp)) {
                        Text("NOM & PRÉNOM", Modifier.weight(1.2f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        Text("TÉLÉPHONE", Modifier.weight(1f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                        Text("ACT.", Modifier.weight(0.4f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    }
                    HorizontalDivider(color = Color.White.copy(alpha = 0.1f))

                    LazyColumn {
                        items(clients) { client ->
                            Row(modifier = Modifier.fillMaxWidth().padding(vertical = 12.dp), verticalAlignment = Alignment.CenterVertically) {
                                Column(Modifier.weight(1.2f)) {
                                    Text(client.nom.uppercase() + " " + client.prenom, color = Color.White, fontSize = 14.sp, fontWeight = FontWeight.Bold)
                                    Text(client.email, color = Color.Gray, fontSize = 12.sp)
                                }
                                Text(client.numTel ?: "N/C", Modifier.weight(1f), color = Color.LightGray, fontSize = 13.sp)
                                Row(modifier = Modifier.weight(0.4f), horizontalArrangement = Arrangement.End) {
                                    IconButton(onClick = { clientToEdit = client }, modifier = Modifier.size(24.dp)) {
                                        Icon(Icons.Default.Edit, null, tint = Color.Gray, modifier = Modifier.size(18.dp))
                                    }
                                }
                            }
                            HorizontalDivider(color = Color.White.copy(alpha = 0.05f))
                        }
                    }
                }
            }
        }
    }

    if (showClientDialog || clientToEdit != null) {
        ClientFormDialog(
            initialClient = clientToEdit,
            onDismiss = { showClientDialog = false; clientToEdit = null },
            onConfirm = { req ->
                if (clientToEdit == null) viewModel.addClient(req) else viewModel.updateClient(clientToEdit!!.idClient, req)
                showClientDialog = false
                clientToEdit = null
            }
        )
    }
}

@Composable
fun ClientFormDialog(initialClient: Client?, onDismiss: () -> Unit, onConfirm: (ClientRequest) -> Unit) {
    var nom by remember { mutableStateOf(initialClient?.nom ?: "") }
    var prenom by remember { mutableStateOf(initialClient?.prenom ?: "") }
    var email by remember { mutableStateOf(initialClient?.email ?: "") }
    var tel by remember { mutableStateOf(initialClient?.numTel ?: "") }
    var permis by remember { mutableStateOf(initialClient?.numPermis ?: "") }
    var dateNaiss by remember { mutableStateOf(initialClient?.dateNaiss ?: "") }
    var nationalite by remember { mutableStateOf(initialClient?.nationalite ?: "") }

    AlertDialog(
        onDismissRequest = onDismiss, containerColor = CardColor,
        title = { Text(if (initialClient == null) "Nouveau Client" else "Modifier Client", color = Color.White) },
        text = {
            Column(
                modifier = Modifier.verticalScroll(rememberScrollState()),
                verticalArrangement = Arrangement.spacedBy(10.dp)
            ) {
                OutlinedTextField(value = nom, onValueChange = { nom = it }, label = { Text("Nom", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
                OutlinedTextField(value = prenom, onValueChange = { prenom = it }, label = { Text("Prénom", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
                OutlinedTextField(value = email, onValueChange = { email = it }, label = { Text("Email", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
                OutlinedTextField(value = tel, onValueChange = { tel = it }, label = { Text("Téléphone", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
                OutlinedTextField(value = permis, onValueChange = { permis = it }, label = { Text("Numéro Permis", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
                OutlinedTextField(value = dateNaiss, onValueChange = { dateNaiss = it }, label = { Text("Date Naissance (AAAA-MM-JJ)", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
                OutlinedTextField(value = nationalite, onValueChange = { nationalite = it }, label = { Text("Nationalité", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
            }
        },
        confirmButton = {
            TextButton(
                onClick = { onConfirm(ClientRequest(nom, prenom, email, tel, permis, dateNaiss, nationalite)) },
                enabled = nom.isNotBlank() && prenom.isNotBlank() && email.isNotBlank() && permis.isNotBlank()
            ) { Text("ENREGISTRER", color = AccentOrange, fontWeight = FontWeight.Bold) }
        },
        dismissButton = { TextButton(onClick = onDismiss) { Text("ANNULER", color = Color.Gray) } }
    )
}

@Composable
fun UserFormDialog(title: String, confirmLabel: String, initialUser: User? = null, onDismiss: () -> Unit, onConfirm: (String, String, String) -> Unit) {
    var name by remember { mutableStateOf(initialUser?.username ?: "") }
    var role by remember { mutableStateOf(initialUser?.role ?: "") }
    var mdp by remember { mutableStateOf("") }
    AlertDialog(
        onDismissRequest = onDismiss, containerColor = CardColor,
        title = { Text(title, color = Color.White, fontSize = 18.sp) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                OutlinedTextField(value = name, onValueChange = { name = it }, label = { Text("Nom d'utilisateur", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
                OutlinedTextField(value = mdp, onValueChange = { mdp = it }, label = { Text(if (initialUser == null) "Mot de passe" else "Nouveau mdp (optionnel)", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
                OutlinedTextField(value = role, onValueChange = { role = it }, label = { Text("Rôle", color = Color.Gray) }, textStyle = TextStyle(color = Color.White), singleLine = true)
            }
        },
        confirmButton = {
            TextButton(
                onClick = { onConfirm(name, role, mdp) },
                enabled = name.isNotBlank() && role.isNotBlank() && (initialUser != null || mdp.isNotBlank())
            ) { Text(confirmLabel, color = AccentOrange, fontWeight = FontWeight.Bold) }
        },
        dismissButton = { TextButton(onClick = onDismiss) { Text("ANNULER", color = Color.Gray) } }
    )
}