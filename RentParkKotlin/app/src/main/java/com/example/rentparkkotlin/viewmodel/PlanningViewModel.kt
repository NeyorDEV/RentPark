package com.example.rentparkkotlin.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.rentparkkotlin.data.RetrofitInstance
import com.example.rentparkkotlin.model.Contrat
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

class PlanningViewModel : ViewModel() {
    private val _contrats = MutableStateFlow<List<Contrat>>(emptyList())
    val contrats: StateFlow<List<Contrat>> = _contrats

    init {
        fetchContrats()
    }

    private fun fetchContrats() {
        viewModelScope.launch {
            try {
                val response = RetrofitInstance.apiContrat.getContrats()
                _contrats.value = response
            } catch (e: Exception) {
                e.printStackTrace()
            }
        }
    }
}