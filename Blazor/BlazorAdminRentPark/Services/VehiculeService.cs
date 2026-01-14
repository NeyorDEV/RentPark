using BlazorAdminRentPark.Models;
using Microsoft.AspNetCore.Components.QuickGrid;
using System.IO;

namespace BlazorAdminRentPark.Services
{
    public class VehiculeService : IVehiculeService 
    {
        private readonly HttpClient _http;
        private const string ImagesBaseUrl = "http://localhost:9990";

        public VehiculeService(HttpClient http)
        {
            _http = http;
        }

        public async Task<GridItemsProviderResult<VehiculeModel>> GetItems(GridItemsProviderRequest<VehiculeModel> request)
        {
            var items = await _http.GetFromJsonAsync<List<VehiculeModel>>($"http://localhost:8880/voitures");

            if (items != null)
            {
                foreach (var item in items)
                {
                    if (!string.IsNullOrEmpty(item.ImagePath))
                    {
                        var path = item.ImagePath.StartsWith("/") ? item.ImagePath : "/" + item.ImagePath;
                        item.ImagePath = $"{ImagesBaseUrl}{path}";
                    }
                }
            }

            return new GridItemsProviderResult<VehiculeModel>
            {
                Items = items,
                TotalItemCount = items?.Count ?? 0
            };
        }

        public async Task Delete(string NumSerie)
        {
            await _http.DeleteAsync($"http://localhost:8880/voitures/{NumSerie}");
        }
    }
}
