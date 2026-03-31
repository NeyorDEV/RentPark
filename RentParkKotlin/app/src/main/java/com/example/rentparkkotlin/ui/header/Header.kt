package com.example.rentparkkotlin.ui.header

import androidx.compose.animation.core.animateDpAsState
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.*
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
import androidx.navigation.NavController
import com.example.rentparkkotlin.R
import com.example.rentparkkotlin.Routes
import com.example.rentparkkotlin.ui.theme.BlackTheme
import com.example.rentparkkotlin.ui.theme.Orange
import kotlinx.coroutines.launch
import androidx.compose.ui.platform.LocalContext
import com.example.rentparkkotlin.data.AuthPrefs
import androidx.compose.runtime.remember

val MaPoliceCustom = FontFamily(
    Font(R.font.fortnite, FontWeight.Normal),
    Font(R.font.fortnite, FontWeight.Bold)
)

@Composable
fun Header(title: String, onMenuClick: () -> Unit) {
    Surface(
        color = BlackTheme,
        shadowElevation = 4.dp
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .height(86.dp)
                .padding(top = 32.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            Box(
                modifier = Modifier.weight(1f),
                contentAlignment = Alignment.CenterStart
            ) {
                IconButton(
                    onClick = onMenuClick,
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

@Composable
fun MainScreen(
    title: String,
    navController: NavController,
    content: @Composable () -> Unit
) {
    val drawerState = rememberDrawerState(initialValue = DrawerValue.Closed)
    val scope = rememberCoroutineScope()
    val context = LocalContext.current
    val authPrefs = remember { AuthPrefs(context) }

    ModalNavigationDrawer(
        drawerState = drawerState,
        gesturesEnabled = true,
        drawerContent = {
            ModalDrawerSheet(
                modifier = Modifier.fillMaxWidth(0.75f).fillMaxHeight(),
                drawerContainerColor = BlackTheme,
                drawerShape = RoundedCornerShape(topEnd = 16.dp, bottomEnd = 16.dp)
            ) {
                Spacer(modifier = Modifier.height(24.dp))

                Text(
                    "RentPark",
                    color = Orange,
                    modifier = Modifier.padding(horizontal = 28.dp, vertical = 16.dp),
                    fontSize = 30.sp,
                    fontFamily = MaPoliceCustom,
                    fontWeight = FontWeight.Bold
                )

                HorizontalDivider(color = Color.White.copy(alpha = 0.2f), thickness = 1.dp)
                Spacer(modifier = Modifier.height(16.dp))

                // --- LISTE DES BOUTONS DU MENU ---
                DrawerMenuItem("Accueil", onClick = {
                    scope.launch { drawerState.close() }
                    navController.navigate(Routes.HomeCustomerRoute)
                })

                DrawerMenuItem("Dashboard", onClick = {
                    scope.launch { drawerState.close() }
                    navController.navigate(Routes.DashBoardRoute)
                })

                DrawerMenuItem("Voitures", onClick = {
                    scope.launch { drawerState.close() }
                    navController.navigate(Routes.CarRoute)
                })

                DrawerMenuItem("Planning", onClick = {
                    scope.launch { drawerState.close() }
                    navController.navigate(Routes.PlanningRoute)
                })

                DrawerMenuItem("Utilisateurs", onClick = {
                    scope.launch { drawerState.close() }
                    navController.navigate(Routes.UserRoute)
                })

                DrawerMenuItem("Contrats", onClick = {
                    scope.launch { drawerState.close() }
                    navController.navigate(Routes.ContratRoute)
                })

                DrawerMenuItem("Paramètres", onClick = {
                    scope.launch { drawerState.close() }
                    navController.navigate(Routes.SettingsRoute)
                })

                Spacer(modifier = Modifier.weight(1f)) // Pousse les paramètres vers le bas

                DrawerMenuItem("Se Déconnecter", onClick = {
                    scope.launch { drawerState.close() }
                    authPrefs.clearAuth()
                    navController.navigate(Routes.LoginRoute) {
                        popUpTo(0) { inclusive = true }
                    }
                })

                Spacer(modifier = Modifier.height(16.dp))
            }
        }
    ) {
        // Animation du flou
        val blurRadius by animateDpAsState(
            targetValue = if (drawerState.isOpen) 12.dp else 0.dp,
            label = "blurAnimation"
        )

        Box(modifier = Modifier.fillMaxSize().blur(blurRadius)) {
            Column {
                Header(
                    title = title,
                    onMenuClick = { scope.launch { drawerState.open() } }
                )
                Box(modifier = Modifier.fillMaxSize()) {
                    content()
                }
            }
        }
    }
}

@Composable
fun DrawerMenuItem(label: String, onClick: () -> Unit) {
    NavigationDrawerItem(
        label = {
            Text(
                text = label,
                color = Color.White,
                fontSize = 18.sp,
                fontWeight = FontWeight.Medium
            )
        },
        selected = false,
        onClick = onClick,
        modifier = Modifier.padding(NavigationDrawerItemDefaults.ItemPadding),
        colors = NavigationDrawerItemDefaults.colors(
            unselectedContainerColor = Color.Transparent
        )
    )
}