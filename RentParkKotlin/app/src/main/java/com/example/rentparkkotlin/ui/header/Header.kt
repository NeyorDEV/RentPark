package com.example.rentparkkotlin.ui.header

import androidx.compose.animation.core.animateDpAsState
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxHeight
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.DrawerValue
import androidx.compose.material3.IconButton
import androidx.compose.material3.ModalDrawerSheet
import androidx.compose.material3.ModalNavigationDrawer
import androidx.compose.material3.Surface
import androidx.compose.material3.Text
import androidx.compose.material3.rememberDrawerState
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.blur
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.Font
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.rentparkkotlin.R
import com.example.rentparkkotlin.ui.theme.BlackTheme
import com.example.rentparkkotlin.ui.theme.Orange
import kotlinx.coroutines.launch

// Header commun à toutes les pages de rentpark (au moins les pages admin)

@Composable
fun Header(title: String, onMenuClick: () -> Unit) {
    Column {
        Spacer(
            modifier = Modifier
                .fillMaxWidth()
                .height(22.dp)
                .background(color = Orange)
        )

        Surface(
            color = BlackTheme,
            shadowElevation = 4.dp
        ) {
            Row(
                modifier = Modifier
                    .fillMaxWidth()
                    .height(64.dp),
                verticalAlignment = Alignment.CenterVertically
            ) {
                Box(
                    modifier = Modifier.weight(1f),
                    contentAlignment = Alignment.CenterStart
                ) {
                    IconButton(
                        onClick = onMenuClick, // C'est ici que l'action est liée
                        modifier = Modifier.padding(start = 8.dp)
                    ) {
                        MenuBurger()
                    }
                }

                Box(
                    modifier = Modifier.weight(3f),
                    contentAlignment = Alignment.Center
                ) {
                    Text(
                        text = title,
                        color = Color.White,
                        fontFamily = MaPoliceCustom,
                        fontSize = 28.sp,
                        fontWeight = FontWeight.ExtraBold,
                        textAlign = TextAlign.Center,
                        modifier = Modifier.padding(top = 4.dp)
                    )
                }
                Spacer(modifier = Modifier.weight(1f))
            }
        }
    }
}
val MaPoliceCustom = FontFamily(
    Font(R.font.fortnite, FontWeight.Normal),
    Font(R.font.fortnite, FontWeight.Bold)
)

@Preview(showBackground = true)
@Composable
fun HeaderPreview() {
    Header(
        title = "RentPark",
        onMenuClick = { /* Ne rien mettre ici */ }
    )
}

// Dans Header.kt

// Dans Header.kt (Extrait des modifications)

// Dans Header.kt

@Composable
fun MainScreen(title: String, content: @Composable () -> Unit) {
    val drawerState = rememberDrawerState(initialValue = DrawerValue.Closed)
    val scope = rememberCoroutineScope()

    ModalNavigationDrawer(
        drawerState = drawerState,
        gesturesEnabled = true,
        drawerContent = {
            ModalDrawerSheet(
                // Définit la largeur à 3/4 de l'écran
                modifier = Modifier.fillMaxWidth(0.75f).fillMaxHeight(),
                drawerContainerColor = BlackTheme,
                drawerShape = RoundedCornerShape(topEnd = 16.dp, bottomEnd = 16.dp)
            ) {
                // --- CONTENU DU MENU ---
                Text(
                    "Menu RentPark",
                    color = Color.White,
                    modifier = Modifier.padding(24.dp),
                    fontSize = 20.sp,
                    fontWeight = FontWeight.Bold
                )
                // Ajoutez ici vos liens de navigation (ex: NavigationDrawerItem)
            }
        }
    ) {
        // Effet de flou sur le contenu principal quand le menu est ouvert
        val blurRadius by animateDpAsState(
            targetValue = if (drawerState.isOpen) 12.dp else 0.dp,
            label = "blurAnimation"
        )

        Box(
            modifier = Modifier
                .fillMaxSize()
                .blur(blurRadius)
        ) {
            Column {
                Header(
                    title = title,
                    onMenuClick = {
                        println("menu cliquer") // Log console demandé
                        scope.launch { drawerState.open() }
                    }
                )
                // Zone où s'affiche le contenu de la page
                Box(modifier = Modifier.fillMaxSize()) {
                    content()
                }
            }
        }
    }
}