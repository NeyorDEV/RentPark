using Microsoft.AspNetCore.Components;
using Microsoft.Extensions.Localization;

namespace BlazorAdminRentPark.Components.Layout
{
    public partial class NavMenu
    {
        [Inject]
        public IStringLocalizer<NavMenu> Localizer { get; set;} = default!;
    }
}