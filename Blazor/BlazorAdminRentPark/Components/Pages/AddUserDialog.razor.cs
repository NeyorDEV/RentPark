using BlazorAdminRentPark.Services;
using BlazorAdminRentPark.UiModels;
using BlazorAdminRentPark.Models;
using Microsoft.AspNetCore.Components;
using Microsoft.Extensions.Localization;
using MudBlazor;

namespace BlazorAdminRentPark.Components.Pages;

public partial class AddUserDialog
{
    [Inject]
    public IStringLocalizer<AddUserDialog> Localizer { get; set; } = default!;

    [Inject]
    private IUserService UserService { get; set; } = default!;
    [Parameter] public UserModel? UserToEdit { get; set; }

    private bool IsEditMode => UserToEdit != null;
    [CascadingParameter]
    private IMudDialogInstance MudDialog { get; set; } = default!;

    private MudForm _form = default!;
    private bool _isValid;
    private UserUiModel _userModel = new();

    protected override void OnInitialized()
    {
        if (IsEditMode)
        {
            // En mode édition, on pré-remplit le formulaire
            // Note : On ne remplit pas le mot de passe car il est optionnel en modification
            _userModel = new UserUiModel
            {
                Username = UserToEdit.Username,
                Role = UserToEdit.Role
            };
        }
    }

    private async Task HandleSubmit()
    {
        await _form.Validate();

        if (_isValid)
        {
            if (IsEditMode)
            {
                // Appel à la méthode Update avec l'ID d'origine
                await UserService.Update(UserToEdit!.Id, _userModel);
            }
            else
            {
                // Comportement standard d'ajout
                await UserService.Add(_userModel);
            }

            MudDialog.Close(DialogResult.Ok(true));
        }
    }
}