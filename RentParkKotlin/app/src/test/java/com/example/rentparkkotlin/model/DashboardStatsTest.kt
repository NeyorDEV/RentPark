package com.example.rentparkkotlin.model

import com.google.gson.Gson
import org.junit.Assert.assertEquals
import org.junit.Assert.assertNotNull
import org.junit.Assert.assertNull
import org.junit.Assert.assertTrue
import org.junit.Test

class DashboardStatsTest {

    private val gson = Gson()

    @Test
    fun `test deserialisation TotalUsersResponse`() {
        val json = """{"total_users": 150}"""
        val response = gson.fromJson(json, TotalUsersResponse::class.java)

        assertEquals(150, response.totalUsers)
    }

    @Test
    fun `test deserialisation MostRentedCarResponse succes`() {
        val json = """
            {
                "voiture_plus_louee": {
                    "Marque": "Renault",
                    "Modele": "Clio",
                    "nb_locations": 25
                }
            }
        """.trimIndent()

        val response = gson.fromJson(json, MostRentedCarResponse::class.java)

        assertNotNull(response.voiturePlusLouee)
        assertEquals("Renault", response.voiturePlusLouee?.marque)
        assertEquals(25, response.voiturePlusLouee?.nbLocations)
    }

    @Test
    fun `test deserialisation MostRentedCarResponse si vide`() {
        // Test le cas où aucune voiture n'a encore été louée
        val json = """{"voiture_plus_louee": null}"""
        val response = gson.fromJson(json, MostRentedCarResponse::class.java)

        assertNull(response.voiturePlusLouee)
    }

    @Test
    fun `test deserialisation MonthlyIncomeResponse`() {
        val json = """{"monthlyIncome": 1250.50}"""
        val response = gson.fromJson(json, MonthlyIncomeResponse::class.java)

        // Utilisation d'un delta pour la comparaison de Float
        assertEquals(1250.50f, response.monthlyIncome, 0.001f)
    }

    @Test
    fun `test deserialisation CTExpireResponse avec liste`() {
        val json = """
            {
                "vehicules_controle_technique_bientot_expire": [
                    {
                        "Marque": "Peugeot",
                        "Modele": "208",
                        "DateExpirationControleTech": "2024-12-01"
                    },
                    {
                        "Marque": "Tesla",
                        "Modele": "Model 3",
                        "DateExpirationControleTech": "2025-01-15"
                    }
                ]
            }
        """.trimIndent()

        val response = gson.fromJson(json, CTExpireResponse::class.java)

        assertEquals(2, response.vehicules.size)
        assertEquals("Peugeot", response.vehicules[0].marque)
        assertEquals("2025-01-15", response.vehicules[1].dateExpiration)
    }

    @Test
    fun `test deserialisation ContratsProchainsResponse liste vide`() {
        val json = """{"contrats_prochains": []}"""
        val response = gson.fromJson(json, ContratsProchainsResponse::class.java)

        assertTrue(response.contratsProchains.isEmpty())
    }

    @Test
    fun `test creation objet Rappel`() {
        // Test simple de la data class Rappel
        val rappel = Rappel("Vidange", "Prévoir vidange Scenic", "2024-06-01")

        assertEquals("Vidange", rappel.titre)
        assertEquals("2024-06-01", rappel.date)
    }

    @Test
    fun `test deserialisation ContratsProchainsResponse avec donnees completes`() {
        // Given: Un JSON contenant une liste de contrats avec des clés en PascalCase
        val json = """
            {
                "contrats_prochains": [
                    {
                        "DateDebut": "2024-04-01",
                        "DateFin": "2024-04-10",
                        "Marque": "Toyota",
                        "Modele": "Yaris"
                    }
                ]
            }
        """.trimIndent()

        // When
        val response = gson.fromJson(json, ContratsProchainsResponse::class.java)

        // Then
        assertNotNull(response.contratsProchains)
        assertEquals(1, response.contratsProchains.size)

        val contrat = response.contratsProchains[0]
        assertEquals("2024-04-01", contrat.dateDebut)
        assertEquals("2024-04-10", contrat.dateFin)
        assertEquals("Toyota", contrat.marque)
        assertEquals("Yaris", contrat.modele)
    }

    @Test
    fun `test integrity de la data class ContratProchain`() {
        // Vérifie simplement que le constructeur et les propriétés fonctionnent hors JSON
        val contrat = ContratProchain("01/01", "02/01", "Ford", "Fiesta")

        assertEquals("Ford", contrat.marque)
        assertEquals("Fiesta", contrat.modele)
    }
}