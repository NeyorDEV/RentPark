package com.example.rentparkkotlin.repository

import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.MonthlyIncomeResponse
import com.example.rentparkkotlin.model.MostRentedCarResponse
import com.example.rentparkkotlin.model.TotalUsersResponse

class StatsRepository {
    suspend fun getTotalUsers(): TotalUsersResponse {
        return RetrofitInstance.apiStats.getTotalUsers()
    }

    suspend fun getMostRentedCar(): MostRentedCarResponse {
        return RetrofitInstance.apiStats.getMostRentedCar()
    }

    suspend fun getMonthlyIncome(): MonthlyIncomeResponse {
        return RetrofitInstance.apiStats.getMonthlyIncome()
    }
}