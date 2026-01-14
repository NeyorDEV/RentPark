using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;
using System.Text.Json.Serialization;

public class DashboardService : IDashboardService
{
    private readonly HttpClient _http;
    private const string BaseUrl = "http://localhost:8880/stats";
    private const string ImagesBaseUrl = "http://localhost:9990";

    public DashboardService(HttpClient http) => _http = http;

    public async Task<int> GetTotalUsersAsync()
    {
        var response = await _http.GetFromJsonAsync<CountResponse>($"{BaseUrl}/total-users");
        return response?.total_users ?? 0;
    }

    public async Task<int> GetMonthlyRevenuesAsync()
    {
        var response = await _http.GetFromJsonAsync<RevenueResponse>($"{BaseUrl}/revenus-mensuel");
        return response?.monthlyIncome ?? 0;
    }

    public async Task<CarDetails> GetMostRentedCarDetailsAsync()
    {
        var response = await _http.GetFromJsonAsync<MostRentedCarResponse>($"{BaseUrl}/voiture-plus-louee");
        var details = response?.Voiture;

        if (details != null && !string.IsNullOrEmpty(details.ImagePath))
        {
            var path = details.ImagePath.StartsWith("/") ? details.ImagePath : "/" + details.ImagePath;
            details.ImagePath = $"{ImagesBaseUrl}{path}";
        }

        return details;
    }

    public async Task<List<RappelItemModel>> GetRappelsAsync()
    {
        // Correction de l'erreur NotImplemented (image 7347dc)
        return await _http.GetFromJsonAsync<List<RappelItemModel>>($"{BaseUrl}/rappels-mensuel") //Pas dans l'api
               ?? new List<RappelItemModel>();
    }

    public async Task<List<PlanningItemModel>> GetPlanningAsync()
    {
        return await _http.GetFromJsonAsync<List<PlanningItemModel>>($"{BaseUrl}/planning-mensuel")
               ?? new List<PlanningItemModel>();
    }
}

// Modèles pour correspondre au format de votre API
public class CountResponse
{
    public int total_users { get; set; }
}
public class RevenueResponse 
{ 
    public int monthlyIncome { get; set; } 
}

public class MostRentedCarResponse
{
    [JsonPropertyName("voiture_plus_louee")]
    public CarDetails Voiture { get; set; }
}

public class CarDetails
{
    public string Marque { get; set; }
    public string Modele { get; set; }
    public string ImagePath { get; set; }
    public int nb_locations { get; set; }
}