using Microsoft.AspNetCore.Components;
using Microsoft.Extensions.Localization;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class Home
    {
        [Inject]
        public IStringLocalizer<Home> Localizer { get; set; } = default!;
    }
}
