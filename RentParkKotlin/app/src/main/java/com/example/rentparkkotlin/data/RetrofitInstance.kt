package com.example.rentparkkotlin.data

import android.content.Context
import okhttp3.OkHttpClient
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

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
    val apiVoiture: VoitureApiService = getRetrofit("rentpark-api-pod/api/").create(VoitureApiService::class.java)

    private val retrofit = Retrofit.Builder()
        .baseUrl("http://10.0.2.2:8880/") // adapte à ton serveur
        .addConverterFactory(GsonConverterFactory.create())
        .build()

    val api: ApiService = retrofit.create(ApiService::class.java)

}