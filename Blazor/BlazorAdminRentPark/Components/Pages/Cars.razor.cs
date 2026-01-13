using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.Web;
using Microsoft.AspNetCore.Components.QuickGrid;
using MudBlazor;
using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class Cars
    {
        [Inject] protected IVehiculeService VehiculeService { get; set; } = default!;
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