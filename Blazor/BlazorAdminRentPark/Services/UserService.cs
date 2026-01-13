using BlazorAdminRentPark.Factories;
using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.UiModels;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services
{
    public class UserService : IUserService
    { 
        private readonly HttpClient _http; 
        public UserService(HttpClient http) 
        {
            _http = http;
        }
        public async Task<GridItemsProviderResult<UserModel>> GetItems(GridItemsProviderRequest<UserModel> request)
        {
            var items = await _http.GetFromJsonAsync<List<UserModel>>($"http://localhost:8880/users");

            return new GridItemsProviderResult<UserModel>
            {
                Items = items,
                TotalItemCount = items.Count
            };
        }

        public async Task Delete(int id)
        {
            await _http.DeleteAsync($"http://localhost:8880/users/{id}");
        }

        public async Task Add(UserUiModel model)
        {
            var item = UserFactory.Create(model);   

            await _http.PostAsJsonAsync("http://localhost:8880/add/users", item);       
        }
    }
}
