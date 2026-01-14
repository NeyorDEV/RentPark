using BlazorAdminRentPark.Services;
using BlazorAdminRentPark.UiModels;
using BlazorAdminRentPark.Models; // Assurez-vous d'avoir ce using pour VehiculeModel
using Microsoft.AspNetCore.Components;
using MudBlazor;

namespace BlazorAdminRentPark.Components.Pages;

public partial class AddCarDialog
{
    [Inject]
    private IVehiculeService VehiculeService { get; set; } = default!;

    [CascadingParameter]
    private IMudDialogInstance MudDialog { get; set; } = default!;

    // --- CORRECTION 1 : Récupérer le véhicule envoyé par Cars.razor ---
    // Le nom "newVehicule" doit correspondre EXACTEMENT à la clé utilisée dans Cars.razor
    [Parameter]
    public VehiculeModel? newVehicule { get; set; }

    private MudForm _form = default!;
    private bool _isValid;
    private VehiculeUiModel _vehiculeModel = new();

    // --- CORRECTION 2 : Initialiser le formulaire si on est en modification ---
    protected override void OnInitialized()
    {
        if (newVehicule != null)
        {
            // On copie les données du véhicule existant vers le modèle de l'interface (UiModel)
            _vehiculeModel = new VehiculeUiModel
            {
                NumSerie = newVehicule.NumSerie,
                Nom = newVehicule.Nom,
                Marque = newVehicule.Marque,
                Annee = newVehicule.Annee,
                Couleur = newVehicule.Couleur,
                Categorie = newVehicule.Categorie,
                NbPlaces = newVehicule.NbPlaces,
                Energie = newVehicule.Energie,
                Transmission = newVehicule.Transmission,
                Boite = newVehicule.Boite,
                Puissance = newVehicule.Puissance,
                Etat = newVehicule.Etat,
                Prix = newVehicule.Prix,
                IdAssureur = newVehicule.IdAssureur,
                IdFournisseur = newVehicule.IdFournisseur,
                ImagePath = newVehicule.ImagePath,
                DateAchat = newVehicule.DateAchat,
                DateDernierControleTech = newVehicule.DateDernierControleTech,
                DateExpirationControleTech = newVehicule.DateExpirationControleTech
            };
        }
    }

    private async Task HandleSubmit()
    {
        await _form.Validate();

        if (_isValid)
        {
            // --- CORRECTION 3 : Distinguer Ajout et Modification ---
            if (newVehicule != null)
            {
                // MODE MODIFICATION

                // On recrée un VehiculeModel à partir du UiModel pour l'envoi au service
                var vehiculeAJour = new VehiculeModel
                {
                    NumSerie = _vehiculeModel.NumSerie,
                    Nom = _vehiculeModel.Nom,
                    Marque = _vehiculeModel.Marque,
                    Annee = _vehiculeModel.Annee,
                    Couleur = _vehiculeModel.Couleur,
                    Categorie = _vehiculeModel.Categorie,
                    NbPlaces = _vehiculeModel.NbPlaces,
                    Energie = _vehiculeModel.Energie,
                    Transmission = _vehiculeModel.Transmission,
                    Boite = _vehiculeModel.Boite,
                    Puissance = _vehiculeModel.Puissance,
                    Etat = _vehiculeModel.Etat,
                    Prix = _vehiculeModel.Prix,
                    IdAssureur = _vehiculeModel.IdAssureur,
                    IdFournisseur = _vehiculeModel.IdFournisseur,
                    ImagePath = _vehiculeModel.ImagePath,
                    DateAchat = _vehiculeModel.DateAchat,
                    DateDernierControleTech = _vehiculeModel.DateDernierControleTech,
                    DateExpirationControleTech = _vehiculeModel.DateExpirationControleTech
                };

                // Appel de la méthode Update
                await VehiculeService.Update(_vehiculeModel.NumSerie, vehiculeAJour);
            }
            else
            {
                // MODE AJOUT
                await VehiculeService.Add(_vehiculeModel);
            }

            MudDialog.Close(DialogResult.Ok(true));
        }
    }

    private void Cancel()
    {
        MudDialog.Cancel();
    }
}