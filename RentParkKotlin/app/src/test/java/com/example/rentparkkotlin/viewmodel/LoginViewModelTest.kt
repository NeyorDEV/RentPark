package com.example.rentparkkotlin.viewmodel

import android.app.Application
import com.example.rentparkkotlin.data.AuthPrefs
import com.example.rentparkkotlin.model.LoginResponse
import com.example.rentparkkotlin.repository.AuthRepository
import io.mockk.coEvery
import io.mockk.coVerify
import io.mockk.every
import io.mockk.mockk
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.test.*
import okhttp3.ResponseBody.Companion.toResponseBody
import org.junit.After
import org.junit.Assert.*
import org.junit.Before
import org.junit.Test
import retrofit2.Response

@OptIn(ExperimentalCoroutinesApi::class)
class LoginViewModelTest {

    private val testDispatcher = StandardTestDispatcher()
    private lateinit var application: Application
    private lateinit var repository: AuthRepository
    private lateinit var authPrefs: AuthPrefs
    private lateinit var viewModel: LoginViewModel

    @Before
    fun setup() {
        Dispatchers.setMain(testDispatcher)

        // On mock tout l'environnement nécessaire
        application = mockk(relaxed = true)
        repository = mockk()
        authPrefs = mockk(relaxed = true) // relaxed = true permet de ne pas avoir à mocker les méthodes qui ne renvoient rien (comme saveAuthInfo)

        // Instanciation du ViewModel avec nos mocks
        viewModel = LoginViewModel(application, repository, authPrefs)
    }

    @After
    fun tearDown() {
        Dispatchers.resetMain()
    }

    // --- Tests des champs vides ---

    @Test
    fun `login avec champs vides affiche une erreur et ne lance pas la requete`() = runTest {
        viewModel.username = ""
        viewModel.password = ""

        viewModel.login()

        assertEquals("Veuillez remplir tous les champs", viewModel.error)
        assertFalse(viewModel.isLoading)
        // Vérifie que le repository n'a JAMAIS été appelé
        coVerify(exactly = 0) { repository.login(any()) }
    }

    // --- Tests de succès ---

    @Test
    fun `login succes API sauvegarde les infos et valide la connexion`() = runTest {
        // Given : L'utilisateur a rempli les champs
        viewModel.username = "JeanDupont"
        viewModel.password = "password123"

        // Given : L'API répond un succès parfait
        val fakeResponse = mockk<LoginResponse>(relaxed = true) {
            every { status } returns "success"
            every { token } returns "mon_super_token"
            every { user } returns mockk(relaxed = true) {
                every { role } returns "admin"
                every { username } returns "JeanDupont"
            }
        }
        coEvery { repository.login(any()) } returns Response.success(fakeResponse)

        // When
        viewModel.login()
        testDispatcher.scheduler.advanceUntilIdle()

        // Then
        assertFalse(viewModel.isLoading)
        assertTrue(viewModel.loginSuccess)
        assertNull(viewModel.error)

        // Vérifie que l'enregistrement dans les SharedPreferences a bien eu lieu avec les bonnes infos
        coVerify { authPrefs.saveAuthInfo("mon_super_token", "admin", "JeanDupont", -1) }
    }

    // --- Tests d'erreurs logiques et réseau ---

    @Test
    fun `login succes HTTP mais statut API en erreur affiche le message d erreur API`() = runTest {
        viewModel.username = "JeanDupont"
        viewModel.password = "wrongpass"

        val fakeResponse = mockk<LoginResponse>(relaxed = true) {
            every { status } returns "error"
            every { message } returns "Mot de passe invalide"
        }
        coEvery { repository.login(any()) } returns Response.success(fakeResponse)

        viewModel.login()
        testDispatcher.scheduler.advanceUntilIdle()

        assertFalse(viewModel.loginSuccess)
        assertEquals("Mot de passe invalide", viewModel.error)
        coVerify(exactly = 0) { authPrefs.saveAuthInfo(any(), any(), any(), any()) }
    }

    @Test
    fun `login erreur HTTP (401) declenche le fallback d erreur d identifiants`() = runTest {
        viewModel.username = "Inconnu"
        viewModel.password = "1234"

        // Given : L'API répond une erreur HTTP (ex: 401 Unauthorized)
        val errorJson = """{"error": "User not found"}"""
        val errorResponseBody = errorJson.toResponseBody(null)
        coEvery { repository.login(any()) } returns Response.error(401, errorResponseBody)

        viewModel.login()
        testDispatcher.scheduler.advanceUntilIdle()

        assertFalse(viewModel.loginSuccess)
        assertEquals("User not found", viewModel.error)
    }

    @Test
    fun `login coupure de connexion capture l exception et affiche erreur reseau`() = runTest {
        viewModel.username = "JeanDupont"
        viewModel.password = "password"

        // Given : Le repository jette une exception (ex: Timeout, pas de WiFi)
        coEvery { repository.login(any()) } throws Exception("Timeout serveur")

        viewModel.login()
        testDispatcher.scheduler.advanceUntilIdle()

        assertFalse(viewModel.loginSuccess)
        assertEquals("Erreur réseau : Timeout serveur", viewModel.error)
    }

}