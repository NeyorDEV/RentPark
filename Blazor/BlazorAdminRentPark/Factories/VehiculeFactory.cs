using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.UiModels;

namespace BlazorAdminRentPark.Factories;

public static class VehiculeFactory
{
    public static VehiculeUiModel ToUiModel(VehiculeModel item)
    {
        return new VehiculeUiModel
        {
            NumSerie = item.NumSerie,
            Nom = item.Nom,
            Marque = item.Marque,
            Energie = item.Energie,
            NbPlaces = item.NbPlaces,
            Categorie = item.Categorie,
            Transmission = item.Transmission,
            Boite = item.Boite,
            Puissance = item.Puissance,
            Etat = item.Etat,
            Prix = item.Prix,
            DateAchat = item.DateAchat,
            DateDernierControleTech = item.DateDernierControleTech,
            DateExpirationControleTech = item.DateExpirationControleTech,
            Annee = item.Annee,
            IdAssureur = item.IdAssureur,
            IdFournisseur = item.IdFournisseur,
            ImagePath = item.ImagePath,
            Couleur = item.Couleur
        };
    }

    public static VehiculeModel Create(VehiculeUiModel model)
    {
        return new VehiculeModel
        {
            NumSerie = model.NumSerie,
            Nom = model.Nom,
            Marque = model.Marque,
            Energie = model.Energie,
            NbPlaces = model.NbPlaces,
            Categorie = model.Categorie,
            Transmission = model.Transmission,
            Boite = model.Boite,
            Puissance = model.Puissance,
            Etat = model.Etat,
            Prix = model.Prix,
            DateAchat = model.DateAchat,
            DateDernierControleTech = model.DateDernierControleTech,
            DateExpirationControleTech = model.DateExpirationControleTech,
            Annee = model.Annee,
            IdAssureur = model.IdAssureur,
            IdFournisseur = model.IdFournisseur,
            ImagePath = model.ImagePath,
            Couleur = model.Couleur
        };
    }
}