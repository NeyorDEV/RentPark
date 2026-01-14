using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;
using BlazorAdminRentPark.UiModels;
using Microsoft.AspNetCore.Components.QuickGrid;
using BlazorAdminRentPark.Models;

namespace BlazorAdminRentPark.Services
{
    public interface IUserService : IDataService<UserModel>
    {
        Task<GridItemsProviderResult<UserModel>> GetItems(GridItemsProviderRequest<UserModel> request);
        Task Delete(int id);
        Task Add(UserUiModel user);
        Task<LoginResponse?> LoginAsync(string username, string password);
    }
}
