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
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.window.Dialog

// Couleurs (gardées de ton code original)
val AccentOrange = Color(0xFFE97451)

data class User(
    val username: String,
    val role: String
)

@Composable
fun UserManagementScreen() {
    val users = remember {
        mutableStateListOf(
            User("je", "admin"),
            User("clroudier", "admin"),
            User("test", "employe")
        )
    }

    var showDialog by remember { mutableStateOf(false) }

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0F0F0F))
            .padding(16.dp)
    ) {
        Row(
            modifier = Modifier.fillMaxWidth().padding(bottom = 16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text("Utilisateurs", color = Color.White, fontSize = 22.sp, fontWeight = FontWeight.Bold)

            Button(
                onClick = { showDialog = true }, // Ouvre le formulaire
                colors = ButtonDefaults.buttonColors(containerColor = AccentOrange),
                shape = RoundedCornerShape(8.dp)
            ) {
                Icon(Icons.Default.Person, contentDescription = null, modifier = Modifier.size(18.dp))
                Spacer(Modifier.width(6.dp))
                Text("Ajouter", fontSize = 13.sp)
            }
        }

        Surface(
            modifier = Modifier.fillMaxWidth(),
            color = Color(0xFF1A1A1A),
            shape = RoundedCornerShape(12.dp)
        ) {
            Column(modifier = Modifier.padding(horizontal = 12.dp)) {
                // En-tête (Username / Rôle / Actions)
                Row(modifier = Modifier.padding(vertical = 12.dp)) {
                    Text("NOM", Modifier.weight(1.2f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    Text("RÔLE", Modifier.weight(1f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    Text("ACT.", Modifier.weight(0.6f), color = AccentOrange, fontSize = 11.sp, fontWeight = FontWeight.Bold)
                }

                HorizontalDivider(color = Color.White.copy(alpha = 0.1f))

                LazyColumn {
                    items(users) { user ->
                        UserListItem(user)
                        HorizontalDivider(color = Color.White.copy(alpha = 0.05f))
                    }
                }
            }
        }
    }

    if (showDialog) {
        AddUserForm(
            onDismiss = { showDialog = false },
            onUserAdded = { newUser ->
                users.add(newUser)
                showDialog = false
            }
        )
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun AddUserForm(onDismiss: () -> Unit, onUserAdded: (User) -> Unit) {
    var name by remember { mutableStateOf("") }
    var role by remember { mutableStateOf("") }
    var mdp by remember { mutableStateOf("") }
    AlertDialog(
        onDismissRequest = onDismiss,
        containerColor = Color(0xFF1E1E1E),
        title = {
            Text("Nouvel Utilisateur", color = Color.White, fontSize = 18.sp)
        },
        text = {
            Column(verticalArrangement = Arrangement.spacedBy(12.dp)) {
                OutlinedTextField(
                    value = name,
                    onValueChange = { name = it },
                    label = { Text("Nom d'utilisateur", color = Color.Gray) },
                    textStyle = androidx.compose.ui.text.TextStyle(color = Color.White),

                )
                OutlinedTextField(
                    value = mdp,
                    onValueChange = { mdp = it },
                    label = { Text("Mot de passe", color = Color.Gray) },
                    textStyle = androidx.compose.ui.text.TextStyle(color = Color.White),
                    )

                OutlinedTextField(
                    value = role,
                    onValueChange = { role = it },
                    label = { Text("Rôle (admin, employe...)", color = Color.Gray) },
                    textStyle = androidx.compose.ui.text.TextStyle(color = Color.White),

                )
            }
        },
        confirmButton = {
            TextButton(
                onClick = { if(name.isNotBlank()) onUserAdded(User(name, role)) },
                enabled = name.isNotBlank() && role.isNotBlank()
            ) {
                Text("AJOUTER", color = AccentOrange, fontWeight = FontWeight.Bold)
            }
        },
        dismissButton = {
            TextButton(onClick = onDismiss) {
                Text("ANNULER", color = Color.Gray)
            }
        }
    )
}

@Composable
fun UserListItem(user: User) {
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
            Icon(Icons.Default.Edit, null, tint = Color.Gray, modifier = Modifier.size(18.dp))
            Spacer(Modifier.width(12.dp))
            Icon(Icons.Default.Delete, null, tint = Color.Gray, modifier = Modifier.size(18.dp))
        }
    }
}