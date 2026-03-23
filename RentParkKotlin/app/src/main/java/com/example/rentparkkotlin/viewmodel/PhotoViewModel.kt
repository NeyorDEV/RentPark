package com.example.rentparkkotlin.viewmodel

import android.net.Uri
import androidx.compose.runtime.mutableStateListOf
import androidx.lifecycle.ViewModel
import com.example.rentparkkotlin.ui.photoDevice.MAX_PHOTOS

public class PhotoViewModel : ViewModel() {
    val photos = mutableStateListOf<Uri>()

    fun addPhoto(uri: Uri) {
        if (photos.size < MAX_PHOTOS) photos.add(uri)
    }

    fun addPhotos(uris: List<Uri>) {
        val available = MAX_PHOTOS - photos.size
        uris.take(available).forEach { photos.add(it) }
    }

    fun removePhoto(index: Int) {
        if (index in photos.indices) photos.removeAt(index)
    }
}
