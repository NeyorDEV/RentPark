package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.Voiture
import retrofit2.http.Body

class CarRepository {

    suspend fun getVoitures(): List<Voiture> {
        return RetrofitInstance.apiVoiture.getVoitures()
    }

    suspend fun addVoiture(@Body newVoiture: Voiture) : retrofit2.Response<Unit> {
        return RetrofitInstance.apiVoiture.addVoiture(newVoiture)
    }

    suspend fun deleteVoiture(id: String): retrofit2.Response<Unit> {
        return RetrofitInstance.apiVoiture.deleteVoiture(id)
    }
}