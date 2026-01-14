namespace BlazorAdminRentPark.Models
{
    public class RappelItemModel
    {
        public string Marque { get; set; }
        public string Modele { get; set; }
        public string DateExpirationControleTech { get; set; }

        // Propriété calculée pour simplifier l'affichage
        public string NomVehicule => $"{Marque} {Modele}";
    }
}
