package com.example.rentparkkotlin.ui.header

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxHeight
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.tooling.preview.Preview
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import com.example.rentparkkotlin.ui.theme.BlackTheme
import com.example.rentparkkotlin.ui.theme.Orange

// Header commun à toutes les pages de rentpark (au moins les pages admin)

@Composable
fun Header(Title: String){
    Column() {
        Box(modifier = Modifier.fillMaxWidth().height(22.dp).background(color = Orange))

        Row(modifier = Modifier
            .fillMaxWidth()
            .height(75.dp)
            .background(color = BlackTheme)) {
            MenuBurger();
            TitlePart(Title);
        }
    }


}

@Composable
fun TitlePart(title: String) {
    Row(modifier = Modifier
        .fillMaxWidth()
        .fillMaxHeight(),
        verticalAlignment = Alignment.CenterVertically) {
        Box(modifier = Modifier
            .fillMaxWidth()){
            Text(text = "Contrats",
                color = Color.White,
                fontSize = 40.sp,
                fontWeight = FontWeight.Bold,
                modifier = Modifier.fillMaxWidth().padding(end = 75.dp),
                textAlign = TextAlign.Center)
        }
    }
}

@Preview(showBackground = true)
@Composable
fun HeaderPreview() {
    Header("RentPark")
}