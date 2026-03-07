package com.example.rentparkkotlin.ui.userPage

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Delete
import androidx.compose.material.icons.filled.Edit
import androidx.compose.material.icons.filled.Person
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.rentparkkotlin.ui.header.Header

// --- COULEURS ---
val AccentOrange = Color(0xFFE97451)
val DarkBackground = Color(0xFF0F0F0F)
val SurfaceColor = Color(0xFF1A1A1A)
val CardColor = Color(0xFF1E1E1E)
val DeleteRed = Color(0xFFEF5350)

// --- MODÈLE ---
data class User(
    val username: String,
    val role: String
)

// --- ÉCRAN PRINCIPAL ---
@Composable
fun UserManagementScreen() {
    val users = remember {
        mutableStateListOf(
            User("je", "admin"),
            User("clroudier", "admin"),
            User("test", "employe")
        )
    }

    var showAddDialog by remember { mutableStateOf(false) }
    var userToEdit by remember { mutableStateOf<User?>(null) }
    // État pour gérer l'utilisateur en cours de suppression
    var userToDelete by remember { mutableStateOf<User?>(null) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(DarkBackground)
            .padding(16.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(bottom = 16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text("Utilisateurs", color = Color.White, fontSize = 22.sp, fontWeight = FontWeight.Bold)

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

        // Liste des utilisateurs
        Surface(
            modifier = Modifier.fillMaxWidth(),
            color = SurfaceColor,
            shape = RoundedCornerShape(12.dp)
        ) {
            Column(modifier = Modifier.padding(horizontal = 12.dp)) {
                // Table Header
                Row(modifier = Modifier.padding(vertical = 12.dp)) {
                    Text("NOM", Modifier.weight(1.2f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    Text("RÔLE", Modifier.weight(1f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    Text("ACT.", Modifier.weight(0.6f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                }

                HorizontalDivider(color = Color.White.copy(alpha = 0.1f))

                LazyColumn {
                    items(users) { user ->
                        UserListItem(
                            user = user,
                            onEditClick = { userToEdit = user },
                            onDeleteClick = { userToDelete = user } // Ouvre le dialogue de confirmation
                        )
                        HorizontalDivider(color = Color.White.copy(alpha = 0.05f))
                    }
                }
            }
        }
    }

    // Dialogue d'Ajout
    if (showAddDialog) {
        UserFormDialog(
            title = "Nouvel Utilisateur",
            confirmLabel = "AJOUTER",
            onDismiss = { showAddDialog = false },
            onConfirm = { newUser ->
                users.add(newUser)
                showAddDialog = false
            }
        )
    }

    // Dialogue de Modification
    userToEdit?.let { user ->
        UserFormDialog(
            title = "Modifier l'utilisateur",
            confirmLabel = "ENREGISTRER",
            initialUser = user,
            onDismiss = { userToEdit = null },
            onConfirm = { updatedUser ->
                val index = users.indexOf(user)
                if (index != -1) {
                    users[index] = updatedUser
                }
                userToEdit = null
            }
        )
    }

    // Dialogue de Confirmation de Suppression
    userToDelete?.let { user ->
        DeleteConfirmationDialog(
            username = user.username,
            onDismiss = { userToDelete = null },
            onConfirm = {
                users.remove(user)
                userToDelete = null
            }
        )
    }
}

// --- COMPOSANT DE FORMULAIRE ---
@Composable
fun UserFormDialog(
    title: String,
    confirmLabel: String,
    initialUser: User? = null,
    onDismiss: () -> Unit,
    onConfirm: (User) -> Unit
) {
    var name by remember { mutableStateOf(initialUser?.username ?: "") }
    var role by remember { mutableStateOf(initialUser?.role ?: "") }
    var mdp by remember { mutableStateOf("") }

    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = CardColor,
        title = { Text(title, color = Color.White, fontSize = 18.sp) },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Nom d'utilisateur", color = Color.Gray) },
                    textStyle = TextStyle(color = Color.White),
                    singleLine = true
                )
                OutlinedTextField(
                    value = mdp,
                    onValueChange = { mdp = it },
                    label = { Text(if (initialUser == null) "Mot de passe" else "Nouveau mdp (optionnel)", color = Color.Gray) },
                    textStyle = TextStyle(color = Color.White),
                    singleLine = true
                )
                OutlinedTextField(
                    value = role,
                    onValueChange = { role = it },
                    label = { Text("Rôle (admin, employe...)", color = Color.Gray) },
                    textStyle = TextStyle(color = Color.White),
                    singleLine = true
                )
            }
        },
        confirmButton = {
            TextButton(
                onClick = { onConfirm(User(name, role)) },
                enabled = name.isNotBlank() && role.isNotBlank()
            ) {
                Text(confirmLabel, color = AccentOrange, fontWeight = FontWeight.Bold)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("ANNULER", color = Color.Gray)
            }
        }
    )
}

// --- NOUVEAU : DIALOGUE DE SUPPRESSION ---
@Composable
fun DeleteConfirmationDialog(
    username: String,
    onDismiss: () -> Unit,
    onConfirm: () -> Unit
) {
    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = CardColor,
        title = { Text("Confirmer la suppression", color = Color.White) },
        text = {
            Text(
                "Voulez-vous vraiment supprimer l'utilisateur $username ? Cette action est définitive.",
                color = Color.LightGray
            )
        },
        confirmButton = {
            TextButton(onClick = onConfirm) {
                Text("SUPPRIMER", color = DeleteRed, fontWeight = FontWeight.Bold)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("ANNULER", color = Color.Gray)
            }
        }
    )
}

// --- COMPOSANT DE LIGNE ---
@Composable
fun UserListItem(
    user: User,
    onEditClick: () -> Unit,
    onDeleteClick: () -> Unit
) {
    Row(
        modifier = Modifier.fillMaxWidth().padding(vertical = 12.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Text(user.username, Modifier.weight(1.2f), color = Color.White, fontSize = 14.sp, fontWeight = FontWeight.Medium)

        Box(Modifier.weight(1f)) {
            Surface(color = Color(0xFF2A2A2A), shape = RoundedCornerShape(4.dp)) {
                Text(user.role, Modifier.padding(horizontal = 8.dp, vertical = 2.dp), color = Color.LightGray, fontSize = 11.sp)
            }
        }

        Row(modifier = Modifier.weight(0.6f), horizontalArrangement = Arrangement.End) {
            IconButton(onClick = onEditClick, modifier = Modifier.size(24.dp)) {
                Icon(Icons.Default.Edit, null, tint = Color.Gray, modifier = Modifier.size(18.dp))
            }
            Spacer(Modifier.width(8.dp))
            IconButton(onClick = onDeleteClick, modifier = Modifier.size(24.dp)) {
                Icon(Icons.Default.Delete, null, tint = Color.Gray, modifier = Modifier.size(18.dp))
            }
        }
    }
}