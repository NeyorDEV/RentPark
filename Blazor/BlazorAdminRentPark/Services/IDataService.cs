using BlazorAdminRentPark.Components.Pages;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services
{
    public interface IDataService
    {
        /*Task Add(Client model);*/

        ValueTask<GridItemsProviderResult<Client>> GetItems(GridItemsProviderRequest<Client> request);
    }
}
