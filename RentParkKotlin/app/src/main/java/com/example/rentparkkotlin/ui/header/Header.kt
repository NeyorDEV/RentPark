package com.example.rentparkkotlin.ui.header

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.tooling.preview.Preview

// Header commun à toutes les pages de rentpark (au moins les pages admin)

@Composable
fun Header(Title: String){
    Row(modifier = Modifier
        .fillMaxWidth()
        .background(Color.Gray)) {
        MenuBurger();
        TitlePart(Title);
    }
}

@Composable
fun TitlePart(title: String) {
    Row(modifier = Modifier
        .fillMaxWidth()) {
        Box(modifier = Modifier
            .fillMaxWidth()
            ,contentAlignment = Alignment.CenterB){
            Text(text = title)
        }
    }
}

@Preview(showBackground = true)
@Composable
fun HeaderPreview() {
    Header("RentPark")
}