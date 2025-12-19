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
            var items = await _http.GetFromJsonAsync<List<VehiculeModel>>($"http://localhost:8880/vehicules");

            return new GridItemsProviderResult<VehiculeModel>
            {
                Items = items,
                TotalItemCount = items.Count
            };
        }

        public async Task Delete(string NumSerie)
        {
            await _http.DeleteAsync($"http://localhost:8880/voitures/{NumSerie}");
        }
    }
}
