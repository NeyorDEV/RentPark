using BlazorAdminRentPark.Components.Pages;
using Microsoft.AspNetCore.Components.QuickGrid;
using static System.Net.WebRequestMethods;

namespace BlazorAdminRentPark.Services
{
    public class ClientService 
    {
        private readonly HttpClient _http;

        public ClientService(HttpClient http)
        {
            _http = http;
        }

        public async Task<GridItemsProviderResult<Client>> GetItems(GridItemsProviderRequest<Client> request)
        {


            var items = await _http.GetFromJsonAsync<List<Client>>($"https://localhost:8880/client");

            return new GridItemsProviderResult<Client>
            {
                Items = items
            };
        }
    }
}
