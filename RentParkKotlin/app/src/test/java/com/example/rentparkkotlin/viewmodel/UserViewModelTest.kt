package com.example.rentparkkotlin.viewmodel

import com.example.rentparkkotlin.MainDispatcherRule
import com.example.rentparkkotlin.model.RegisterRequest
import com.example.rentparkkotlin.model.RegisterResponse
import com.example.rentparkkotlin.model.UpdateUserRequest
import com.example.rentparkkotlin.model.User
import com.example.rentparkkotlin.repository.UserRepository
import io.mockk.coEvery
import io.mockk.coVerify
import io.mockk.mockk
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.test.runTest
import org.junit.Assert.*
import org.junit.Before
import org.junit.Rule
import org.junit.Test
import retrofit2.Response


@OptIn(ExperimentalCoroutinesApi::class)
class UserViewModelTest {

    // Règle pour rediriger les coroutines du Main thread vers notre thread de test
    @get:Rule
    val mainDispatcherRule = MainDispatcherRule()

    private lateinit var repository: UserRepository
    private lateinit var viewModel: UserViewModel

    private val mockUserList = listOf(
        User(id = 1, username = "JohnDoe", role = "Admin")
    )

    @Before
    fun setUp() {
        // On mock le repository
        repository = mockk()

        // Le bloc `init` du ViewModel appelle `fetchUsers()` à la création.
        // Il faut donc mocker le retour par défaut de getUsers() AVANT d'instancier le ViewModel.
        coEvery { repository.getUsers() } returns mockUserList

        viewModel = UserViewModel(repository)
    }

    // ==========================================
    // TESTS POUR: fetchUsers() / init {}
    // ==========================================

    @Test
    fun `init trigger fetchUsers et charge la liste des utilisateurs avec succes`() = runTest {
        // Vérification de l'état final après l'appel (géré par le UnconfinedTestDispatcher)
        assertEquals(mockUserList, viewModel.users)
        assertNull(viewModel.error)
        assertFalse(viewModel.isLoading)
    }

    @Test
    fun `fetchUsers declenche une exception et met a jour le message d'erreur`() = runTest {
        // Given: Le repository lance une erreur
        val errorMessage = "Timeout"
        coEvery { repository.getUsers() } throws Exception(errorMessage)

        // When
        viewModel.fetchUsers()

        // Then
        assertTrue(viewModel.error?.contains(errorMessage) == true)
        assertTrue(viewModel.users.isEmpty() || viewModel.users == mockUserList) // L'ancienne liste reste
        assertFalse(viewModel.isLoading)
    }

    // ==========================================
    // TESTS POUR: addUser()
    // ==========================================

    @Test
    fun `addUser succes rappelle fetchUsers`() = runTest {
        // Given
        val response = mockk<Response<RegisterResponse>>()
        coEvery { response.isSuccessful } returns true
        coEvery { repository.addUser(any()) } returns response

        // When
        viewModel.addUser("Alice", "User", "password123")

        // Then
        coVerify { repository.addUser(RegisterRequest("Alice", "password123", "User")) }
        coVerify(atLeast = 2) { repository.getUsers() } // 1x dans le init, 1x après le succès
        assertNull(viewModel.error)
    }

    @Test
    fun `addUser echoue (code HTTP erreur) met a jour le message d'erreur`() = runTest {
        // Given
        val response = mockk<Response<RegisterResponse>>()
        coEvery { response.isSuccessful } returns false
        coEvery { repository.addUser(any()) } returns response

        // When
        viewModel.addUser("Alice", "User", "password123")

        // Then
        assertEquals("Erreur lors de l'ajout", viewModel.error)
    }

    @Test
    fun `addUser lance une exception reseau met a jour le message d'erreur`() = runTest {
        // Given
        coEvery { repository.addUser(any()) } throws Exception("No internet")

        // When
        viewModel.addUser("Alice", "User", "password123")

        // Then
        assertTrue(viewModel.error?.contains("Erreur réseau") == true)
    }

    // ==========================================
    // TESTS POUR: updateUser()
    // ==========================================

    @Test
    fun `updateUser avec mot de passe succes rappelle fetchUsers`() = runTest {
        // Given
        val response = mockk<Response<Unit>>()
        coEvery { response.isSuccessful } returns true
        coEvery { repository.updateUser(any(), any()) } returns response

        // When
        viewModel.updateUser(1, "Bob", "Admin", "newPass")

        // Then
        coVerify { repository.updateUser(1, UpdateUserRequest("Bob", "Admin", "newPass")) }
        coVerify(atLeast = 2) { repository.getUsers() }
    }

    @Test
    fun `updateUser avec mot de passe null ou vide envoie un null au repository`() = runTest {
        // Given
        val response = mockk<Response<Unit>>()
        coEvery { response.isSuccessful } returns true
        coEvery { repository.updateUser(any(), any()) } returns response

        // When (mot de passe vide "")
        viewModel.updateUser(1, "Bob", "Admin", "")

        // Then : Vérifier que le mot de passe envoyé dans la requête est null
        coVerify { repository.updateUser(1, UpdateUserRequest("Bob", "Admin", null)) }
    }

    @Test
    fun `updateUser echoue met a jour l'erreur`() = runTest {
        // Given
        val response = mockk<Response<Unit>>()
        coEvery { response.isSuccessful } returns false
        coEvery { repository.updateUser(any(), any()) } returns response

        // When
        viewModel.updateUser(1, "Bob", "Admin", null)

        // Then
        assertEquals("Erreur lors de la modification", viewModel.error)
    }

    @Test
    fun `updateUser lance une exception met a jour l'erreur`() = runTest {
        // Given
        coEvery { repository.updateUser(any(), any()) } throws Exception("Crash")

        // When
        viewModel.updateUser(1, "Bob", "Admin", null)

        // Then
        assertTrue(viewModel.error?.contains("Erreur réseau: Crash") == true)
    }

    // ==========================================
    // TESTS POUR: deleteUser()
    // ==========================================

    @Test
    fun `deleteUser succes rappelle fetchUsers`() = runTest {
        // Given
        val response = mockk<Response<Unit>>()
        coEvery { response.isSuccessful } returns true
        coEvery { repository.deleteUser(1) } returns response

        // When
        viewModel.deleteUser(1)

        // Then
        coVerify { repository.deleteUser(1) }
        coVerify(atLeast = 2) { repository.getUsers() }
    }

    @Test
    fun `deleteUser echoue met a jour l'erreur`() = runTest {
        // Given
        val response = mockk<Response<Unit>>()
        coEvery { response.isSuccessful } returns false
        coEvery { repository.deleteUser(1) } returns response

        // When
        viewModel.deleteUser(1)

        // Then
        assertEquals("Erreur lors de la suppression", viewModel.error)
    }

    @Test
    fun `deleteUser lance une exception met a jour l'erreur`() = runTest {
        // Given
        coEvery { repository.deleteUser(any()) } throws Exception("Crash")

        // When
        viewModel.deleteUser(1)

        // Then
        assertTrue(viewModel.error?.contains("Erreur réseau: Crash") == true)
    }
}