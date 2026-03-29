package com.example.rentparkkotlin.viewmodel

import com.example.rentparkkotlin.data.ApiService
import com.example.rentparkkotlin.model.Rappel
import com.example.rentparkkotlin.repository.RappelRepository
import com.example.rentparkkotlin.ui.dashboard.DashboardCard
import io.mockk.coEvery
import io.mockk.coVerify
import io.mockk.every
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.test.*
import org.junit.After
import org.junit.Assert.*
import org.junit.Before
import org.junit.Test

@OptIn(ExperimentalCoroutinesApi::class)
class DashboardViewModelTest {

    private val testDispatcher = StandardTestDispatcher()
    private lateinit var repository: RappelRepository
    private lateinit var api: ApiService
    private lateinit var viewModel: DashboardViewModel

    @Before
    fun setup() {
        Dispatchers.setMain(testDispatcher)
        repository = mockk()
        api = mockk()
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    // --- Tests de fetchDashboardData (Init) ---

    @Test
    fun `fetchDashboardData succes cree les 5 cartes correctement`() = runTest {
        // Given : On mock TOUTES les requêtes réseau et BDD avec la syntaxe mockk { every { ... } }
        coEvery { api.getTotalUsers() } returns mockk { every { totalUsers } returns 150 }
        coEvery { api.getMostRentedCar() } returns mockk {
            every { voiturePlusLouee } returns mockk {
                every { marque } returns "Peugeot"
                every { modele } returns "208"
                every { nbLocations } returns 42
            }
        }
        coEvery { api.getMonthlyIncome() } returns mockk { every { monthlyIncome } returns 4500.50f }
        coEvery { api.getCTAlerts() } returns mockk {
            every { vehicules } returns listOf(
                mockk {
                    every { marque } returns "Renault"
                    every { modele } returns "Clio"
                }
            )
        }
        coEvery { api.getContratsProchains() } returns mockk { every { contratsProchains } returns emptyList() }

        // Correction du constructeur de Rappel (sans l'ID)
        coEvery { repository.getRappels() } returns listOf(Rappel("Vidange", "Faire la vidange", "2023-10-10"))

        // When : L'instanciation lance fetchDashboardData
        viewModel = DashboardViewModel(repository, api)
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertFalse(viewModel.isLoading)
        assertEquals(5, viewModel.cards.size)

        // Vérification du contenu des cartes (correction de .titre en .title)
        val usersCard = viewModel.cards[0]
        assertEquals("Nombre d'utilisateurs", usersCard.title)
        assertEquals("150", usersCard.items[0])

        val carCard = viewModel.cards[1]
        assertEquals("Voiture la plus louée", carCard.title)
        assertEquals("Peugeot 208", carCard.items[0])
        assertEquals("Louée 42 fois", carCard.items[1])

        val incomeCard = viewModel.cards[2]
        assertEquals("Revenus mensuels", incomeCard.title)
        assertEquals("4500€", incomeCard.items[0])

        val alertsCard = viewModel.cards[3]
        assertEquals("Rappels", alertsCard.title)
        assertTrue(alertsCard.items.contains("Contrôle technique – Renault Clio"))
        assertTrue(alertsCard.items.contains("Rappel : Vidange – Faire la vidange"))
    }

    @Test
    fun `fetchDashboardData erreur reseau cree une carte Erreur`() = runTest {
        // Given : L'API crash
        coEvery { api.getTotalUsers() } throws Exception("Timeout serveur")

        // When
        viewModel = DashboardViewModel(repository, api)
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertFalse(viewModel.isLoading)
        assertEquals(1, viewModel.cards.size)
        assertEquals("Erreur", viewModel.cards[0].title)
        assertTrue(viewModel.cards[0].items[0].contains("Impossible de charger les données : Timeout serveur"))
    }

    @Test
    fun `fetchDashboardData sans aucune alerte affiche le message par defaut`() = runTest {
        coEvery { api.getTotalUsers() } returns mockk { every { totalUsers } returns 10 }
        coEvery { api.getMostRentedCar() } returns mockk { every { voiturePlusLouee } returns null }
        coEvery { api.getMonthlyIncome() } returns mockk { every { monthlyIncome } returns 100.0f }
        coEvery { api.getCTAlerts() } returns mockk { every { vehicules } returns emptyList() }
        coEvery { api.getContratsProchains() } returns mockk { every { contratsProchains } returns emptyList() }
        coEvery { repository.getRappels() } returns emptyList()

        viewModel = DashboardViewModel(repository, api)
        testDispatcher.scheduler.advanceUntilIdle()

        val alertsCard = viewModel.cards.find { it.title == "Rappels" }
        assertNotNull(alertsCard)
        assertEquals("✅ Aucun rappel en cours", alertsCard!!.items[0])
    }

    // --- Tests de addRappel ---

    @Test
    fun `addRappel succes ajoute via le repo et rafraichit les donnees`() = runTest {
        // Mocks initiaux pour que le ViewModel démarre sans crasher
        coEvery { api.getTotalUsers() } returns mockk { every { totalUsers } returns 10 }
        coEvery { api.getMostRentedCar() } returns mockk { every { voiturePlusLouee } returns null }
        coEvery { api.getMonthlyIncome() } returns mockk { every { monthlyIncome } returns 100.0f }
        coEvery { api.getCTAlerts() } returns mockk { every { vehicules } returns emptyList() }
        coEvery { api.getContratsProchains() } returns mockk { every { contratsProchains } returns emptyList() }
        coEvery { repository.getRappels() } returns emptyList()

        viewModel = DashboardViewModel(repository, api)
        testDispatcher.scheduler.advanceUntilIdle()

        // Préparation du mock d'ajout (correction : retourne un mock au lieu de Unit)
        coEvery { repository.addRappel(any()) } returns mockk()

        // Action
        viewModel.addRappel("Pneus", "Changer pneus neige", "2023-12-01")
        testDispatcher.scheduler.advanceUntilIdle()

        // Vérification
        coVerify { repository.addRappel(match { it.titre == "Pneus" }) }
        // Vérifie que fetchDashboardData a bien été relancé (getMonthlyIncome appelé 2 fois : init + refresh)
        coVerify(exactly = 2) { api.getMonthlyIncome() }
    }
}