package com.example.rentparkkotlin.model


import com.google.gson.Gson
import org.junit.Assert.*
import org.junit.Before
import org.junit.Test

class AuthTest {

    private lateinit var gson: Gson

    @Before
    fun setUp() {
        gson = Gson()
    }

    // --- Tests LoginRequest ---
    @Test
    fun `LoginRequest should hold correct values`() {
        val request = LoginRequest("alice", "password123")
        assertEquals("alice", request.username)
        assertEquals("password123", request.password)
    }

    // --- Tests LoginResponse (Vérification SerializedName) ---
    @Test
    fun `LoginResponse should deserialize correctly from JSON`() {
        val json = """
            {
                "status": "success",
                "token": "abc-123",
                "user": { "username": "alice", "role": "admin" },
                "message": "Welcome"
            }
        """.trimIndent()

        val response = gson.fromJson(json, LoginResponse::class.java)

        assertEquals("success", response.status)
        assertEquals("abc-123", response.token)
        assertEquals("alice", response.user?.username)
        assertEquals("admin", response.user?.role)
        assertNull(response.error)
    }

    // --- Tests UserInfo ---
    @Test
    fun `UserInfo should support null values for safety`() {
        val userInfo = UserInfo(null, null)
        assertNull(userInfo.username)
        assertNull(userInfo.role)
    }

    // --- Tests RegisterRequest ---
    @Test
    fun `RegisterRequest copy method should work correctly`() {
        val original = RegisterRequest("bob", "secret", "user")
        val updated = original.copy(role = "admin")

        assertEquals("bob", updated.username)
        assertEquals("admin", updated.role)
        assertEquals("secret", updated.password)
    }

    // --- Tests RegisterResponse ---
    @Test
    fun `RegisterResponse handles error message mapping`() {
        val json = """ { "error": "User already exists" } """
        val response = gson.fromJson(json, RegisterResponse::class.java)

        assertEquals("User already exists", response.error)
        assertNull(response.message)
    }
}