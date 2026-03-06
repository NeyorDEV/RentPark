package com.example.rentparkkotlin.viewmodel

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.ui.dashboard.DashboardCard
import kotlinx.coroutines.async
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class DashboardViewModel : ViewModel() {

    var cards by mutableStateOf<List<DashboardCard>>(emptyList())
        private set

    var isLoading by mutableStateOf(true)
        private set

    init {
        fetchDashboardData()
    }

    private fun fetchDashboardData() {
        viewModelScope.launch {
            isLoading = true
            try {
                val totalUsersDeferred = async { RetrofitInstance.api.getTotalUsers() }
                val bestCarDeferred = async { RetrofitInstance.api.getMostRentedCar() }
                val incomeDeferred = async { RetrofitInstance.api.getMonthlyIncome() }
                val rappelsDeferred = async { RetrofitInstance.api.getRappels() }
                val ctAlertsDeferred = async { RetrofitInstance.api.getCTAlerts() }
                val contratsDeferred = async { RetrofitInstance.api.getContratsProchains() }

                val totalUsers = totalUsersDeferred.await().totalUsers
                val bestCar = bestCarDeferred.await().voiturePlusLouee
                val income = incomeDeferred.await().monthlyIncome
                val rappels = rappelsDeferred.await()
                val ctAlerts = ctAlertsDeferred.await().vehicules
                val contrats = contratsDeferred.await().contratsProchains

                val bestCarItems = if (bestCar != null) {
                    listOf("${bestCar.marque} ${bestCar.modele}", "Louée ${bestCar.nbLocations} fois")
                } else {
                    listOf("Aucune donnée")
                }

                val alertItems = mutableListOf<String>()
                ctAlerts.forEach { ct ->
                    alertItems.add("Contrôle technique – ${ct.marque} ${ct.modele}")
                }
                rappels.forEach { rappel ->
                    alertItems.add("Rappel : ${rappel.titre} – ${rappel.description}")
                }
                if (alertItems.isEmpty()) alertItems.add("✅ Aucun rappel en cours")

                val planningItems = mutableListOf<String>()
                val today = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Date())

                contrats.forEach { c ->
                    if (c.dateDebut >= today) {
                        planningItems.add("${formatDate(c.dateDebut)} – Location : ${c.marque} ${c.modele}")
                    }
                    if (c.dateFin >= today) {
                        planningItems.add("${formatDate(c.dateFin)} – Retour : ${c.marque} ${c.modele}")
                    }
                }
                planningItems.sort()
                if (planningItems.isEmpty()) planningItems.add("Aucun événement prévu")

                cards = listOf(
                    DashboardCard("Nombre d'utilisateurs", listOf(totalUsers.toString())),
                    DashboardCard("Voiture la plus louée", bestCarItems),
                    DashboardCard("Revenus mensuels", listOf("${income.toInt()}€")), // ou $income€
                    DashboardCard("Rappels", alertItems),
                    DashboardCard("Planning Proche", planningItems)
                )

            } catch (e: Exception) {
                e.printStackTrace()
                cards = listOf(DashboardCard("Erreur", listOf("Impossible de charger les données : ${e.message}")))
            }
            isLoading = false
        }
    }

    private fun formatDate(dateString: String): String {
        return try {
            val parser = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
            val formatter = SimpleDateFormat("dd/MM/yyyy", Locale.getDefault())
            val date = parser.parse(dateString.substring(0, 10))
            if (date != null) formatter.format(date) else dateString
        } catch (e: Exception) {
            dateString
        }
    }
}