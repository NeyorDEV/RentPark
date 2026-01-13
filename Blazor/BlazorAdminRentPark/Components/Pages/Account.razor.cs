using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.Services;
using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.QuickGrid;
using MudBlazor;

namespace BlazorAdminRentPark.Components.Pages;

public partial class Account
{
    [Inject]
    private IUserService UserService { get; set; } = default!;

    private string _searchString = "";
    private int _currentPage = 1;
    private int _pageSize = 8;
    private List<UserModel> _users = [];

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

    protected int PageCount =>
        Math.Max(1, (int)Math.Ceiling(FilteredUsers.Count() / (double)_pageSize));

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
        _users = result.Items?.ToList() ?? [];
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
            await LoadUsers();
            StateHasChanged();
        }
    }

    protected async Task DeleteUser(int id)
    {
        await UserService.Delete(id);
        _users.RemoveAll(u => u.Id == id);
        
        if (_currentPage > PageCount)
            _currentPage = PageCount;
        
        StateHasChanged();
    }

    protected void EditUser(int id)
    {
        // TODO: Implémenter la logique de modification
    }

    protected void OnPageChanged(int newPage)
    {
        _currentPage = newPage;
    }
}