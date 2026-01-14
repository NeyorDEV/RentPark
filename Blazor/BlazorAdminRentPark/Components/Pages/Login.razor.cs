using Microsoft.AspNetCore.Components;
using Microsoft.Extensions.Localization;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class Login
    {
        [Inject]
        public IStringLocalizer<Login> Localizer { get; set; } = default!;
    }
}
