namespace BlazorAdminRentPark.Models
{
    public class DashboardModel
    {
        // Statistiques principales (Chiffres)
        public int NombreUtilisateurs { get; set; }
        public int RevenusMensuels { get; set; }

        // Voiture la plus louée (Chemin de l'image ou Nom)
        public string ImageVoiturePlusLouee { get; set; }

        // Collections pour les listes
        // Utilisation de classes simples pour structurer les rappels et le planning
        public List<RappelItemModel> Rappels { get; set; } = new List<RappelItemModel>();
        public List<PlanningItemModel> Planning { get; set; } = new List<PlanningItemModel>();
    }
}