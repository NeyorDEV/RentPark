using BlazorAdminRentPark.Components.Pages;
using BlazorAdminRentPark.Models;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services
{
    public interface IClientService : IDataService<ClientModel>
    {
        Task<GridItemsProviderResult<ClientModel>> GetItems(GridItemsProviderRequest<ClientModel> request);
    }
}
