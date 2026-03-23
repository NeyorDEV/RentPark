package com.example.rentparkkotlin.ui.photoDevice

import android.Manifest
import android.content.Intent
import android.net.Uri
import android.os.Build
import android.os.Environment
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.compose.foundation.background
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.grid.GridCells
import androidx.compose.foundation.lazy.grid.LazyVerticalGrid
import androidx.compose.foundation.lazy.grid.itemsIndexed
import androidx.compose.foundation.shape.CircleShape
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Close
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.unit.dp
import androidx.core.content.FileProvider
import coil.compose.AsyncImage
import java.io.File
import java.text.SimpleDateFormat
import java.util.*
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.rentparkkotlin.viewmodel.PhotoViewModel

const val MAX_PHOTOS = 10

@Composable
fun PhotoDeviceScreen(
    onNavigateBack: () -> Unit = {},
    onSendPhotos: (List<Uri>) -> Unit = {},
    viewModel: PhotoViewModel = viewModel()
) {
    val context = LocalContext.current
    val photos = viewModel.photos
    var cameraUri by remember { mutableStateOf<Uri?>(null) }
    var showDialog by remember { mutableStateOf(false) }
    var permissionError by remember { mutableStateOf<String?>(null) }

    // ── Caméra ──────────────────────────────────────────────────────────────
    val cameraLauncher = rememberLauncherForActivityResult(
        ActivityResultContracts.TakePicture()
    ) { success ->
        if (success) cameraUri?.let { viewModel.addPhoto(it) }
    }

    // ── Galerie (multi-sélection) ────────────────────────────────────────────
    val galleryLauncher = rememberLauncherForActivityResult(
        ActivityResultContracts.OpenMultipleDocuments()  // meilleur que GetMultipleContents
    ) { uris ->
        val available = MAX_PHOTOS - photos.size
        uris.take(available).forEach { uri ->
            // Persistance OBLIGATOIRE avec OpenMultipleDocuments
            context.contentResolver.takePersistableUriPermission(
                uri, Intent.FLAG_GRANT_READ_URI_PERMISSION
            )
            viewModel.addPhotos(uris)
        }
    }

    // ── Permission caméra ────────────────────────────────────────────────────
    val cameraPermissionLauncher = rememberLauncherForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { granted ->
        if (granted) {
            // Permission accordée → on lance la caméra
            val ts = SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(Date())
            val file = File(
                context.getExternalFilesDir(Environment.DIRECTORY_PICTURES),
                "IMG_${ts}_.jpg"
            )
            cameraUri = FileProvider.getUriForFile(
                context, "${context.packageName}.fileprovider", file
            )
            cameraLauncher.launch(cameraUri)
        } else {
            permissionError = "Permission caméra refusée"
        }
    }

    // ── Permission galerie (Android < 13) ────────────────────────────────────
    val storagePermissionLauncher = rememberLauncherForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { granted ->
        if (granted) {
            galleryLauncher.launch(arrayOf("image/*"))
        } else {
            permissionError = "Permission stockage refusée"
        }
    }

    // ── Fonctions helper ─────────────────────────────────────────────────────
    fun openCamera() {
        cameraPermissionLauncher.launch(Manifest.permission.CAMERA)
    }

    fun openGallery() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            // Android 13+ : pas besoin de permission pour la galerie
            galleryLauncher.launch(arrayOf("image/*"))
        } else {
            // Android < 13 : demande permission stockage
            storagePermissionLauncher.launch(Manifest.permission.READ_EXTERNAL_STORAGE)
        }
    }

    // ── Dialog choix source ──────────────────────────────────────────────────
    if (showDialog) {
        AlertDialog(
            onDismissRequest = { showDialog = false },
            title = { Text("Ajouter des photos") },
            text = {
                Column {
                    TextButton(
                        onClick = {
                            showDialog = false
                            openGallery()
                        },
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text("📁  Choisir depuis les fichiers")
                    }
                    HorizontalDivider()
                    TextButton(
                        onClick = {
                            showDialog = false
                            openCamera()
                        },
                        modifier = Modifier.fillMaxWidth()
                    ) {
                        Text("📷  Prendre une photo")
                    }
                }
            },
            confirmButton = {},
            dismissButton = {
                TextButton(onClick = { showDialog = false }) { Text("Annuler") }
            }
        )
    }

    // ── Snackbar erreur permission ───────────────────────────────────────────
    permissionError?.let { error ->
        LaunchedEffect(error) {
            permissionError = null
        }
        Snackbar(modifier = Modifier.padding(16.dp)) { Text(error) }
    }

    // ── UI ───────────────────────────────────────────────────────────────────
    Column(
        modifier = Modifier
            .fillMaxSize()
            .padding(16.dp)
    ) {
        Text("Photos", style = MaterialTheme.typography.headlineSmall)
        Spacer(modifier = Modifier.height(12.dp))

        Card(
            modifier = Modifier.fillMaxWidth(),
            shape = RoundedCornerShape(12.dp),
            elevation = CardDefaults.cardElevation(2.dp)
        ) {
            Column(modifier = Modifier.padding(12.dp)) {

                if (photos.isNotEmpty()) {
                    LazyVerticalGrid(
                        columns = GridCells.Fixed(3),
                        modifier = Modifier
                            .fillMaxWidth()
                            .heightIn(max = 400.dp),
                        horizontalArrangement = Arrangement.spacedBy(4.dp),
                        verticalArrangement = Arrangement.spacedBy(4.dp)
                    ) {
                        itemsIndexed(photos) { index, uri ->
                            PhotoItem(
                                uri = uri,
                                onDelete = { viewModel.removePhoto(index) }
                            )
                        }
                    }
                    Spacer(modifier = Modifier.height(8.dp))
                } else {
                    // Placeholder quand vide
                    Box(
                        modifier = Modifier
                            .fillMaxWidth()
                            .height(120.dp),
                        contentAlignment = Alignment.Center
                    ) {
                        Text("Aucune photo ajoutée", color = Color.Gray)
                    }
                }

                Text(
                    "${photos.size} / $MAX_PHOTOS photos",
                    style = MaterialTheme.typography.bodySmall,
                    color = Color.Gray
                )

                Spacer(modifier = Modifier.height(8.dp))

                OutlinedButton(
                    onClick = { showDialog = true },
                    enabled = photos.size < MAX_PHOTOS,
                    modifier = Modifier.fillMaxWidth()
                ) {
                    Text(if (photos.size < MAX_PHOTOS) "+ Importer des photos" else "Limite atteinte")
                }
            }
        }

        Spacer(modifier = Modifier.weight(1f))

        Button(
            onClick = { onSendPhotos(photos.toList()) },
            enabled = photos.isNotEmpty(),
            modifier = Modifier.fillMaxWidth()
        ) {
            Text("Envoyer les photos (${photos.size}) →")
        }
    }
}

@Composable
fun PhotoItem(uri: Uri, onDelete: () -> Unit) {
    Box(modifier = Modifier.size(100.dp)) {
        AsyncImage(
            model = uri,
            contentDescription = null,
            contentScale = ContentScale.Crop,
            modifier = Modifier
                .fillMaxSize()
                .clip(RoundedCornerShape(8.dp))
        )
        IconButton(
            onClick = onDelete,
            modifier = Modifier
                .size(22.dp)
                .align(Alignment.TopEnd)
                .background(Color.Black.copy(alpha = 0.6f), CircleShape)
        ) {
            Icon(
                Icons.Default.Close,
                contentDescription = "Supprimer",
                tint = Color.White,
                modifier = Modifier.size(14.dp)
            )
        }
    }
}