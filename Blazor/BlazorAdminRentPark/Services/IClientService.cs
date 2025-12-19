using BlazorAdminRentPark.Components.Pages;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services
{
    public interface IClientService : IDataService<Client>
    {
        Task<GridItemsProviderResult<Client>> GetItems(GridItemsProviderRequest<Client> request);
    }
}
