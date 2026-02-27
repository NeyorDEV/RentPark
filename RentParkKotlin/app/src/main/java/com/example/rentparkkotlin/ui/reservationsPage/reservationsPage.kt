package com.example.rentparkkotlin.ui.reservationsPage

import android.R
import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp

// 1. Modèle de données
data class Reservation(
    val id: Int,
    val clientId: Int,
    val vehiculeId: String,
    val debut: String,
    val fin: String
)

class ContratsActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            // Fond Noir Pur
            Surface(modifier = Modifier.fillMaxSize(), color = Color.Black) {
                ContratsScreen()
            }
        }
    }
}

@Composable
fun ContratsScreen() {
    val reservations = listOf(
        Reservation(36, 50, "TEST22335564", "2026-02-27", "2026-03-01"),
        Reservation(38, 44, "TEST22335564", "2026-02-27", "2026-02-28")
    )

    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp)
            .background(Color(0xFF0F0F0F))
    ) {
        // Titre
        Text(
            text = "Contrats",
            color = Color.White,
            fontSize = 32.sp,
            fontWeight = FontWeight.Bold,
            modifier = Modifier
                .fillMaxWidth()
                .padding(vertical = 32.dp),
            textAlign = TextAlign.Center
        )

        // Barre de recherche (fond gris très sombre pour contraster avec le noir)
        FilterHeader()

        Spacer(modifier = Modifier.height(32.dp))

        // Liste des cartes
        LazyColumn(verticalArrangement = Arrangement.spacedBy(16.dp)) {
            items(reservations) { res ->
                ReservationCard(res)
            }
        }
    }
}

@Composable
fun FilterHeader() {
    Surface(
        color = Color(0xFF151515), // Gris très foncé pour la barre
        shape = RoundedCornerShape(50),
        modifier = Modifier.fillMaxWidth()
    ) {
        Row(
            modifier = Modifier.padding(horizontal = 16.dp, vertical = 10.dp),
            verticalAlignment = Alignment.CenterVertically,
            horizontalArrangement = Arrangement.spacedBy(12.dp)
        ) {
            Text("RECHERCHER PAR :", color = Color.White, fontSize = 10.sp, fontWeight = FontWeight.ExtraBold)

            Surface(color = Color(0xFF222222), shape = RoundedCornerShape(8.dp)) {
                Text("ID", color = Color.White, modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp), fontSize = 12.sp)
            }

            Surface(
                color = Color(0xFF222222),
                shape = RoundedCornerShape(8.dp),
                modifier = Modifier.weight(1f)
            ) {
                Text("Rechercher...", color = Color.Gray, modifier = Modifier.padding(horizontal = 12.dp, vertical = 6.dp), fontSize = 12.sp)
            }

            Icon(Icons.Default.Search, contentDescription = null, tint = Color(0xFFE67E22))

            Button(
                onClick = { /* Ajouter */ },
                colors = ButtonDefaults.buttonColors(containerColor = Color(0xFF7DCEA0)),
                contentPadding = PaddingValues(horizontal = 16.dp),
                shape = RoundedCornerShape(20.dp),
                modifier = Modifier.height(36.dp)
            ) {
                Text("+ Ajouter", color = Color.White, fontWeight = FontWeight.Bold, fontSize = 12.sp)
            }
        }
    }
}

@Composable
fun ReservationCard(reservation: Reservation) {
    Card(
        modifier = Modifier.fillMaxWidth(),
        colors = CardDefaults.cardColors(containerColor = Color(0xFF181818)), // Gris foncé pour les cartes
        shape = RoundedCornerShape(20.dp)
    ) {
        Row(
            modifier = Modifier.padding(24.dp),
            horizontalArrangement = Arrangement.SpaceBetween,
            verticalAlignment = Alignment.CenterVertically
        ) {
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = "Reservation n°${reservation.id}",
                    color = Color(0xFFE67E22), // Orange
                    fontWeight = FontWeight.Bold,
                    fontSize = 18.sp
                )
                Spacer(modifier = Modifier.height(12.dp))
                InfoLabel("Client", reservation.clientId.toString())
                InfoLabel("Vehicule", reservation.vehiculeId)
                InfoLabel("Debut", reservation.debut)
                InfoLabel("Fin", reservation.fin)
            }

            Column(verticalArrangement = Arrangement.spacedBy(10.dp)) {
                ActionButton("Modifier", Color(0xFFEBF5FB), Color(0xFF2E86C1))
                ActionButton("Supprimer", Color(0xFFFDEDEC), Color(0xFFCB4335))
            }
        }
    }
}

@Composable
fun InfoLabel(label: String, value: String) {
    Text(
        text = "$label : $value",
        color = Color.White,
        fontSize = 14.sp,
        modifier = Modifier.padding(vertical = 2.dp)
    )
}

@Composable
fun ActionButton(text: String, bgColor: Color, textColor: Color) {
    Button(
        onClick = { /* Action */ },
        colors = ButtonDefaults.buttonColors(containerColor = bgColor),
        shape = RoundedCornerShape(10.dp),
        contentPadding = PaddingValues(horizontal = 16.dp, vertical = 0.dp),
        modifier = Modifier.width(115.dp).height(40.dp)
    ) {
        Text(text = text, color = textColor, fontSize = 14.sp, fontWeight = FontWeight.SemiBold)
    }
}