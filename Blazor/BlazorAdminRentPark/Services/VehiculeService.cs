using System.Net.Http.Json;
using BlazorAdminRentPark.Factories;
using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.UiModels;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services
{
    public class VehiculeService : IVehiculeService
    {
        private readonly HttpClient _httpClient;

        public VehiculeService(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        public async Task<GridItemsProviderResult<VehiculeModel>> GetItems(GridItemsProviderRequest<VehiculeModel> request)
        {
            var response = await _httpClient.GetFromJsonAsync<List<VehiculeModel>>("http://localhost:8880/voitures");
            var items = response ?? new List<VehiculeModel>();
            
            return new GridItemsProviderResult<VehiculeModel>
            {
                Items = items,
                TotalItemCount = items.Count
            };
        }

        public async Task Add(VehiculeUiModel vehiculeUiModel)
        {
            var vehicule = VehiculeFactory.Create(vehiculeUiModel);
            await _httpClient.PostAsJsonAsync("http://localhost:8880/add/vehicule", vehicule);
        }

        public async Task Delete(string numSerie)
        {
            await _httpClient.DeleteAsync($"http://localhost:8880/delete/voitures/{numSerie}");
        }

        public async Task Update(string numSerie, VehiculeModel vehicule)
        {
            await _http.PutAsJsonAsync($"http://localhost:8880/voitures/{numSerie}", vehicule); // [cite: 7]
        }

    }
}
