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
    public partial class Cars
    {
        [Inject]
        public IStringLocalizer<Cars> Localizer { get; set; } = default!;

        [Inject]
        protected IVehiculeService VehiculeService { get; set; } = default!;

        [Inject]
        protected IDialogService DialogService { get; set; } = default!;

        private int _pageSize = 8;
        private int _currentPage = 1;
        private string _searchString = "";

        protected List<VehiculeModel> _vehicules = new();

        protected string SearchString
        {
            get => _searchString;
            set
            {
                if (_searchString != value)
                {
                    _searchString = value;
                    _currentPage = 1;
                }
            }
        }

        protected int CurrentPage
        {
            get => _currentPage;
            set => _currentPage = value;
        }

        protected int PageSize
        {
            get => _pageSize;
            set
            {
                if (_pageSize != value)
                {
                    _pageSize = value;
                    _currentPage = 1;
                }
            }
        }

        protected IEnumerable<VehiculeModel> FilteredCars =>
            _vehicules.Where(c =>
                string.IsNullOrWhiteSpace(SearchString) ||
                (c.Nom?.Contains(SearchString, StringComparison.OrdinalIgnoreCase) == true) ||
                (c.Marque?.Contains(SearchString, StringComparison.OrdinalIgnoreCase) == true) ||
                (c.Categorie?.Contains(SearchString, StringComparison.OrdinalIgnoreCase) == true));

        protected IEnumerable<VehiculeModel> PagedCars =>
            FilteredCars
                .Skip((CurrentPage - 1) * PageSize)
                .Take(PageSize);

        protected int PageCount =>
            Math.Max(1, (int)Math.Ceiling(FilteredCars.Count() / (double)Math.Max(1, PageSize)));

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

        protected override void OnParametersSet()
        {
            if (CurrentPage > PageCount)
                CurrentPage = PageCount;
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
            // 1. Affichage de la boîte de dialogue de confirmation
            bool? result = await DialogService.ShowMessageBox(
                @Localizer["Confirmation"],
                @Localizer["DeleteConfirmation"],
                yesText: @Localizer["Delete"],
                cancelText: @Localizer["Cancel"]);

            // 2. Si l'utilisateur confirme la suppression
            if (result == true)
            {
                
                await VehiculeService.Delete(numSerie);

                await LoadVehicules();

                if (CurrentPage > PageCount)
                    CurrentPage = PageCount;

                StateHasChanged();
            }
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
                await VehiculeService.Update(updatedCar.NumSerie, updatedCar);
                await LoadVehicules();
                StateHasChanged();
            }
        }

        protected void OnPageChanged(int newPage)
        {
            CurrentPage = newPage;
        }

        private void OnPageSizeChanged(int newSize)
        {
            PageSize = newSize;
            CurrentPage = 1;
        }

        private void OnSearchChanged(string text)
        {
            SearchString = text;
            CurrentPage = 1;
        }
    }
}