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
        
        public async Task<LoginResponse?> LoginAsync(string username, string password)
        {
            try
            {
                var response = await _http.PostAsJsonAsync("http://localhost:8880/login", new { username, password });

                if (response.IsSuccessStatusCode)
                {
                    return await response.Content.ReadFromJsonAsync<LoginResponse>();
                }
                
                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Erreur Login : {ex.Message}");
                return null;
            }
        }

        public async Task Update(int id, UserUiModel model)
        {
            // On crée un objet anonyme pour correspondre exactement aux champs attendus par ton API PHP
            var data = new
            {
                username = model.Username,
                role = model.Role,
                password = model.Password // Si null ou vide, l'API PHP l'ignorera
            };

            await _http.PutAsJsonAsync($"http://localhost:8880/users/{id}", data); //
        }
    }
}
