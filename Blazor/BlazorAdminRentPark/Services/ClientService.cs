using BlazorAdminRentPark.Components.Pages;
using BlazorAdminRentPark.Models;
using Microsoft.AspNetCore.Components.QuickGrid;
using static System.Net.WebRequestMethods;

namespace BlazorAdminRentPark.Services
{
    public class ClientService : IClientService 
    {
        private readonly HttpClient _http; 

        public ClientService(HttpClient http)
        {
            _http = http;
        }

        public async Task<GridItemsProviderResult<ClientModel>> GetItems(GridItemsProviderRequest<ClientModel> request)
        {
            var items = await _http.GetFromJsonAsync<List<ClientModel>>($"{BlazorAdminRentPark.ApiConfig.Api}/clients");

            return new GridItemsProviderResult<ClientModel>
            {
                Items = items,
                TotalItemCount = items.Count
            };
        }
    }
}
