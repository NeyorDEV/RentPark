package com.example.rentparkkotlin.viewmodel

import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.repository.CarRepository
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.test.*
import org.junit.After
import org.junit.Assert.assertEquals
import org.junit.Assert.assertNull
import org.junit.Assert.assertTrue
import org.junit.Before
import org.junit.Test
import retrofit2.Response

@OptIn(ExperimentalCoroutinesApi::class)
class CarViewModelTest {

    private val testDispatcher = StandardTestDispatcher()
    private lateinit var repository: CarRepository
    private lateinit var viewModel: CarViewModel

    @Before
    fun setup() {
        // 1. On remplace le dispatcher Main (UI) par un dispatcher de test
        Dispatchers.setMain(testDispatcher)

        // 2. On crée un faux repository
        repository = mockk()
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    @Test
    fun `fetchVoitures succes devrait mettre a jour la liste`() = runTest {
        // Utilisation du helper : simple et rapide !
        val listeVoitures = listOf(createFakeVoiture(numSerie = "V1", marque = "Renault"))

        coEvery { repository.getVoitures() } returns listeVoitures

        viewModel = CarViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals(listeVoitures, viewModel.voitures)
    }

    @Test
    fun `fetchVoitures erreur devrait mettre a jour le message d erreur`() = runTest {
        // Given (On simule une explosion de l'API)
        coEvery { repository.getVoitures() } throws Exception("Erreur Réseau")

        // When
        viewModel = CarViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertEquals("Erreur Réseau", viewModel.error)
        assertEquals(false, viewModel.isLoading)
    }

    @Test
    fun `deleteVoiture succes devrait rafraichir la liste`() = runTest {
        // Given
        coEvery { repository.deleteVoiture("1") } returns Response.success(null)
        coEvery { repository.getVoitures() } returns emptyList() // Appel suite à suppression

        viewModel = CarViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        // When
        viewModel.deleteVoiture("1")
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertTrue(viewModel.voitures.isEmpty())
    }

    @Test
    fun `addVoiture succes devrait rafraichir la liste`() = runTest {
        // Given
        val nouvelleVoiture = createFakeVoiture(numSerie = "NEW-001")
        coEvery { repository.addVoiture(nouvelleVoiture) } returns Response.success(null)
        coEvery { repository.getVoitures() } returns listOf(nouvelleVoiture) // Simule le refresh

        viewModel = CarViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        // When
        viewModel.addVoiture(nouvelleVoiture)
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertEquals(1, viewModel.voitures.size)
        assertNull(viewModel.error)
    }

    @Test
    fun `addVoiture erreur serveur devrait mettre a jour error`() = runTest {
        // Given
        val voiture = createFakeVoiture()
        // On simule une erreur 400 (Bad Request)
        coEvery { repository.addVoiture(voiture) } returns Response.error(400, mockk(relaxed = true))

        viewModel = CarViewModel(repository)

        // When
        viewModel.addVoiture(voiture)
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertEquals("Erreur serveur : 400", viewModel.error)
    }

    @Test
    fun `addVoiture exception devrait afficher message impossible`() = runTest {
        // Given
        val voiture = createFakeVoiture()
        coEvery { repository.addVoiture(voiture) } throws Exception("Timeout")

        viewModel = CarViewModel(repository)

        // When
        viewModel.addVoiture(voiture)
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertEquals("Impossible d'ajouter le véhicule : Timeout", viewModel.error)
    }

    private fun createFakeVoiture(numSerie: String = "TEST-123", marque: String = "Toyota"): Voiture {
        return Voiture(
            NumSerie = numSerie,
            Energie = "Essence",
            NbPlaces = "5",
            Categorie = "Citadine",
            Transmission = "Avant",
            Boite = "Manuelle",
            Etat = "Excellent",
            Puissance = "110cv",
            DateAchat = "2023-01-01",
            DateExpirationControleTech = "2025-01-01",
            DateDernierControleTech = "2023-01-01",
            Marque = marque,
            Nom = "Yaris",
            Annee = "2023",
            IdAssureur = 1,
            IdFournisseur = 1,
            ImagePath = "path/to/image.png",
            Couleur = "Gris",
            Prix = "45"
        )
    }
}

