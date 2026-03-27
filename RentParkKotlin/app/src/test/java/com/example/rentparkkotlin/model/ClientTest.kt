package com.example.rentparkkotlin.model

import com.google.gson.Gson
import org.junit.Assert.assertEquals
import org.junit.Assert.assertNull
import org.junit.Test

class ClientTest {

    private val gson = Gson()

    @Test
    fun `test deserialisation Client complet depuis JSON`() {
        val json = """
            {
                "IdClient": 10,
                "Nom": "Dupont",
                "Prenom": "Jean",
                "Email": "jean.dupont@email.com",
                "NumTel": "0601020304",
                "NumPermis": "ABC12345",
                "DateNaiss": "1990-05-15",
                "Nationalite": "Française"
            }
        """.trimIndent()

        val client = gson.fromJson(json, Client::class.java)

        assertEquals(10, client.idClient)
        assertEquals("Dupont", client.nom)
        assertEquals("0601020304", client.numTel)
        assertEquals("1990-05-15", client.dateNaiss)
        assertEquals("Française", client.nationalite)
        assertEquals("ABC12345", client.numPermis)
        assertEquals("Jean", client.prenom)
        assertEquals("jean.dupont@email.com", client.email)
    }

    @Test
    fun `test Client avec valeurs optionnelles nulles`() {
        // Ici on omet NumTel, DateNaiss et Nationalite dans le JSON
        val json = """
            {
                "IdClient": 11,
                "Nom": "Martin",
                "Prenom": "Alice",
                "Email": "alice@email.com",
                "NumPermis": "XYZ987"
            }
        """.trimIndent()

        val client = gson.fromJson(json, Client::class.java)

        assertEquals(11, client.idClient)
        assertNull(client.numTel)
        assertNull(client.dateNaiss)
        assertNull(client.nationalite)
        assertEquals("Alice", client.prenom)
        assertEquals("XYZ987", client.numPermis)
        assertEquals("Martin", client.nom)
        assertEquals("alice@email.com", client.email)
    }

    @Test
    fun `test serialisation ClientRequest vers JSON`() {
        // Given
        val request = ClientRequest(
            nom = "Durand",
            prenom = "Paul",
            email = "paul@email.com",
            numTel = null,
            numPermis = "PERM99",
            dateNaiss = "1985-10-20",
            nationalite = "Belge"
        )

        // When
        val json = gson.toJson(request)

        // Then
        org.junit.Assert.assertTrue("Le JSON doit contenir la clé Nom", json.contains("\"Nom\":\"Durand\""))
        org.junit.Assert.assertTrue("Le JSON doit contenir la clé NumPermis", json.contains("\"NumPermis\":\"PERM99\""))


        org.junit.Assert.assertFalse("Le JSON ne doit pas contenir NumTel s'il est null", json.contains("\"NumTel\""))
    }

    @Test
    fun `test deserialisation ClientResponse succes`() {
        val json = """
            {
                "message": "Client créé avec succès",
                "idClient": 500
            }
        """.trimIndent()

        val response = gson.fromJson(json, ClientResponse::class.java)

        assertEquals("Client créé avec succès", response.message)
        assertEquals(500, response.idClient)
        assertNull(response.error)
    }

    @Test
    fun `test deserialisation ClientResponse erreur`() {
        val json = """
            {
                "error": "Email déjà utilisé"
            }
        """.trimIndent()

        val response = gson.fromJson(json, ClientResponse::class.java)

        assertNull(response.message)
        assertNull(response.idClient)
        assertEquals("Email déjà utilisé", response.error)
    }
}