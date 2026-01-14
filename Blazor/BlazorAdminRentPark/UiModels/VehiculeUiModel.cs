namespace BlazorAdminRentPark.UiModels;

public class VehiculeUiModel
{
    public string NumSerie { get; set; } 
    public string Nom { get; set; } 
    public string Marque { get; set; } 
    public string Energie { get; set; } 
    public int NbPlaces { get; set; }
    public string Categorie { get; set; } 
    public string Transmission { get; set; } 
    public string Boite { get; set; } 
    public string Puissance { get; set; }
    public string Etat { get; set; } 
    public float Prix { get; set; }
    public DateOnly DateAchat { get; set; }
    public DateOnly DateDernierControleTech { get; set; }
    public DateOnly DateExpirationControleTech { get; set; } 
    public string Annee { get; set; }
    public int IdAssureur { get; set; }
    public int IdFournisseur { get; set; }
    public string ImagePath { get; set; } 
    public string Couleur { get; set; }
}