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
    public partial class Cars : ComponentBase
    {
        [Inject]
        public IStringLocalizer<Cars> Localizer { get; set; } = default!;
        [Inject] protected IVehiculeService VehiculeService { get; set; }
  

        protected string SearchString = ""; 
        protected int PageSize = 8; 
        protected int CurrentPage = 1; 
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
        protected int PageCount => Math.Max(1, (int)Math.Ceiling((double)FilteredCars.Count() / PageSize));

        protected void OnPageChanged(int page)
        {
            CurrentPage = page; 
        }
    }
}