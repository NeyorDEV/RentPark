using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;
using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.QuickGrid;
using Microsoft.Extensions.Localization;
using MudBlazor;
using Microsoft.Extensions.Logging;

namespace BlazorAdminRentPark.Components.Pages;

public partial class Account
{
    [Inject]
    public IStringLocalizer<Account> Localizer { get; set; } = default!;

    [Inject]
    private IUserService UserService { get; set; } = default!;

    [Inject]
    private IDialogService DialogService { get; set; } = default!;

    [Inject]
    private ILogger<Account> Logger { get; set; } = default!;

    private string _searchString = "";
    private int _currentPage = 1;
    private int _pageSize = 8;
    private List<UserModel> _users = new List<UserModel>();

    protected string SearchString
    {
        get => _searchString;
        set
        {
            if (_searchString != value)
            {
                _searchString = value;
                _currentPage = 1;
            }
        }
    }

    protected int CurrentPage
    {
        get => _currentPage;
        set => _currentPage = value;
    }

    protected int PageSize
    {
        get => _pageSize;
        set
        {
            if (_pageSize != value)
            {
                _pageSize = value;
                _currentPage = 1;
            }
        }
    }

    protected IEnumerable<UserModel> FilteredUsers =>
        string.IsNullOrWhiteSpace(_searchString)
            ? _users
            : _users.Where(u =>
                (u.Username?.Contains(_searchString, StringComparison.OrdinalIgnoreCase) == true) ||
                (u.Role?.Contains(_searchString, StringComparison.OrdinalIgnoreCase) == true) ||
                u.Id.ToString().Contains(_searchString));

    protected IEnumerable<UserModel> PagedUsers =>
        FilteredUsers
            .Skip((_currentPage - 1) * _pageSize)
            .Take(_pageSize);

    private int PageCount => Math.Max(1, (int)Math.Ceiling(FilteredUsers.Count() / (double)Math.Max(1, _pageSize)));

    protected override async Task OnInitializedAsync()
    {
        await LoadUsers();
    }

    protected override void OnParametersSet()
    {
        if (_currentPage > PageCount)
            _currentPage = PageCount;
    }

    private async Task LoadUsers()
    {
        var request = new GridItemsProviderRequest<UserModel>
        {
            StartIndex = 0,
            Count = null
        };

        var result = await UserService.GetItems(request);
        _users = result.Items?.ToList() ?? new List<UserModel>();
    }

    protected async Task OpenAddUserDialog()
    {
        var options = new DialogOptions
        {
            CloseOnEscapeKey = true,
            MaxWidth = MaxWidth.Small,
            FullWidth = true
        };

        var dialog = await DialogService.ShowAsync<AddUserDialog>("", options);
        var result = await dialog.Result;

        if (!result.Canceled)
        {
            Logger.LogInformation(@Localizer["AlertAdd"]);
            
            await LoadUsers();
            StateHasChanged();
        }
    }

    protected async Task DeleteUser(int id)
    {
        // 1. Préparer le message de confirmation
        bool? result = await DialogService.ShowMessageBox(
            @Localizer["Confirmation"],
            @Localizer["ConfirmDelete"],
            yesText: @Localizer["Delete"],
            cancelText: @Localizer["Cancel"]);

        // 2. Si l'utilisateur a cliqué sur "Supprimer" (vrai)
        if (result == true)
        {
            try
            {
                var userToDelete = _users.FirstOrDefault(u => u.Id == id);
                string userInfos = userToDelete != null ? $"{userToDelete.Username} ({userToDelete.Role})" : "Inconnu";

                Logger.LogInformation("ACTION : Demande de suppression de l'utilisateur ID {Id} - {UserInfos}", id, userInfos);

                await UserService.Delete(id);

                _users.RemoveAll(u => u.Id == id);

                if (_currentPage > PageCount)
                    _currentPage = PageCount;

                Logger.LogInformation("SUCCÈS : L'utilisateur ID {Id} a été supprimé correctement.", id);

                StateHasChanged();
            }
            catch (Exception ex)
            {
                Logger.LogError(ex, "ERREUR : Impossible de supprimer l'utilisateur ID {Id}", id);
            }
        }
    }

    protected async Task EditUser(int id)
    {
        // 1. Trouver l'utilisateur dans la liste locale
        var user = _users.FirstOrDefault(u => u.Id == id);
        if (user == null) return;

        // 2. Pr�parer les param�tres pour le dialogue
        var parameters = new DialogParameters<AddUserDialog>
    {
        { x => x.UserToEdit, user }
    };

        var options = new DialogOptions
        {
            CloseOnEscapeKey = true,
            MaxWidth = MaxWidth.Small,
            FullWidth = true
        };

        // 3. Ouvrir le dialogue
        var dialog = await DialogService.ShowAsync<AddUserDialog>("", parameters, options);
        var result = await dialog.Result;

        // 4. Si valid�, recharger la liste
        if (!result.Canceled)
        {
            await LoadUsers();
            StateHasChanged();
        }
    }
    protected void OnPageChanged(int newPage)
    {
        _currentPage = newPage;
    }

    private void OnPageSizeChanged(int newSize)
    {
        _pageSize = newSize;
        _currentPage = 1;
    }

    private void OnSearchChanged(string text)
    {
        _searchString = text;
        _currentPage = 1;
    }
}