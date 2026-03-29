package com.example.rentparkkotlin.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.Contrat
import com.example.rentparkkotlin.repository.ContratRepository
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

class PlanningViewModel(private val repository: ContratRepository) : ViewModel() {
    private val _contrats = MutableStateFlow<List<Contrat>>(emptyList())
    val contrats: StateFlow<List<Contrat>> = _contrats

    init {
        fetchContrats()
    }

    private fun fetchContrats() {
        viewModelScope.launch {
            try {
                // ✅ On utilise le repository injecté !
                val response = repository.getContrats()
                _contrats.value = response
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
    }
}