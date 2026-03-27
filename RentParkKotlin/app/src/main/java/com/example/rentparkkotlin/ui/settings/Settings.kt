package com.example.rentparkkotlin.ui.settings

import androidx.compose.foundation.BorderStroke
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
import com.example.rentparkkotlin.ui.header.Header

val AppOrange = Color(0xFFE35D33)
val ButtonGrey = Color(0xFF9E9E9E)

@Composable
fun SettingsPage(
    isDarkMode: Boolean,
    onThemeChange: (Boolean) -> Unit
) {
    var notificationsEnabled by remember { mutableStateOf(false) }

    Scaffold(
    ) { paddingValues ->
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(paddingValues)
                .padding(horizontal = 24.dp, vertical = 16.dp)
        ) {
            Text(
                text = "Paramètres",
                fontSize = 32.sp,
                fontWeight = FontWeight.Bold,
                color = MaterialTheme.colorScheme.onBackground
            )
            Spacer(modifier = Modifier.height(4.dp))
            Box(
                modifier = Modifier
                    .width(100.dp)
                    .height(3.dp)
                    .background(AppOrange)
            )

            Spacer(modifier = Modifier.height(40.dp))

            Text(
                text = "Mode d'affichage",
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                color = MaterialTheme.colorScheme.onBackground
            )
            Text(
                text = "Choisissez entre le mode clair et le mode sombre :",
                color = Color.Gray,
                fontSize = 14.sp,
                modifier = Modifier.padding(vertical = 8.dp)
            )

            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.spacedBy(12.dp)
            ) {
                ModeButton(
                    text = "Mode Clair",
                    icon = "☀️",
                    isSelected = !isDarkMode,
                    modifier = Modifier.weight(1f),
                    onClick = { onThemeChange(false) }
                )
                ModeButton(
                    text = "Mode Sombre",
                    icon = "🌙",
                    isSelected = isDarkMode,
                    modifier = Modifier.weight(1f),
                    onClick = { onThemeChange(true) }
                )
            }

            Spacer(modifier = Modifier.height(32.dp))
            HorizontalDivider(color = Color.LightGray.copy(alpha = 0.5f))
            Spacer(modifier = Modifier.height(32.dp))

            Text(
                text = "Notifications",
                fontSize = 18.sp,
                fontWeight = FontWeight.Bold,
                color = MaterialTheme.colorScheme.onBackground
            )
            Spacer(modifier = Modifier.height(16.dp))

            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .background(MaterialTheme.colorScheme.surfaceVariant, RoundedCornerShape(16.dp))
                    .padding(16.dp),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Text(
                    text = "Recevoir les alertes de maintenance par email",
                    modifier = Modifier.weight(1f),
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )

                Button(
                    onClick = { notificationsEnabled = !notificationsEnabled },
                    colors = ButtonDefaults.buttonColors(
                        containerColor = if (notificationsEnabled) AppOrange else ButtonGrey
                    ),
                    shape = RoundedCornerShape(12.dp)
                ) {
                    Text(
                        text = if (notificationsEnabled) "Activé" else "Désactivé",
                        color = Color.White,
                        fontWeight = FontWeight.Bold
                    )
                }
            }
        }
    }
}

@Composable
fun ModeButton(
    text: String,
    icon: String,
    isSelected: Boolean,
    modifier: Modifier = Modifier,
    onClick: () -> Unit
) {
    OutlinedButton(
        onClick = onClick,
        modifier = modifier.height(56.dp),
        shape = RoundedCornerShape(12.dp),
        border = BorderStroke(
            width = if (isSelected) 2.dp else 1.dp,
            color = if (isSelected) MaterialTheme.colorScheme.onBackground else Color.Gray
        ),
        colors = ButtonDefaults.outlinedButtonColors(
            contentColor = MaterialTheme.colorScheme.onBackground
        )
    ) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            Text(icon)
            Spacer(modifier = Modifier.width(8.dp))
            Text(text = text, fontWeight = if (isSelected) FontWeight.Bold else FontWeight.Normal)
        }
    }
}