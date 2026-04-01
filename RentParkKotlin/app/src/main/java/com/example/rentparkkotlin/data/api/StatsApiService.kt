package com.example.rentparkkotlin.data.api

import com.example.rentparkkotlin.model.MonthlyIncomeResponse
import com.example.rentparkkotlin.model.MostRentedCarResponse
import com.example.rentparkkotlin.model.TotalUsersResponse
import retrofit2.http.GET

interface StatsApiService {
    @GET("stats/total-users")
    suspend fun getTotalUsers(): TotalUsersResponse

    @GET("stats/voiture-plus-louee")
    suspend fun getMostRentedCar(): MostRentedCarResponse

    @GET("stats/revenus-mensuel")
    suspend fun getMonthlyIncome(): MonthlyIncomeResponse
}