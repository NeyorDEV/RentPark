using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;
using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.QuickGrid;
using System.ComponentModel.DataAnnotations;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class Client
    {
        private GridItemsProviderRequest<ClientModel> request;
        private List<ClientModel> clients;

        [Inject]
        private IClientService ClientService { get; init; }

        protected override async Task OnInitializedAsync()
        {
            var request = new GridItemsProviderRequest<ClientModel>
            {
                StartIndex = 0,
                Count = null
            };

            var result = await ClientService.GetItems(request);
            clients = result.Items?.ToList();
            StateHasChanged();
        }
    }
}
