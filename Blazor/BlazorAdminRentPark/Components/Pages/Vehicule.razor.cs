using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;
using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.QuickGrid;
using System.ComponentModel.DataAnnotations;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class Vehicule
    {
        private GridItemsProviderRequest<VehiculeModel> request;
        private List<VehiculeModel> vehicules;

        [Inject]
        private IVehiculeService VehiculeService { get; init; }

        protected override async Task OnInitializedAsync()
        {
            var request = new GridItemsProviderRequest<VehiculeModel>
            {
                StartIndex = 0,
                Count = null
            };

            var result = await VehiculeService.GetItems(request);
            vehicules = result.Items?.ToList();
            StateHasChanged();
        }

        private async Task DeleteVehicule(string numSerie) 
        {
            if (vehicules == null) 
                return;

                await VehiculeService.Delete(numSerie);
                vehicules.RemoveAll(v => v.NumSerie == numSerie);
                StateHasChanged();
        }
    }
}
