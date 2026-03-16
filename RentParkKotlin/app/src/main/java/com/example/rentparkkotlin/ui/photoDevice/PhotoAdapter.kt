import android.net.Uri
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageButton
import android.widget.ImageView
import androidx.recyclerview.widget.RecyclerView
import coil.load // ou Glide si vous préférez
import com.example.rentparkkotlin.R

class PhotoAdapter(
    private val photos: MutableList<Uri>,
    private val onDelete: (Int) -> Unit
) : RecyclerView.Adapter<PhotoAdapter.PhotoViewHolder>() {

    inner class PhotoViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val ivPhoto: ImageView = view.findViewById(R.id.ivPhoto)
        val btnDelete: ImageButton = view.findViewById(R.id.btnDelete)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PhotoViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_photo, parent, false)
        return PhotoViewHolder(view)
    }

    override fun onBindViewHolder(holder: PhotoViewHolder, position: Int) {
        holder.ivPhoto.load(photos[position]) {
            crossfade(true)
        }
        holder.btnDelete.setOnClickListener {
            onDelete(holder.adapterPosition)
        }
    }

    override fun getItemCount() = photos.size

    fun addPhoto(uri: Uri) {
        photos.add(uri)
        notifyItemInserted(photos.size - 1)
    }

    fun removePhoto(index: Int) {
        photos.removeAt(index)
        notifyItemRemoved(index)
    }

    fun getPhotos(): List<Uri> = photos.toList()
}