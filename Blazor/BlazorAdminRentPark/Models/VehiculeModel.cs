namespace BlazorAdminRentPark.Models
{
    public class VehiculeModel
    {
        public string NumSerie { get; set; }
        public string Energie { get; set; }
        public int NbPlaces { get; set; }
        public string Categorie { get; set; }
        public string Transmission { get; set; }
        public string Boite { get; set; }
        public string Etat { get; set; }
        public string Puissance { get; set; }
        public DateTime DateAchat { get; set; }
        public DateTime DateExpirationControleTech { get; set; }
        public DateTime DateDernierControleTech { get; set; }
        public string Marque { get; set; }
        public string Nom { get; set; }
        public string ImagePath { get; set; }
    }
}
