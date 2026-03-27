package com.example.rentparkkotlin

import kotlinx.serialization.Serializable

sealed interface Routes {
    @Serializable
    data object LoginRoute

    @Serializable
    data object HomeCustomerRoute

    @Serializable
    data object DashBoardRoute

    @Serializable
    data object CarRoute

    @Serializable
    data object ContratRoute

    @Serializable
    data object UserRoute

    @Serializable
    data object PlanningRoute

    @Serializable
    data object PhotoRoute

    @Serializable
    data object SettingsRoute
}