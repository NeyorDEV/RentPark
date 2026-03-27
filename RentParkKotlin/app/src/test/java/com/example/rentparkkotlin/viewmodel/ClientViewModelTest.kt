package com.example.rentparkkotlin.viewmodel

import com.example.rentparkkotlin.model.Client
import com.example.rentparkkotlin.model.ClientRequest
import com.example.rentparkkotlin.repository.ClientRepository
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.test.*
import org.junit.After
import org.junit.Assert.*
import org.junit.Before
import org.junit.Test
import retrofit2.Response

@OptIn(ExperimentalCoroutinesApi::class)
class ClientViewModelTest {

    private val testDispatcher = StandardTestDispatcher()
    private lateinit var repository: ClientRepository
    private lateinit var viewModel: ClientViewModel

    @Before
    fun setup() {
        Dispatchers.setMain(testDispatcher)
        repository = mockk()
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    // --- Tests de fetchClients ---

    @Test
    fun `fetchClients succes met a jour la liste des clients`() = runTest {
        val mockClients = listOf(createFakeClient(1, "Dupont"))
        coEvery { repository.getClients() } returns mockClients

        viewModel = ClientViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals(mockClients, viewModel.clients)
        assertNull(viewModel.error)
        assertFalse(viewModel.isLoading)
    }

    @Test
    fun `fetchClients erreur met a jour le message d erreur`() = runTest {
        coEvery { repository.getClients() } throws Exception("Crash")

        viewModel = ClientViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        assertTrue(viewModel.error!!.contains("Erreur de chargement: Crash"))
        assertFalse(viewModel.isLoading)
    }

    // --- Tests de addClient ---

    @Test
    fun `addClient succes rafraichit la liste`() = runTest {
        val request = createFakeClientRequest()
        coEvery { repository.addClient(request) } returns Response.success(mockk())
        coEvery { repository.getClients() } returns listOf(createFakeClient(1, "Nouveau"))

        viewModel = ClientViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        viewModel.addClient(request)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals(1, viewModel.clients.size)
        assertEquals("Nouveau", viewModel.clients[0].nom)
    }

    @Test
    fun `addClient erreur API affiche un message d erreur`() = runTest {
        val request = createFakeClientRequest()
        coEvery { repository.addClient(request) } returns Response.error(400, mockk(relaxed = true))

        viewModel = ClientViewModel(repository)
        viewModel.addClient(request)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals("Erreur lors de l'ajout du client", viewModel.error)
    }

    // --- Tests de updateClient ---

    @Test
    fun `updateClient succes rafraichit la liste`() = runTest {
        val request = createFakeClientRequest()
        coEvery { repository.updateClient(1, request) } returns Response.success(mockk())
        coEvery { repository.getClients() } returns listOf(createFakeClient(1, "Modifie"))

        viewModel = ClientViewModel(repository)
        viewModel.updateClient(1, request)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals("Modifie", viewModel.clients[0].nom)
    }

    @Test
    fun `updateClient erreur reseau affiche message erreur reseau`() = runTest {
        val request = createFakeClientRequest()
        coEvery { repository.updateClient(1, request) } throws Exception("Pas de WiFi")

        viewModel = ClientViewModel(repository)
        viewModel.updateClient(1, request)
        testDispatcher.scheduler.advanceUntilIdle()

        assertEquals("Erreur réseau: Pas de WiFi", viewModel.error)
    }

    @Test
    fun `addClient ligne 51 - devrait capturer exception et afficher erreur reseau`() = runTest {
        // Given
        val request = createFakeClientRequest()
        // On force le repository à jeter une exception pour atteindre le catch
        coEvery { repository.addClient(request) } throws Exception("Connexion perdue")

        viewModel = ClientViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        // When
        viewModel.addClient(request)
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertEquals("Erreur réseau: Connexion perdue", viewModel.error)
    }

    @Test
    fun `updateClient ligne 63 - devrait capturer exception et afficher erreur reseau`() = runTest {
        // Given
        val request = createFakeClientRequest()
        val clientId = 1
        // On force l'exception ici aussi
        coEvery { repository.updateClient(clientId, request) } throws Exception("Serveur introuvable")

        viewModel = ClientViewModel(repository)
        testDispatcher.scheduler.advanceUntilIdle()

        // When
        viewModel.updateClient(clientId, request)
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertEquals("Erreur réseau: Serveur introuvable", viewModel.error)
    }

    // --- Helpers pour creer les objets ---

    private fun createFakeClient(id: Int, nom: String) = Client(
        idClient = id,
        nom = nom,
        prenom = "Jean",
        email = "jean@test.com",
        numTel = "0102030405",
        numPermis = "ABC123",
        dateNaiss = "1990-01-01",
        nationalite = "Française"
    )

    private fun createFakeClientRequest() = ClientRequest(
        nom = "Test",
        prenom = "Test",
        email = "test@test.com",
        numTel = "0000",
        numPermis = "P1",
        dateNaiss = "1990-01-01",
        nationalite = "Française"
    )
}