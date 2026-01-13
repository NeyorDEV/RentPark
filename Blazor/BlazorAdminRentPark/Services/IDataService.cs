using BlazorAdminRentPark.Components.Pages;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services
{
    public interface IDataService<T>
    {
        /*Task Add(Client model);*/

        Task<GridItemsProviderResult<T>> GetItems(GridItemsProviderRequest<T> request);

    }
}
