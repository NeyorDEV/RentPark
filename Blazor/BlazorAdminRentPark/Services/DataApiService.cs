using BlazorAdminRentPark.Components.Pages;
using Microsoft.AspNetCore.Components.QuickGrid;
namespace BlazorAdminRentPark.Services
{
    public class DataApiService : IDataService
    {
        private readonly HttpClient _http;

        public DataApiService(HttpClient http)
        {
            _http = http;
        }

       
        /*
        /// <inheritdoc />
        public async Task Add(Client model)
        {
            // Get the item
            var item = PersonFactory.Create(model);

            // Save the data
            await _http.PostAsJsonAsync("https://localhost:8880/", item);
        }
        */

        /// <inheritdoc />
        public async ValueTask<GridItemsProviderResult<Client>> GetItems(GridItemsProviderRequest<Client> request)
        {
            var items = await _http.GetFromJsonAsync<List<Client>>($"https://localhost:8880/client");

            return new GridItemsProviderResult<Client>
            {
                Items = items
            };
        }
        /*

        /// <inheritdoc />
        public async Task<Client> GetById(int id)
        {
            return await _http.GetFromJsonAsync<Client>($"https://localhost:8880/{id}");
        }

        /// <inheritdoc />
        public async Task Update(int id, Client model)
        {
            // Get the item
            var item = PersonFactory.Create(model);

            await _http.PutAsJsonAsync($"https://localhost:8880/{id}", item);
        }

        /// <inheritdoc />
        public async Task Delete(int id)
        {
            await _http.DeleteAsync($"https://localhost:8880/{id}");
        }

        */
    }
}
