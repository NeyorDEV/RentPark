namespace BlazorAdminRentPark.Services;

using System.Security.Claims;
using Blazored.SessionStorage;
using Microsoft.AspNetCore.Components.Authorization;

public class CustomAuthStateProvider : AuthenticationStateProvider
{
    private readonly ISessionStorageService _sessionStorage;

    public CustomAuthStateProvider(ISessionStorageService sessionStorage)
    {
        _sessionStorage = sessionStorage;
    }
    
    public override async Task<AuthenticationState> GetAuthenticationStateAsync()
    {
        try
        {
            var token = await _sessionStorage.GetItemAsync<string>("authToken");

            if (string.IsNullOrEmpty(token))
            {
                return new AuthenticationState(new ClaimsPrincipal(new ClaimsIdentity()));
            }

            var identity = new ClaimsIdentity(new[]
            {
                new Claim(ClaimTypes.Name, "Utilisateur"),
            }, "apiauth_type");

            var user = new ClaimsPrincipal(identity);
            return new AuthenticationState(user);
        }
        catch (Exception)
        {
            return new AuthenticationState(new ClaimsPrincipal(new ClaimsIdentity()));
        }
    }

    public async Task MarkUserAsAuthenticated(string token)
    {
        await _sessionStorage.SetItemAsync("authToken", token);

        var identity = new ClaimsIdentity(new[]
        {
            new Claim(ClaimTypes.Name, "Utilisateur"),
        }, "apiauth_type");

        var user = new ClaimsPrincipal(identity);

        NotifyAuthenticationStateChanged(Task.FromResult(new AuthenticationState(user)));
    }

    public async Task MarkUserAsLoggedOut()
    {
        await _sessionStorage.RemoveItemAsync("authToken");

        var anonyme = new ClaimsPrincipal(new ClaimsIdentity());
        
        NotifyAuthenticationStateChanged(Task.FromResult(new AuthenticationState(anonyme)));
    }
}