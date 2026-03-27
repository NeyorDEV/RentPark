package com.example.rentparkkotlin.model

import com.google.gson.Gson
// Removed JUnit 4 import to fix ambiguity
import org.junit.jupiter.api.Assertions.* import org.junit.jupiter.api.DisplayName
import org.junit.jupiter.api.Test

class UserTest {

    private val gson = Gson()

    @Test
    @DisplayName("User : devrait s'instancier correctement et maintenir l'égalité")
    fun `test User integrity`() {
        val user1 = User(1, "john_doe", "ADMIN")
        val user2 = User(1, "john_doe", "ADMIN")

        assertAll(
            { assertEquals(1, user1.id) },
            { assertEquals("john_doe", user1.username) },
            { assertEquals("ADMIN", user1.role) },
            { assertEquals(user1, user2, "Les instances avec les mêmes données devraient être égales") },
            { assertEquals(user1.hashCode(), user2.hashCode()) }
        )
    }

    @Test
    @DisplayName("User : devrait se désérialiser correctement depuis JSON")
    fun `test User deserialization`() {
        val json = """{"id": 42, "username": "alice", "role": "USER"}"""
        val user = gson.fromJson(json, User::class.java)

        // Defined locally so they are accessible
        val expectedUser = User(42, "alice", "USER")

        assertAll(
            { assertEquals(42, user.id) },
            { assertEquals("alice", user.username) },
            { assertEquals("USER", user.role) },
            { assertEquals(expectedUser, user) }
        )
    }

    @Test
    @DisplayName("UpdateUserRequest : devrait gérer le mot de passe optionnel (null)")
    fun `test UpdateUserRequest optional password`() {
        val request = UpdateUserRequest("bob", "MODERATOR")

        assertNull(request.password, "Le mot de passe devrait être null par défaut")

        val json = gson.toJson(request)
        assertFalse(json.contains("password"), "Le champ password ne devrait pas être présent dans le JSON s'il est null")
    }

    @Test
    @DisplayName("UpdateUserRequest : test de la fonction copy()")
    fun `test UpdateUserRequest copy`() {
        val original = UpdateUserRequest("dev_user", "USER")
        val updated = original.copy(role = "ADMIN", password = "new_password")

        assertAll(
            { assertEquals("dev_user", updated.username) },
            { assertEquals("ADMIN", updated.role) },
            { assertEquals("new_password", updated.password) }
        )
    }
}