package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.Contrat

class ContratRepository {
    suspend fun getContrats(): List<Contrat> {
        return RetrofitInstance.apiContrat.getContrats()
    }

    suspend fun addContrat(contrat: Contrat): Contrat {
        return RetrofitInstance.apiContrat.addContrat(contrat)
    }

    suspend fun updateContrat(id: Int, contrat: Contrat): Contrat {
        return RetrofitInstance.apiContrat.updateContrat(id, contrat)
    }

    suspend fun deleteContrat(id: Int): Contrat {
        return RetrofitInstance.apiContrat.deleteContrat(id)
    }

    suspend fun getContrat(id: Int): Contrat {
        return RetrofitInstance.apiContrat.getContrat(id)
    }

    suspend fun updateContratStatut(id: Int, nouveauStatut: String) {
        val body = mapOf("Statut" to nouveauStatut)
        RetrofitInstance.apiContrat.updateContratStatut(id, body)
    }

    suspend fun partialUpdateContrat(id: Int, contrat: Contrat): Contrat {
        return RetrofitInstance.apiContrat.partialUpdateContrat(id, contrat)
    }
}