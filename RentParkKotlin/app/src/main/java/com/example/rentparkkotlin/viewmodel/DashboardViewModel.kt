package com.example.rentparkkotlin.viewmodel

import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.setValue
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.repository.RappelRepository
import com.example.rentparkkotlin.repository.StatsRepository
import com.example.rentparkkotlin.ui.dashboard.DashboardCard
import kotlinx.coroutines.async
import kotlinx.coroutines.launch
import kotlinx.coroutines.coroutineScope
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class DashboardViewModel(
    private val repositoryRappel: RappelRepository = RappelRepository(),
    private val repositoryStats: StatsRepository = StatsRepository()
) : ViewModel() {

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
                coroutineScope {
                    val totalUsersDeferred = async { repositoryStats.getTotalUsers() }
                    val bestCarDeferred = async { repositoryStats.getMostRentedCar() }
                    val incomeDeferred = async { repositoryStats.getMonthlyIncome() }
                    val rappelsDeferred = async { repositoryRappel.getRappels() }

                    val voituresDeferred = async { RetrofitInstance.apiVoiture.getVoitures() }
                    val contratsDeferred = async { RetrofitInstance.apiContrat.getContrats() }

                    val totalUsersResponse = totalUsersDeferred.await()
                    val bestCarResponse = bestCarDeferred.await()
                    val incomeResponse = incomeDeferred.await()
                    val rappels = rappelsDeferred.await()
                    val voitures = voituresDeferred.await()
                    val contrats = contratsDeferred.await()

                    val bestCar = bestCarResponse.voiturePlusLouee
                    val bestCarItems = if (bestCar != null) {
                        listOf("${bestCar.marque} ${bestCar.modele}", "Louée ${bestCar.nbLocations} fois")
                    } else {
                        listOf("Aucune donnée")
                    }

                    val alertItems = mutableListOf<String>()
                    val todayDate = Date()
                    val sdf = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
                    val today = sdf.format(todayDate)

                    val limitDate = Date(todayDate.time + (30L * 24 * 60 * 60 * 1000))

                    voitures.forEach { voiture ->
                        voiture.DateExpirationControleTech?.let { dateString ->
                            try {
                                val dateCT = sdf.parse(dateString.substring(0, 10))
                                if (dateCT != null && dateCT.before(limitDate)) {
                                    alertItems.add("Contrôle technique – ${voiture.Marque} ${voiture.Nom} ${voiture.NumSerie}")
                                }
                            } catch (e: Exception) {
                            }
                        }
                    }

                    rappels.forEach { rappel ->
                        val dateRappel = if (rappel.date.length >= 10) rappel.date.substring(0, 10) else rappel.date

                        if (dateRappel >= today) {
                            val dateAffichee = formatDate(rappel.date)
                            alertItems.add("Rappel du $dateAffichee : ${rappel.titre} – ${rappel.description}")
                        }
                    }
                    if (alertItems.isEmpty()) alertItems.add("✅ Aucun rappel en cours")

                    val planningItems = mutableListOf<String>()

                    contrats.forEach { c ->
                        val voiture = voitures.find { it.NumSerie == c.idVehicule }
                        val nomVoiture = if (voiture != null) "${voiture.Marque} ${voiture.Nom} ${voiture.NumSerie}" else "Véhicule #${c.idVehicule}"

                        if (c.dateDebut >= today) {
                            planningItems.add("${formatDate(c.dateDebut)} – Location : $nomVoiture")
                        }
                        if (c.dateFin >= today) {
                            planningItems.add("${formatDate(c.dateFin)} – Retour : $nomVoiture")
                        }
                    }

                    planningItems.sort()
                    if (planningItems.isEmpty()) planningItems.add("Aucun événement prévu")

                    cards = listOf(
                        DashboardCard("Nombre d'utilisateurs", listOf(totalUsersResponse.totalUsers.toString())),
                        DashboardCard("Voiture la plus louée", bestCarItems),
                        DashboardCard("Revenus mensuels", listOf("${incomeResponse.monthlyIncome.toInt()}€")),
                        DashboardCard("Rappels", alertItems),
                        DashboardCard("Planning Proche", planningItems)
                    )
                }
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

    fun addRappel(titre: String, description: String, date: String) {
        viewModelScope.launch {
            try {
                val nouveauRappel = com.example.rentparkkotlin.model.Rappel(
                    titre = titre,
                    description = description,
                    date = date
                )
                repositoryRappel.addRappel(nouveauRappel)
                fetchDashboardData()
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
    }
}