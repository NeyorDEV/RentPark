package com.example.rentparkkotlin.data

import android.content.Context
import com.example.rentparkkotlin.data.api.AuthApiService
import com.example.rentparkkotlin.data.api.ClientApiService
import com.example.rentparkkotlin.data.api.ContratApiService
import com.example.rentparkkotlin.data.api.RappelApiService
import com.example.rentparkkotlin.data.api.StatsApiService
import com.example.rentparkkotlin.data.api.UserApiService
import com.example.rentparkkotlin.data.api.VoitureApiService
import okhttp3.OkHttpClient
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import kotlin.jvm.java

object RetrofitInstance {
    private const val BASE_URL = "https://codefirst.iut.uca.fr/kubernetes/iut-inf63-projets-etudiants-rentpark/"
    private var authPrefs: AuthPrefs? = null

    fun init(context: Context) {
        authPrefs = AuthPrefs(context.applicationContext)
    }

    private val client = OkHttpClient.Builder()
        .addInterceptor { chain ->
            val requestBuilder = chain.request().newBuilder()
            val token = authPrefs?.getToken()
            if (token != null) {
                requestBuilder.addHeader("Authorization", "Bearer $token")
            }

            chain.proceed(requestBuilder.build())
        }
        .build()

    private fun getRetrofit(podName: String): Retrofit {
        return Retrofit.Builder()
            .baseUrl("$BASE_URL$podName")
            .client(client)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }

    val apiAuth: AuthApiService = getRetrofit("rentpark-auth-pod/").create(AuthApiService::class.java)
    val apiVoiture: VoitureApiService = getRetrofit("rentpark-api-pod/api/").create(
        VoitureApiService::class.java)
    val apiUser: UserApiService = getRetrofit("rentpark-utilisateurs-pod/api/").create(UserApiService::class.java)
    val apiClient: ClientApiService = getRetrofit("rentpark-clients-pod/api/").create(ClientApiService::class.java)
    val apiRappel: RappelApiService = getRetrofit("rentpark-rappel-pod/api/").create(RappelApiService::class.java)
    val apiContrat: ContratApiService = getRetrofit("rentpark-contrat-pod/api/").create(ContratApiService::class.java)
    val apiStats: StatsApiService = getRetrofit("rentpark-statts-pod/api/").create(StatsApiService::class.java)
}