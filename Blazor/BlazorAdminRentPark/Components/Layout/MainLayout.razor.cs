using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.Authorization;
using Microsoft.Extensions.Localization;
using MudBlazor;

namespace BlazorAdminRentPark.Components.Layout
{
    public partial class MainLayout
    {

        [Inject]
        protected NavigationManager NavManager { get; set; } = default!;

        [Inject]
        public IStringLocalizer<MainLayout> Localizer { get; set; } = default!;

        private bool _drawerOpen = true;

        protected void DrawerToggle()
        {
            _drawerOpen = !_drawerOpen;
        }

        protected void Logout()
        {
            NavManager.NavigateTo("/Account/Logout", forceLoad: true);
        }
    }
}

