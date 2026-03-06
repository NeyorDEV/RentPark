package com.example.rentparkkotlin.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import kotlinx.coroutines.launch
import androidx.compose.runtime.*
import com.example.rentparkkotlin.model.Voiture
import com.example.rentparkkotlin.repository.CarRepository

class CarViewModel : ViewModel() {

    private val repository = CarRepository()

    var voitures by mutableStateOf<List<Voiture>>(emptyList())
        private set

    var isLoading by mutableStateOf(false)
        private set

    var error by mutableStateOf<String?>(null)
        private set

    init {
        fetchVoitures()
    }

    private fun fetchVoitures() {
        viewModelScope.launch {
            isLoading = true
            try {
                voitures = repository.getVoitures()
            } catch (e: Exception) {
                error = e.message
            }
            isLoading = false
        }
    }
}