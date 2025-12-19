namespace BlazorAdminRentPark.Models
{
    public class ClientModel
    {
        public int IdClient { get; set; }
        public string Nom { get; set; }
        public string Prenom { get; set; }
        public DateTime DateNaiss { get; set; }
        public string Nationalite { get; set; }
        public string NumTel { get; set; }
        public string Email { get; set; }
        public string NumPermis { get; set; }
        public string Commentaire { get; set; }

    }
}
