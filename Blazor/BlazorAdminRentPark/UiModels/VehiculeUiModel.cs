namespace BlazorAdminRentPark.UiModels;

public class VehiculeUiModel
{
    public string NumSerie { get; set; } = string.Empty;
    public string Nom { get; set; } = string.Empty;
    public string Marque { get; set; } = string.Empty;
    public string Energie { get; set; } = string.Empty;
    public int NbPlaces { get; set; }
    public string Categorie { get; set; } = string.Empty;
    public string Transmission { get; set; } = string.Empty;
    public string Boite { get; set; } = string.Empty;
    public string Puissance { get; set; } = string.Empty;
    public string Etat { get; set; } = string.Empty;
    public float Prix { get; set; }
    public DateTime DateAchat { get; set; } = DateTime.Now;
    public DateTime DateDernierControleTech { get; set; } = DateTime.Now;
    public DateTime DateExpirationControleTech { get; set; } = DateTime.Now;
    public string Annee { get; set; } = string.Empty;
    public int IdAssureur { get; set; }
    public int IdFournisseur { get; set; }
    public string ImagePath { get; set; } = string.Empty;
    public string Couleur { get; set; } = string.Empty;
}