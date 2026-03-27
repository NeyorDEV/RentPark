package com.example.rentparkkotlin.model

import org.junit.Test

import org.junit.Assert.*

import com.google.gson.Gson
import org.junit.Assert.assertEquals
import org.junit.Assert.assertNull
import kotlin.String


class VoitureTest {
    @Test
    fun `test de la creation d une voiture et verification des proprietes`() {
        // Given
        val voiture = Voiture(
        NumSerie = "TEST22335564",
        Energie = "Essence",
        NbPlaces = "5",
        Categorie = "Berline",
        Transmission = "Intégrale",
        Boite = "Manuelle",
        Etat = "Libre",
        Puissance = "500",
        DateAchat = "2024-01-01",
        DateExpirationControleTech = "2025-01-16",
        DateDernierControleTech = "2025-01-01",
        Marque = "BMW",
        Nom = "Série 3",
        Annee = "2021",
        IdAssureur = 1,
        IdFournisseur = 1,
        ImagePath = "html/icons/cars/698c33f3cff7e_695e3039f2fd5_image_2026-01-07_110642891.png",
        Couleur = "Rouge",
        Prix = "100.00"
        )

        // Then
        assertEquals("TEST22335564", voiture.NumSerie)
        assertEquals("Essence", voiture.Energie)
        assertEquals("5", voiture.NbPlaces)
        assertEquals("Berline", voiture.Categorie)
        assertEquals("Intégrale", voiture.Transmission)
        assertEquals("Manuelle", voiture.Boite)
        assertEquals("Libre", voiture.Etat)
        assertEquals("500", voiture.Puissance)
        assertEquals("2024-01-01", voiture.DateAchat)
        assertEquals("2025-01-16", voiture.DateExpirationControleTech)
        assertEquals("2025-01-01", voiture.DateDernierControleTech)
        assertEquals("BMW", voiture.Marque)
        assertEquals("Série 3", voiture.Nom)
        assertEquals("2021", voiture.Annee)
        assertEquals(1, voiture.IdAssureur)
        assertEquals(1, voiture.IdFournisseur)
        assertEquals("html/icons/cars/698c33f3cff7e_695e3039f2fd5_image_2026-01-07_110642891.png",voiture.ImagePath)
        assertEquals("Rouge", voiture.Couleur)
        assertEquals("100.00", voiture.Prix)
    }
}

