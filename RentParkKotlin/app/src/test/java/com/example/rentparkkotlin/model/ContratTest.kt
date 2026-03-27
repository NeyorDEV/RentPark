package com.example.rentparkkotlin.model

import org.junit.Test

import org.junit.Assert.*

import com.google.gson.Gson
import org.junit.Assert.assertEquals
import org.junit.Assert.assertNull


class ContratTest {

    private val gson = Gson()

    @Test
    fun `test de la creation d un contrat et verification des proprietes`() {
        // Given
        val contrat = Contrat(
            idContrat = 1,
            dateDebut = "2024-03-27",
            dateFin = "2024-03-30",
            statut = "Actif",
            idClient = 101,
            idVehicule = "ABC-123"
        )

        // Then
        assertEquals(1, contrat.idContrat)
        assertEquals("2024-03-27", contrat.dateDebut)
        assertEquals("2024-03-30", contrat.dateFin)
        assertEquals("Actif", contrat.statut)
        assertEquals(101, contrat.idClient)
        assertEquals("ABC-123", contrat.idVehicule)
    }

    @Test
    fun `test deserialisation JSON vers objet Contrat`() {
        // Given: Un flux JSON simulant un retour d'API
        val json = """
            {
                "IdContrat": 42,
                "DateDebut": "2024-01-01",
                "DateFin": "2024-01-10",
                "Statut": "Terminé",
                "IdClient": 5,
                "IdVehicule": "XYZ-999"
            }
        """.trimIndent()

        // When
        val contrat = gson.fromJson(json, Contrat::class.java)

        // Then
        assertEquals(42, contrat.idContrat)
        assertEquals("2024-01-01", contrat.dateDebut)
        assertEquals("2024-01-10", contrat.dateFin)
        assertEquals("XYZ-999", contrat.idVehicule)
        assertEquals("Terminé", contrat.statut)
        assertEquals(5, contrat.idClient)
    }

    @Test
    fun `test gestion des valeurs nulles lors de la deserialisation`() {
        // Given: Un JSON où les champs optionnels sont absents
        val json = """
            {
                "IdContrat": 99,
                "DateDebut": "2024-05-01",
                "DateFin": "2024-05-05"
            }
        """.trimIndent()

        // When
        val contrat = gson.fromJson(json, Contrat::class.java)

        // Then
        assertEquals(99, contrat.idContrat)
        assertNull(contrat.statut)
        assertNull(contrat.idClient)
        assertNull(contrat.idVehicule)
    }

    @Test
    fun `test copy de la data class`() {
        // Given
        val contratOriginal = Contrat(1, "2024-01-01", "2024-01-10", "Old", 1, "V1")

        // When: On modifie juste le statut via copy()
        val contratModifie = contratOriginal.copy(statut = "New")

        // Then
        assertEquals("New", contratModifie.statut)
        assertEquals(contratOriginal.idContrat, contratModifie.idContrat)
        assertEquals(contratOriginal.dateDebut, contratModifie.dateDebut)
        assertEquals(contratOriginal.dateFin, contratModifie.dateFin)
        assertEquals(contratOriginal.idClient, contratModifie.idClient)
        assertEquals(contratOriginal.idVehicule, contratModifie.idVehicule)
    }
}