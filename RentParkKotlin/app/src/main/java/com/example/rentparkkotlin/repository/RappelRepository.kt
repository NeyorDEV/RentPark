package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.Rappel

class RappelRepository {
    suspend fun getRappels(): List<Rappel> {
        return RetrofitInstance.apiRappel.getRappels()
    }

    suspend fun addRappel(rappel: Rappel): Rappel {
        return RetrofitInstance.apiRappel.addRappel(rappel)
    }
}