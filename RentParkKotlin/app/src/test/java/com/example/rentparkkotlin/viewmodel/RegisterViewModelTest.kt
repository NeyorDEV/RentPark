package com.example.rentparkkotlin.viewmodel

import com.example.rentparkkotlin.MainDispatcherRule
import com.example.rentparkkotlin.model.RegisterRequest
import com.example.rentparkkotlin.model.RegisterResponse
import com.example.rentparkkotlin.repository.AuthRepository
import io.mockk.coEvery
import io.mockk.coVerify
import io.mockk.mockk
import kotlinx.coroutines.ExperimentalCoroutinesApi
import kotlinx.coroutines.test.runTest
import okhttp3.ResponseBody.Companion.toResponseBody
import org.junit.Assert.*
import org.junit.Before
import org.junit.Rule
import org.junit.Test
import retrofit2.Response

@OptIn(ExperimentalCoroutinesApi::class)
class RegisterViewModelTest {

    // On réutilise la même règle que pour le UserViewModelTest
    @get:Rule
    val mainDispatcherRule = MainDispatcherRule()

    private lateinit var repository: AuthRepository
    private lateinit var viewModel: RegisterViewModel

    @Before
    fun setUp() {
        repository = mockk()
        viewModel = RegisterViewModel(repository)
    }

    // ==========================================
    // TESTS DES VERIFICATIONS LOCALES (Saisie)
    // ==========================================

    @Test
    fun `register avec champs vides met a jour l'erreur`() {
        // Given (Un champ est vide)
        viewModel.username = ""
        viewModel.password = "password123"
        viewModel.confirmPassword = "password123"

        // When
        viewModel.register()

        // Then
        assertEquals("Veuillez remplir tous les champs", viewModel.error)
        assertFalse(viewModel.isLoading) // Le test s'arrête avant le lancement de la coroutine
    }

    @Test
    fun `register avec mots de passe differents met a jour l'erreur`() {
        // Given
        viewModel.username = "JohnDoe"
        viewModel.password = "password123"
        viewModel.confirmPassword = "password456"

        // When
        viewModel.register()

        // Then
        assertEquals("Les mots de passe ne correspondent pas", viewModel.error)
    }

    // ==========================================
    // TESTS DE L'APPEL API (Coroutines)
    // ==========================================

    @Test
    fun `register avec succes met a jour registerSuccess a true`() = runTest {
        // Given
        viewModel.username = "JohnDoe"
        viewModel.password = "password123"
        viewModel.confirmPassword = "password123"

        val response = mockk<Response<RegisterResponse>>()
        coEvery { response.isSuccessful } returns true
        coEvery { repository.register(any()) } returns response

        // When
        viewModel.register()

        // Then
        coVerify { repository.register(RegisterRequest("JohnDoe", "password123", "user")) }
        assertTrue(viewModel.registerSuccess)
        assertNull(viewModel.error)
        assertFalse(viewModel.isLoading)
    }

    @Test
    fun `register API erreur avec body JSON valide extrait le message d'erreur`() = runTest {
        // Given
        viewModel.username = "JohnDoe"
        viewModel.password = "password123"
        viewModel.confirmPassword = "password123"

        // On simule une réponse en erreur (ex: Code 400) avec un body JSON
        val errorJson = """{"error": "Nom d'utilisateur déjà pris"}"""
        val errorResponseBody = errorJson.toResponseBody(null)
        val response = Response.error<RegisterResponse>(400, errorResponseBody)

        coEvery { repository.register(any()) } returns response

        // When
        viewModel.register()

        // Then (Nécessite testOptions { unitTests.isReturnDefaultValues = true } dans build.gradle)
        assertEquals("Nom d'utilisateur déjà pris", viewModel.error)
        assertFalse(viewModel.registerSuccess)
    }

    @Test
    fun `register API erreur avec JSON invalide met l'erreur par defaut`() = runTest {
        // Given
        viewModel.username = "JohnDoe"
        viewModel.password = "password123"
        viewModel.confirmPassword = "password123"

        // On simule une réponse d'erreur avec un body qui n'est pas du JSON valide
        val badJson = "Ceci n'est pas du JSON"
        val errorResponseBody = badJson.toResponseBody(null)
        val response = Response.error<RegisterResponse>(400, errorResponseBody)
        coEvery { repository.register(any()) } returns response

        // When
        viewModel.register()

        // Then
        assertEquals("Erreur lors de l'inscription", viewModel.error)
        assertFalse(viewModel.registerSuccess)
    }

    @Test
    fun `register API erreur avec body null met l'erreur serveur`() = runTest {
        // Given
        viewModel.username = "JohnDoe"
        viewModel.password = "password123"
        viewModel.confirmPassword = "password123"

        val response = mockk<Response<RegisterResponse>>()
        coEvery { response.isSuccessful } returns false
        coEvery { response.errorBody() } returns null
        coEvery { repository.register(any()) } returns response

        // When
        viewModel.register()

        // Then
        assertEquals("Erreur serveur", viewModel.error)
        assertFalse(viewModel.registerSuccess)
    }

    @Test
    fun `register crash reseau (Exception) met a jour le message d'erreur`() = runTest {
        // Given
        viewModel.username = "JohnDoe"
        viewModel.password = "password123"
        viewModel.confirmPassword = "password123"

        coEvery { repository.register(any()) } throws Exception("Timeout")

        // When
        viewModel.register()

        // Then
        assertTrue(viewModel.error?.contains("Erreur réseau : Timeout") == true)
        assertFalse(viewModel.registerSuccess)
    }
}