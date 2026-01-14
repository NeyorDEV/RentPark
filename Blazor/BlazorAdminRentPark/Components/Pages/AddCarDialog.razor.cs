using BlazorAdminRentPark.Services;
using BlazorAdminRentPark.UiModels;
using Microsoft.AspNetCore.Components;
using MudBlazor;

namespace BlazorAdminRentPark.Components.Pages;

public partial class AddCarDialog
{
    [Inject]
    private IVehiculeService VehiculeService { get; set; } = default!;

    [CascadingParameter]
    private IMudDialogInstance MudDialog { get; set; } = default!;

    private MudForm _form = default!;
    private bool _isValid;
    private VehiculeUiModel _vehiculeModel = new();

    private async Task HandleSubmit()
    {
        await _form.Validate();

        if (_isValid)
        {
            await VehiculeService.Add(_vehiculeModel);
            MudDialog.Close(DialogResult.Ok(true));
        }
    }

    private void Cancel()
    {
        MudDialog.Cancel();
    }
}