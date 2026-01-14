using BlazorAdminRentPark.Services;
using BlazorAdminRentPark.UiModels;
using Microsoft.AspNetCore.Components;
using MudBlazor;

namespace BlazorAdminRentPark.Components.Pages;

public partial class AddUserDialog
{
    [Inject]
    private IUserService UserService { get; set; } = default!;

    [CascadingParameter]
    private IMudDialogInstance MudDialog { get; set; } = default!;

    private MudForm _form = default!;
    private bool _isValid;
    private UserUiModel _userModel = new();

    private async Task HandleSubmit()
    {
        await _form.Validate();
        
        if (_isValid)
        {
            await UserService.Add(_userModel);
            MudDialog.Close(DialogResult.Ok(true));
        }
    }
}