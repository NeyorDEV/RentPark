package com.example.rentparkkotlin.viewmodel

import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.repository.ContratRepository
import io.mockk.coEvery
import io.mockk.coVerify
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.test.*
import org.junit.After
import org.junit.Assert.*
import org.junit.Before
import org.junit.Test

@OptIn(ExperimentalCoroutinesApi::class)
class ContratViewModelTest {

    private val testDispatcher = StandardTestDispatcher()
    private lateinit var repository: ContratRepository
    private lateinit var viewModel: ContratViewModel

    @Before
    fun setup() {
        Dispatchers.setMain(testDispatcher)
        repository = mockk()
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    // --- Tests de fetchContrats (appelé dans le init) ---

    @Test
    fun `fetchContrats succes met a jour la liste des contrats`() = runTest {
        val mockContrats = listOf(createFakeContrat(1, "Terminé"))
        // On mock la réponse de getContrats qui va être appelée par le init {}
        coEvery { repository.getContrats() } returns mockContrats

        viewModel = ContratViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals(mockContrats, viewModel.contrats)
        assertNull(viewModel.error)
        assertFalse(viewModel.isLoading)
    }

    @Test
    fun `fetchContrats erreur met a jour le message d erreur`() = runTest {
        coEvery { repository.getContrats() } throws Exception("Erreur API")

        viewModel = ContratViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        assertTrue(viewModel.error!!.contains("Erreur de chargement: Erreur API"))
        assertFalse(viewModel.isLoading)
        assertTrue(viewModel.contrats.isEmpty())
    }

    // --- Tests de addContrat ---

    @Test
    fun `addContrat succes appelle le repo et rafraichit la liste`() = runTest {
        val nouveauContrat = createFakeContrat(2, "En cours")
        val listeApresAjout = listOf(nouveauContrat)

        // Mock de l'init
        coEvery { repository.getContrats() } returns emptyList()
        // Mock de l'ajout (ne retourne rien de spécial)

        coEvery { repository.addContrat(nouveauContrat) } returns nouveauContrat

        viewModel = ContratViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        // On change le mock pour simuler que le getContrats() retourne maintenant la nouvelle liste
        coEvery { repository.getContrats() } returns listeApresAjout

        // Action
        viewModel.addContrat(nouveauContrat)
        testDispatcher.scheduler.advanceUntilIdle()

        // Vérifications
        coVerify { repository.addContrat(nouveauContrat) }
        assertEquals(1, viewModel.contrats.size)
        assertEquals("En cours", viewModel.contrats[0].statut)
    }

    @Test
    fun `addContrat erreur affiche un message d erreur`() = runTest {
        val contrat = createFakeContrat(3, "En cours")
        coEvery { repository.getContrats() } returns emptyList() // pour l'init
        coEvery { repository.addContrat(contrat) } throws Exception("Erreur réseau")

        viewModel = ContratViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        viewModel.addContrat(contrat)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals("Erreur d'ajout: Erreur réseau", viewModel.error)
    }

    // --- Tests de updateContrat ---

    @Test
    fun `updateContrat succes appelle le repo et rafraichit la liste`() = runTest {
        val contratInit = createFakeContrat(1, "En cours")
        val contratModifie = createFakeContrat(1, "Terminé")

        coEvery { repository.getContrats() } returns listOf(contratInit)
        coEvery { repository.updateContrat(1, contratModifie) } returns contratModifie

        viewModel = ContratViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        // Simulation du rafraîchissement
        coEvery { repository.getContrats() } returns listOf(contratModifie)

        viewModel.updateContrat(1, contratModifie)
        testDispatcher.scheduler.advanceUntilIdle()

        coVerify { repository.updateContrat(1, contratModifie) }
        assertEquals("Terminé", viewModel.contrats[0].statut)
    }

    @Test
    fun `updateContrat erreur affiche message erreur`() = runTest {
        val contrat = createFakeContrat(1, "En cours")
        coEvery { repository.getContrats() } returns emptyList()
        coEvery { repository.updateContrat(1, contrat) } throws Exception("Accès refusé")

        viewModel = ContratViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        viewModel.updateContrat(1, contrat)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals("Erreur de modification: Accès refusé", viewModel.error)
    }

    // --- Tests de deleteContrat ---

    @Test
    fun `deleteContrat succes appelle le repo et rafraichit la liste`() = runTest {
        val contrat = createFakeContrat(1, "En cours")

        coEvery { repository.getContrats() } returns listOf(contrat)
        coEvery { repository.deleteContrat(1) } returns mockk()

        viewModel = ContratViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        // Simulation de la liste vide après suppression
        coEvery { repository.getContrats() } returns emptyList()

        viewModel.deleteContrat(1)
        testDispatcher.scheduler.advanceUntilIdle()

        coVerify { repository.deleteContrat(1) }
        assertTrue(viewModel.contrats.isEmpty())
    }

    @Test
    fun `deleteContrat erreur affiche message erreur`() = runTest {
        coEvery { repository.getContrats() } returns emptyList()
        coEvery { repository.deleteContrat(1) } throws Exception("Suppression impossible")

        viewModel = ContratViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        viewModel.deleteContrat(1)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals("Erreur de suppression: Suppression impossible", viewModel.error)
    }
    
    private fun createFakeContrat(id: Int, statut: String? = "Actif") = Contrat(
        idContrat = id,
        dateDebut = "2023-01-01",
        dateFin = "2023-01-10",
        statut = statut,
        idClient = 1,
        idVehicule = "V123"
    )
}