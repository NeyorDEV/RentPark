using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.UiModels;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Services;

public interface IVehiculeService
{
    Task<GridItemsProviderResult<VehiculeModel>> GetItems(GridItemsProviderRequest<VehiculeModel> request);
    Task Add(VehiculeUiModel vehiculeUiModel);
    Task Delete(string numSerie);
}
