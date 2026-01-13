using BlazorAdminRentPark.Factories;
using BlazorAdminRentPark.Services;
using BlazorAdminRentPark.UiModels;
using Microsoft.AspNetCore.Components;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class AddUser
    {
        private UserUiModel userModel = new();

        [Inject]
        private IUserService UserService { get; set; }

        [Inject]
        private NavigationManager NavigationManager { get; set; }

        private async Task HandleSubmit()
        {
            await UserService.Add(userModel);
            NavigationManager.NavigateTo("/users");
        }

        private void Cancel()
        {
            NavigationManager.NavigateTo("/users");
        }
    }
}