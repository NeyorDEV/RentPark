package com.example.rentparkkotlin.ui.homeCustomer

import androidx.compose.foundation.background
import androidx.compose.foundation.border
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
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.rentparkkotlin.R
import com.example.rentparkkotlin.ui.i18n.Strings

val MaPoliceCustom = FontFamily(
    Font(R.font.fortnite, FontWeight.Normal),
    Font(R.font.fortnite, FontWeight.Bold)
)
@Composable
fun RentParkHomeScreen() {
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

        // --- 3. Contenu Central ---
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 24.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
            verticalArrangement = Arrangement.Center
        ) {

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
                    VerticalDateField(label = strings.current.home.departureDate)
                    VerticalDateField(label = q.current.home.returnDate)

                    Button(
                        onClick = { /* Action */ },
                        modifier = Modifier.fillMaxWidth().height(60.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = Color(0xFFF16937)),
                        shape = RoundedCornerShape(16.dp)
                    ) {
                        Text(Strings.current.home.showCars, fontSize = 16.sp, fontWeight = FontWeight.Bold)
                    }
                }
            }
            Spacer(modifier = Modifier.height(40.dp))
            ConnectionButton()
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun VerticalDateField(label: String) {
    Column {
        Text(label, color = Color.Gray, fontSize = 14.sp, modifier = Modifier.padding(bottom = 8.dp))
        OutlinedTextField(
            value = Strings.current.home.styleDate,
            onValueChange = {},
            modifier = Modifier.fillMaxWidth(),
            trailingIcon = { Icon(Icons.Default.DateRange, contentDescription = null, tint = Color.Gray) },
            shape = RoundedCornerShape(12.dp),
            readOnly = true
        )
    }
}

@Composable
fun ConnectionButton() {
    Row(
        modifier = Modifier
            .padding(horizontal = 12.dp, vertical = 6.dp)
            // 1. Ajoute le contour ici
            .border(
                width = 1.dp,
                color = Color.White,
                shape = RoundedCornerShape(50) // 50% pour un effet pilule, ou 8.dp pour des coins arrondis
            )
            // 2. Ajoute un peu de padding interne pour que le texte ne touche pas le bord
            .padding(horizontal = 16.dp, vertical = 8.dp),
        verticalAlignment = Alignment.CenterVertically
    ) {
        Icon(
            imageVector = Icons.Default.Person,
            contentDescription = null,
            tint = Color.White,
            modifier = Modifier.size(22.dp)
        )
        Spacer(Modifier.width(8.dp))
        Text(
            Strings.current.home.connection,
            color = Color.White,
            fontSize = 18.sp
        )
    }
}