using BlazorAdminRentPark.Models;

namespace BlazorAdminRentPark.Models
{
    public class DashboardModel
    {
        public int total_users { get; set; }
        public int RevenusMensuels { get; set; }

        // Remplacement du string par l'objet complet
        public CarDetails VoiturePlusLouee { get; set; } = new();

        public List<RappelItemModel> Rappels { get; set; } = new();
        public List<PlanningItemModel> Planning { get; set; } = new();
    }
}