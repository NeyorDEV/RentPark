using Microsoft.AspNetCore.Components;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class Home : ComponentBase
    {
        // Données pour les graphiques
        protected int UserPercentage { get; set; } = 75;
        protected int MostRentedPercentage { get; set; } = 50;
        protected int MonthlyRevenuePercentage { get; set; } = 15;

        // Listes de données
        protected List<string> ListeRappels { get; set; } = new()
        {
            "Contrôle technique – BMW Série 4",
            "Vidange – Clio 3",
            "Assurance – Tesla Model 3",
            "Contrôle Pollution – Peugeot 208",
            "Révision – Audi A3"
        };

        protected List<string> ListePlanning { get; set; } = new()
        {
            "09:00 – Location Peugeot 208",
            "10:30 – Retour Tesla Model 3",
            "13:00 – Location BMW Série 4",
            "16:00 – Nettoyage Renault Twingo",
            "18:00 – Clôture caisse"
        };
    }
}