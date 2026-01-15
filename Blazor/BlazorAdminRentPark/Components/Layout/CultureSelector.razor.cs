using System.Globalization;
using Microsoft.AspNetCore.Components;

namespace BlazorAdminRentPark.Components.Layout
{
    public partial class CultureSelector
    {
  

        protected readonly CultureInfo[] SupportedCultures = new[]
        {
            new CultureInfo("fr-FR"),
            new CultureInfo("en-US")
        };

        private string _selectedCultureName = CultureInfo.CurrentCulture.Name;

        protected string SelectedCultureName
        {
            get => _selectedCultureName;
            set
            {
                if (string.Equals(_selectedCultureName, value, StringComparison.Ordinal))
                    return;

                _selectedCultureName = value;
                OnCultureChanged(value);
            }
        }

        private void OnCultureChanged(string cultureName)
        {
            if (string.IsNullOrWhiteSpace(cultureName))
                return;

            var culture = cultureName.ToLower(CultureInfo.InvariantCulture);

            var uri = new Uri(NavigationManager.Uri)
                .GetComponents(UriComponents.PathAndQuery, UriFormat.Unescaped);

            var query = $"?culture={Uri.EscapeDataString(culture)}&redirectUri={Uri.EscapeDataString(uri)}";

            
            NavigationManager.NavigateTo("/Culture/SetCulture" + query, forceLoad: true);
        }
    }
}
