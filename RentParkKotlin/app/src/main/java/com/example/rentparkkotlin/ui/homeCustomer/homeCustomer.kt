package com.example.rentparkkotlin.ui.homeCustomer

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

// Couleurs personnalisées basées sur ton image
val DarkBackground = Color(0xFF000000)
val CardBackground = Color(0xFF1A1A1A).copy(alpha = 0.8f) // Effet translucide
val AccentOrange = Color(0xFFF16937)

@Composable
fun RentParkScreen() {
    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(DarkBackground)
    ) {
        // --- Header (Menu et Connexion) ---


        Column(
            modifier = Modifier.fillMaxSize(),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {
            // --- Logo ---
            Text(
                text = "RENTPARK",
                fontSize = 64.sp,
                fontWeight = FontWeight.Black,
                color = Color.White,
                modifier = Modifier.padding(bottom = 32.dp)
            )

            // --- Formulaire de réservation ---
            BookingCard()
        }
    }
}

@Composable
fun BookingCard() {
    Surface(
        modifier = Modifier
            .fillMaxWidth(0.9f)
            .padding(16.dp),
        color = CardBackground,
        shape = RoundedCornerShape(24.dp),
        border = null // Tu peux ajouter une bordure fine grise pour l'effet Glass
    ) {
        Row(
            modifier = Modifier
                .padding(24.dp)
                .fillMaxWidth(),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(16.dp)
        ) {
            // Champ Date de départ
            DatePickerField("Date de départ", modifier = Modifier.weight(1f))

            // Champ Date de retour
            DatePickerField("Date de retour", modifier = Modifier.weight(1f))

            // Bouton Action
            Button(
                onClick = { /* Action */ },
                modifier = Modifier
                    .height(56.dp)
                    .padding(top = 24.dp), // Aligné avec les inputs
                colors = ButtonDefaults.buttonColors(containerColor = AccentOrange),
                shape = RoundedCornerShape(12.dp)
            ) {
                Text("Voir les véhicules", color = Color.White, fontWeight = FontWeight.Bold)
            }
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun DatePickerField(label: String, modifier: Modifier = Modifier) {
    Column(modifier = modifier) {
        Text(label, color = Color.White, fontSize = 14.sp, modifier = Modifier.padding(bottom = 8.dp))
        OutlinedTextField(
            value = "jj/mm/aaaa",
            onValueChange = {},
            modifier = Modifier.fillMaxWidth(),
            colors = TextFieldDefaults.colors(
                focusedContainerColor = Color.White,
                unfocusedContainerColor = Color.White,
                focusedIndicatorColor = Color.Transparent,
                unfocusedIndicatorColor = Color.Transparent
            ),
            shape = RoundedCornerShape(12.dp),

        )
    }
}