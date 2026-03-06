package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.Voiture

class CarRepository {

    suspend fun getVoitures(): List<Voiture> {
        return RetrofitInstance.api.getVoitures()
    }
}