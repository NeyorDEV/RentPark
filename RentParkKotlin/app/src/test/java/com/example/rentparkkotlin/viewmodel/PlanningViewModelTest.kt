package com.example.rentparkkotlin.viewmodel

import com.example.rentparkkotlin.MainDispatcherRule
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.repository.ContratRepository // ou ton ApiContrat
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.test.runTest
import org.junit.Assert.assertEquals
import org.junit.Assert.assertTrue
import org.junit.Rule
import org.junit.Test

@OptIn(ExperimentalCoroutinesApi::class)
class PlanningViewModelTest {

    @get:Rule
    val mainDispatcherRule = MainDispatcherRule()

    private lateinit var repository: ContratRepository
    private lateinit var viewModel: PlanningViewModel

    // ==========================================
    // TESTS DE L'INITIALISATION ET APPEL API
    // ==========================================

    @Test
    fun `init recupere la liste des contrats et met a jour le StateFlow`() = runTest {
        // Given
        repository = mockk()
        // Comme on ne connait pas les propriétés exactes de Contrat, on mock simplement des objets
        val mockContrats = listOf(mockk<Contrat>(), mockk<Contrat>())

        // On configure le mock AVANT d'instancier le ViewModel car l'appel est dans le bloc init
        coEvery { repository.getContrats() } returns mockContrats

        // When
        viewModel = PlanningViewModel(repository)

        // Then
        // On vérifie que le StateFlow a bien reçu la liste
        assertEquals(mockContrats, viewModel.contrats.value)
        assertEquals(2, viewModel.contrats.value.size)
    }

    @Test
    fun `init declenche une exception et garde le StateFlow vide`() = runTest {
        // Given
        repository = mockk()
        // On simule un crash réseau ou serveur
        coEvery { repository.getContrats() } throws Exception("Erreur de connexion")

        // When
        viewModel = PlanningViewModel(repository)

        // Then
        // Le bloc catch fait un e.printStackTrace(), la liste doit donc rester vide
        assertTrue(viewModel.contrats.value.isEmpty())
    }
}