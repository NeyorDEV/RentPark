using BlazorAdminRentPark.Models;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services
{
    public interface IVehiculeService : IDataService<VehiculeModel>
    {
        Task<GridItemsProviderResult<VehiculeModel>> GetItems(GridItemsProviderRequest<VehiculeModel> request);
    }
}
