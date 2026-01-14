namespace BlazorAdminRentPark.Services;
using System.Security.Claims;
using Blazored.LocalStorage;
using Microsoft.AspNetCore.Components.Authorization;

public class CustomAuthStateProvider : AuthenticationStateProvider
{
    private readonly ILocalStorageService _localStorage;

    public CustomAuthStateProvider(ILocalStorageService localStorage)
    {
        _localStorage = localStorage;
    }
    
    public override async Task<AuthenticationState> GetAuthenticationStateAsync()
    {
        try
        {
            var token = await _localStorage.GetItemAsync<string>("authToken");

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
        catch (InvalidOperationException) 
        {
            return new AuthenticationState(new ClaimsPrincipal(new ClaimsIdentity()));
        }
        catch (Exception)
        {
            return new AuthenticationState(new ClaimsPrincipal(new ClaimsIdentity()));
        }
    }

    public void MarkUserAsAuthenticated(string token)
    {
        var identity = new ClaimsIdentity(new[]
        {
            new Claim(ClaimTypes.Name, "Utilisateur"),
        }, "apiauth_type");

        var user = new ClaimsPrincipal(identity);

        NotifyAuthenticationStateChanged(Task.FromResult(new AuthenticationState(user)));
    }

    public void MarkUserAsLoggedOut()
    {
        var anonyme = new ClaimsPrincipal(new ClaimsIdentity());
        
        NotifyAuthenticationStateChanged(Task.FromResult(new AuthenticationState(anonyme)));
    }
}