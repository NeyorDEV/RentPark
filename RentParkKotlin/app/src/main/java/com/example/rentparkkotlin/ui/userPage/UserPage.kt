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
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

// Couleurs personnalisées basées sur l'image
val BackgroundDark = Color(0xFF121212)
val SurfaceDark = Color(0xFF1E1E1E)
val AccentOrange = Color(0xFFE97451)
val RoleBadgeColor = Color(0xFF2D2D2D)

data class User(
    val username: String,
    val role: String
)

@Composable
fun UserManagementScreen() {
    val users = listOf(
        User("je", "admin"),
        User("clroudier", "admin"),
        User("qumiotto", "admin"),
        User("altixier", "admin"),
        User("dagauthier", "admin"),
        User("angrimaud", "admin"),
        User("racaumond", "admin"),
        User("test", "employe"),
        User("te", "employe")
    )

    Column(
        modifier = Modifier
            .fillMaxSize()
            .background(Color(0xFF0F0F0F)) // Fond très sombre
            .padding(16.dp)
    ) {
        // --- HEADER COMPACT ---
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(bottom = 16.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Text(
                text = "Utilisateurs", // Titre plus court
                color = Color.White,
                fontSize = 22.sp, // Taille réduite
                fontWeight = FontWeight.Bold
            )

            // Bouton plus compact (icône seule ou texte court)
            Button(
                onClick = { /* Action */ },
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFE97451)),
                contentPadding = PaddingValues(horizontal = 12.dp, vertical = 8.dp),
                shape = RoundedCornerShape(8.dp)
            ) {
                Icon(Icons.Default.Person, contentDescription = null, modifier = Modifier.size(18.dp))
                Spacer(Modifier.width(6.dp))
                Text("Ajouter", fontSize = 13.sp)
            }
        }

        // --- LISTE / TABLE ---
        Surface(
            modifier = Modifier.fillMaxWidth(),
            color = Color(0xFF1A1A1A),
            shape = RoundedCornerShape(12.dp)
        ) {
            Column(modifier = Modifier.padding(horizontal = 12.dp)) {

                // En-tête de colonnes (Username / Rôle / Actions)
                Row(
                    modifier = Modifier.padding(vertical = 12.dp),
                    verticalAlignment = Alignment.CenterVertically
                ) {
                    Text("NOM", Modifier.weight(1.2f), color = Color(0xFFE97451), fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    Text("RÔLE", Modifier.weight(1f), color = Color(0xFFE97451), fontSize = 11.sp, fontWeight = FontWeight.Bold)
                    Text("ACT.", Modifier.weight(0.6f), color = Color(0xFFE97451), fontSize = 11.sp, fontWeight = FontWeight.Bold)
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
}

@Composable
fun UserListItem(user: User) {
    Row(
        modifier = Modifier
            .fillMaxWidth()
            .padding(vertical = 12.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        // Username (Petit et gras)
        Text(
            text = user.username,
            modifier = Modifier.weight(1.2f),
            color = Color.White,
            fontSize = 14.sp,
            fontWeight = FontWeight.Medium
        )

        // Badge de rôle compact
        Box(Modifier.weight(1f)) {
            Surface(
                color = Color(0xFF2A2A2A),
                shape = RoundedCornerShape(4.dp)
            ) {
                Text(
                    text = user.role,
                    modifier = Modifier.padding(horizontal = 8.dp, vertical = 2.dp),
                    color = Color.LightGray,
                    fontSize = 11.sp
                )
            }
        }

        // Actions resserrées
        Row(
            modifier = Modifier.weight(0.6f),
            horizontalArrangement = Arrangement.End
        ) {
            Icon(
                Icons.Default.Edit,
                contentDescription = null,
                tint = Color.Gray,
                modifier = Modifier.size(18.dp)
            )
            Spacer(Modifier.width(12.dp))
            Icon(
                Icons.Default.Delete,
                contentDescription = null,
                tint = Color.Gray,
                modifier = Modifier.size(18.dp)
            )
        }
    }
}