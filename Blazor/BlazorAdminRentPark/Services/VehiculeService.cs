using BlazorAdminRentPark.Models;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services
{
    public class VehiculeService : IVehiculeService 
    {
        private readonly HttpClient _http;

        public VehiculeService(HttpClient http)
        {
            _http = http;
        }

        public async Task<GridItemsProviderResult<VehiculeModel>> GetItems(GridItemsProviderRequest<VehiculeModel> request)
        {
            var items = await _http.GetFromJsonAsync<List<VehiculeModel>>($"http://localhost:8880/voitures");

            return new GridItemsProviderResult<VehiculeModel>
            {
                Items = items,
                TotalItemCount = items.Count
            };
        }

        public async Task Delete(string NumSerie)
        {
            await _http.DeleteAsync($"http://localhost:8880/delete/voitures/{NumSerie}");
        }

        public async Task Update(string numSerie, VehiculeModel vehicule)
        {
            await _http.PutAsJsonAsync($"http://localhost:8880/voitures/{numSerie}", vehicule); // [cite: 7]
        }

    }
}
