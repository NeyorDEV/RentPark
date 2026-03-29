package com.example.rentparkkotlin.viewmodel

import android.net.Uri
import com.example.rentparkkotlin.ui.photoDevice.MAX_PHOTOS
import io.mockk.mockk
import org.junit.Assert.assertEquals
import org.junit.Assert.assertTrue
import org.junit.Before
import org.junit.Test

class PhotoViewModelTest {

    private lateinit var viewModel: PhotoViewModel

    @Before
    fun setup() {
        // On recrée un ViewModel tout neuf avant chaque test
        viewModel = PhotoViewModel()
    }

    // --- Tests de addPhoto ---

    @Test
    fun `addPhoto ajoute une URI quand la limite n est pas atteinte`() {
        // Given
        val mockUri = mockk<Uri>()

        // When
        viewModel.addPhoto(mockUri)

        // Then
        assertEquals(1, viewModel.photos.size)
        assertEquals(mockUri, viewModel.photos[0])
    }

    @Test
    fun `addPhoto n ajoute rien si MAX_PHOTOS est atteint`() {
        // Given : On remplit la liste jusqu'au maximum autorisé
        val mockUri = mockk<Uri>()
        for (i in 0 until MAX_PHOTOS) {
            viewModel.addPhoto(mockUri)
        }

        // Vérification intermédiaire : on est bien au max
        assertEquals(MAX_PHOTOS, viewModel.photos.size)

        // When : On essaie d'en ajouter une de plus
        val extraUri = mockk<Uri>()
        viewModel.addPhoto(extraUri)

        // Then : La taille n'a pas bougé et la photo supplémentaire n'y est pas
        assertEquals(MAX_PHOTOS, viewModel.photos.size)
        assertTrue(!viewModel.photos.contains(extraUri))
    }

    // --- Tests de addPhotos (par lot) ---

    @Test
    fun `addPhotos ajoute plusieurs URIs d un coup`() {
        // Given
        val uri1 = mockk<Uri>()
        val uri2 = mockk<Uri>()
        val uris = listOf(uri1, uri2)

        // When
        viewModel.addPhotos(uris)

        // Then
        assertEquals(2, viewModel.photos.size)
        assertEquals(uri1, viewModel.photos[0])
        assertEquals(uri2, viewModel.photos[1])
    }

    @Test
    fun `addPhotos tronque la liste ajoutee si on depasse MAX_PHOTOS`() {
        // Given : On remplit la liste pour qu'il ne reste qu'UNE SEULE place (MAX_PHOTOS - 1)
        val mockUri = mockk<Uri>()
        for (i in 0 until MAX_PHOTOS - 1) {
            viewModel.addPhoto(mockUri)
        }

        // On crée une liste de 3 nouvelles photos
        val newPhotos = listOf(mockk<Uri>(), mockk<Uri>(), mockk<Uri>())

        // When : On essaie d'ajouter les 3 photos alors qu'il ne reste qu'une place
        viewModel.addPhotos(newPhotos)

        // Then : La liste a atteint son max pile poil, et le programme n'a pas crashé
        assertEquals(MAX_PHOTOS, viewModel.photos.size)

        // On vérifie que seule la PREMIÈRE photo de la nouvelle liste a pu rentrer
        assertEquals(newPhotos[0], viewModel.photos.last())
    }

    // --- Tests de removePhoto ---

    @Test
    fun `removePhoto supprime la photo au bon index`() {
        // Given
        val uri1 = mockk<Uri>()
        val uri2 = mockk<Uri>()
        val uri3 = mockk<Uri>()
        viewModel.addPhotos(listOf(uri1, uri2, uri3))

        // When : On supprime la photo du milieu (index 1)
        viewModel.removePhoto(1)

        // Then
        assertEquals(2, viewModel.photos.size)
        assertEquals(uri1, viewModel.photos[0])
        assertEquals(uri3, viewModel.photos[1]) // uri3 a glissé à l'index 1
    }

    @Test
    fun `removePhoto ne fait rien et ne crash pas si index negatif`() {
        val uri = mockk<Uri>()
        viewModel.addPhoto(uri)

        // When
        viewModel.removePhoto(-1)

        // Then
        assertEquals(1, viewModel.photos.size)
    }

    @Test
    fun `removePhoto ne fait rien et ne crash pas si index trop grand`() {
        val uri = mockk<Uri>()
        viewModel.addPhoto(uri)

        // When (la liste n'a qu'un élément à l'index 0)
        viewModel.removePhoto(5)

        // Then
        assertEquals(1, viewModel.photos.size)
    }
}