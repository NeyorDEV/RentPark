package com.example.rentparkkotlin.model

import com.google.gson.Gson
import org.junit.Assert.* // On utilise JUnit 4
import org.junit.Test

class UserTest {

    private val gson = Gson()

    @Test
    fun `test User integrity`() {
        val user1 = User(1, "john_doe", "ADMIN")
        val user2 = User(1, "john_doe", "ADMIN")

        assertEquals(1, user1.id)
        assertEquals("john_doe", user1.username)
        assertEquals("ADMIN", user1.role)
        assertEquals("Les instances avec les mêmes données devraient être égales", user1, user2)
        assertEquals(user1.hashCode(), user2.hashCode())
    }

    @Test
    fun `test User deserialization`() {
        val json = """{"id": 42, "username": "alice", "role": "USER"}"""
        val user = gson.fromJson(json, User::class.java)

        val expectedUser = User(42, "alice", "USER")

        assertEquals(42, user.id)
        assertEquals("alice", user.username)
        assertEquals("USER", user.role)
        assertEquals(expectedUser, user)
    }

    @Test
    fun `test UpdateUserRequest optional password`() {
        val request = UpdateUserRequest("bob", "MODERATOR")

        assertNull("Le mot de passe devrait être null par défaut", request.password)

        val json = gson.toJson(request)
        // Correction : assertFalse de JUnit 4
        assertFalse("Le champ password ne devrait pas être présent dans le JSON s'il est null", json.contains("password"))
    }

    @Test
    fun `test UpdateUserRequest copy`() {
        val original = UpdateUserRequest("dev_user", "USER")
        val updated = original.copy(role = "ADMIN", password = "new_password")

        assertEquals("dev_user", updated.username)
        assertEquals("ADMIN", updated.role)
        assertEquals("new_password", updated.password)
    }
}