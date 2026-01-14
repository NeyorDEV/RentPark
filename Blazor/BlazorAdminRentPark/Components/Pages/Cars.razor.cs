using BlazorAdminRentPark.Components.Layout;
using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;
using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.QuickGrid;
using Microsoft.AspNetCore.Components.Web;
using Microsoft.Extensions.Localization;
using MudBlazor;

namespace BlazorAdminRentPark.Components.Pages
{

    // L'héritage : ComponentBase est indispensable pour OnInitializedAsync et StateHasChanged
    public partial class Cars 
    {
        [Inject]
        public IStringLocalizer<Cars> Localizer { get; set; } = default!;
        [Inject] protected IVehiculeService VehiculeService { get; set; }
  
        [Inject] protected IDialogService DialogService { get; set; } = default!;

        protected string SearchString = "";
        protected int PageSize = 8;
        protected int CurrentPage = 1;
        protected int PageCount => (int)Math.Ceiling((double)FilteredCars.Count() / PageSize);
        protected List<VehiculeModel> _vehicules = new();

        protected override async Task OnInitializedAsync()
        {
            await LoadVehicules();
        }

        protected async Task LoadVehicules()
        {
            var request = new GridItemsProviderRequest<VehiculeModel> { StartIndex = 0, Count = null };
            var result = await VehiculeService.GetItems(request);
            _vehicules = result.Items?.ToList() ?? new List<VehiculeModel>();
        }

        protected async Task OpenAddCarDialog()
        {
            var options = new DialogOptions
            {
                CloseOnEscapeKey = true,
                MaxWidth = MaxWidth.Medium,
                FullWidth = true
            };

            var dialog = await DialogService.ShowAsync<AddCarDialog>("", options);
            var result = await dialog.Result;

            if (!result.Canceled)
            {
                await LoadVehicules();
                StateHasChanged();
            }
        }

        protected async Task DeleteCar(string numSerie)
        {
            await VehiculeService.Delete(numSerie);
            await LoadVehicules();
            StateHasChanged();
        }
        protected async Task OpenEditCarDialog(VehiculeModel car)
        {
            var carCopy = new VehiculeModel
            {
                NumSerie = car.NumSerie,
                Nom = car.Nom,
                Marque = car.Marque,
                Energie = car.Energie,
                NbPlaces = car.NbPlaces,
                Categorie = car.Categorie,
                Transmission = car.Transmission,
                Boite = car.Boite,
                Puissance = car.Puissance,
                Etat = car.Etat,
                Prix = car.Prix,
                DateAchat = car.DateAchat,
                DateDernierControleTech = car.DateDernierControleTech,
                DateExpirationControleTech = car.DateExpirationControleTech
            };

            var parameters = new DialogParameters { { "newVehicule", carCopy } };
            var options = new DialogOptions { CloseOnEscapeKey = true, MaxWidth = MaxWidth.Medium, FullWidth = true };

            var dialog = await DialogService.ShowAsync<AddCarDialog>("Modifier le véhicule", parameters, options);
            var result = await dialog.Result;

            if (!result.Canceled && result.Data is VehiculeModel updatedCar)
            {
                // Appel au service avec l'objet modifié
                await VehiculeService.Update(updatedCar.NumSerie, updatedCar);
                await LoadVehicules();
                StateHasChanged();
            }
        }

        protected IEnumerable<VehiculeModel> FilteredCars => _vehicules
            .Where(c => string.IsNullOrWhiteSpace(SearchString) ||
                        (c.Nom?.Contains(SearchString, StringComparison.OrdinalIgnoreCase) == true) ||
                        (c.Marque?.Contains(SearchString, StringComparison.OrdinalIgnoreCase) == true) ||
                        (c.Categorie?.Contains(SearchString, StringComparison.OrdinalIgnoreCase) == true));

        protected IEnumerable<VehiculeModel> PagedCars => FilteredCars
            .Skip((CurrentPage - 1) * PageSize)
            .Take(PageSize);

    }
}