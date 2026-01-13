using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;

public class DashboardService : IDashboardService
{
    private readonly HttpClient _http;
    private const string BaseUrl = "http://localhost:8880/stats";

    public DashboardService(HttpClient http) => _http = http;

    public async Task<int> GetTotalUsersAsync()
    {
        // Utilisation d'un DTO pour extraire la valeur du JSON
        var response = await _http.GetFromJsonAsync<CountResponse>($"{BaseUrl}/total-users");
        return response?.Count ?? 0;
    }

    public async Task<int> GetMonthlyRevenuesAsync()
    {
        var response = await _http.GetFromJsonAsync<RevenueResponse>($"{BaseUrl}/revenus-mensuels");
        return response?.Amount ?? 0;
    }

    public async Task<string> GetMostRentedCarImageAsync()
    {
        return await _http.GetStringAsync($"{BaseUrl}/voiture-plus-louee");
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
public class CountResponse { public int Count { get; set; } }
public class RevenueResponse { public int Amount { get; set; } }