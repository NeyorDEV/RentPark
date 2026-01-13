using Microsoft.AspNetCore.Components;
using MudBlazor;

namespace BlazorAdminRentPark.Components.Pages
{
    // L'ajout de "partial" est OBLIGATOIRE pour lier les deux fichiers
    public partial class Account
    {
        [Inject] private IDialogService DialogService { get; set; }

        private string _searchString = "";
        private int _currentPage = 1;
        private int _pageSize = 8;

        private async Task OpenAddUserDialog()
        {
            var options = new DialogOptions { CloseOnEscapeKey = true, MaxWidth = MaxWidth.Small, FullWidth = true };
            var dialog = await DialogService.ShowAsync<AddUserDialog>("", options);
            var result = await dialog.Result;
            if (!result.Canceled) { /* Logique de rafraîchissement */ }
        }

        private void OnPageChanged(int newPage)
        {
            _currentPage = newPage;
        }

        private IEnumerable<UserDto> FilteredUsers =>
            string.IsNullOrWhiteSpace(_searchString)
                ? _users
                : _users.Where(u =>
                    (u.Username?.Contains(_searchString, StringComparison.OrdinalIgnoreCase) == true) ||
                    (u.Role?.Contains(_searchString, StringComparison.OrdinalIgnoreCase) == true) ||
                    u.Id.ToString().Contains(_searchString));

        private IEnumerable<UserDto> PagedUsers =>
            FilteredUsers.Skip((_currentPage - 1) * _pageSize).Take(_pageSize);

        private int PageCount =>
            Math.Max(1, (int)Math.Ceiling(FilteredUsers.Count() / (double)_pageSize));

        public class UserDto
        {
            public int Id { get; set; }
            public string Username { get; set; }
            public string Role { get; set; }
        }

        private List<UserDto> _users = new()
        {
            new UserDto { Id = 31, Username = "neyorrr", Role = "admin" },
            new UserDto { Id = 36, Username = "popilo", Role = "admin" },
            new UserDto { Id = 37, Username = "popo", Role = "admin" },
            new UserDto { Id = 38, Username = "lili", Role = "admin" },
            new UserDto { Id = 39, Username = "papa", Role = "admin" },
            new UserDto { Id = 41, Username = "poulet", Role = "admin" },
            new UserDto { Id = 42, Username = "testcontrolle", Role = "admin" },
            new UserDto { Id = 43, Username = "lalalalal", Role = "admin" }
        };
    }
}