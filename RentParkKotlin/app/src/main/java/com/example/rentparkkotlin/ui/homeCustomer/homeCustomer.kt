package com.example.rentparkkotlin.ui.homeCustomer

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.DateRange
import androidx.compose.material.icons.filled.Menu
import androidx.compose.material.icons.filled.Person
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

@Composable
fun RentParkScreen() {
    Box(modifier = Modifier.fillMaxSize().background(Color.Black)) {

        // --- 1. Image de fond (Voiture) avec dégradé noir ---
        // Remplace 'R.drawable.car_bg' par ton image réelle
        Box(modifier = Modifier.fillMaxSize()) {
            // Image ici si disponible
            Box(
                modifier = Modifier
                    .fillMaxSize()
                    .background(
                        Brush.verticalGradient(
                            colors = listOf(Color.Transparent, Color.Black),
                            startY = 300f
                        )
                    )
            )
        }

        // --- 2. Interface de Navigation (Top Bar) ---
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(top = 48.dp, start = 20.dp, end = 20.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Icon(Icons.Default.Menu, contentDescription = null, tint = Color.White)
            Surface(
                color = Color.White.copy(alpha = 0.1f),
                shape = RoundedCornerShape(50.dp),
                border = ButtonDefaults.outlinedButtonBorder
            ) {
                Row(modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp), verticalAlignment = Alignment.CenterVertically) {
                    Icon(Icons.Default.Person, contentDescription = null, tint = Color.White, modifier = Modifier.size(16.dp))
                    Spacer(Modifier.width(8.dp))
                    Text("Connexion", color = Color.White, fontSize = 12.sp)
                }
            }
        }

        // --- 3. Contenu Central ---
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            Text(
                text = "RENTPARK",
                fontSize = 48.sp,
                fontWeight = FontWeight.ExtraBold,
                color = Color.White,
                modifier = Modifier.padding(bottom = 40.dp)
            )

            // --- Carte de réservation Verticale ---
            Surface(
                modifier = Modifier.fillMaxWidth(),
                color = Color(0xFF1A1A1A).copy(alpha = 0.85f),
                shape = RoundedCornerShape(28.dp)
            ) {
                Column(
                    modifier = Modifier.padding(24.dp),
                    verticalArrangement = Arrangement.spacedBy(20.dp)
                ) {
                    VerticalDateField(label = "Date de départ")
                    VerticalDateField(label = "Date de retour")

                    Button(
                        onClick = { /* Action */ },
                        modifier = Modifier.fillMaxWidth().height(60.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFF16937)),
                        shape = RoundedCornerShape(16.dp)
                    ) {
                        Text("Voir les véhicules", fontSize = 16.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun VerticalDateField(label: String) {
    Column {
        Text(label, color = Color.Gray, fontSize = 14.sp, modifier = Modifier.padding(bottom = 8.dp))
        OutlinedTextField(
            value = "jj/mm/aaaa",
            onValueChange = {},
            modifier = Modifier.fillMaxWidth(),
            trailingIcon = { Icon(Icons.Default.DateRange, contentDescription = null, tint = Color.Gray) },
            shape = RoundedCornerShape(12.dp),
            readOnly = true
        )
    }
}