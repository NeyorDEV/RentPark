using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;
using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.QuickGrid;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class User
    {
        private GridItemsProviderRequest<UserModel> request;
        private List<UserModel> users;

        [Inject]
        private IUserService UserService { get; init; }

        [Inject]
        private NavigationManager NavigationManager { get; set; }

        protected override async Task OnInitializedAsync()
        {
            var request = new GridItemsProviderRequest<UserModel>
            {
                StartIndex = 0,
                Count = null
            };

            var result = await UserService.GetItems(request);
            users = result.Items?.ToList();
            StateHasChanged();
        }

        private void NavigateToAddUser()
        {
            NavigationManager.NavigateTo("/users/add");
        }

        private async Task DeleteUser(int id)
        {
            if (users == null)
                return;

            await UserService.Delete(id);
            users.RemoveAll(u => u.Id == id);
            StateHasChanged();
        }
    }
}
