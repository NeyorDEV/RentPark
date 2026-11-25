using System.Net.Http.Json;
using BlazorAdminRentPark.Factories;
using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.UiModels;
using Microsoft.AspNetCore.Components.QuickGrid;
using System.IO;

namespace BlazorAdminRentPark.Services
{
    public class VehiculeService : IVehiculeService
    {
        private readonly HttpClient _httpClient;
        private static readonly string ImagesBaseUrl = BlazorAdminRentPark.ApiConfig.Images;

        public VehiculeService(HttpClient httpClient)
        {
            _httpClient = httpClient;
        }

        public async Task<GridItemsProviderResult<VehiculeModel>> GetItems(GridItemsProviderRequest<VehiculeModel> request)
        {
            var items = await _httpClient.GetFromJsonAsync<List<VehiculeModel>>($"{BlazorAdminRentPark.ApiConfig.Api}/voitures");

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

        public async Task Add(VehiculeUiModel vehiculeUiModel)
        {
            var vehicule = VehiculeFactory.Create(vehiculeUiModel);
            await _httpClient.PostAsJsonAsync($"{BlazorAdminRentPark.ApiConfig.Api}/add/vehicule", vehicule);
        }

        public async Task Delete(string numSerie)
        {
            await _httpClient.DeleteAsync($"{BlazorAdminRentPark.ApiConfig.Api}/delete/voitures/{numSerie}");
        }

        public async Task Update(string numSerie, VehiculeModel vehicule)
        {
            await _httpClient.PutAsJsonAsync($"{BlazorAdminRentPark.ApiConfig.Api}/voitures/{numSerie}", vehicule); //
        }
        
    }
}
