using Microsoft.AspNetCore.Authentication;
using Microsoft.AspNetCore.Authentication.Cookies;
using Microsoft.AspNetCore.Mvc;
using System.Security.Claims;
using BlazorAdminRentPark.Services;

namespace BlazorAdminRentPark.Controllers
{
    [Route("Account")] 
    public class AccountController : Controller
    {
        private readonly IUserService _userService;

        public AccountController(IUserService userService)
        {
            _userService = userService;
        }

        [HttpPost("Login")]
        [IgnoreAntiforgeryToken]
        public async Task<IActionResult> Login([FromForm] string username, [FromForm] string password, [FromForm] string returnUrl = "/")
        {
            var result = await _userService.LoginAsync(username, password);

            if (result != null && result.Success)
            {
                string role = result.Role?.ToLower() ?? "";

                if (role == "client")
                {
                    return Redirect("/login?error=unauthorized");
                }
                var claims = new List<Claim>
                {
                    new Claim(ClaimTypes.Name, result.Username ?? username),
                    new Claim(ClaimTypes.NameIdentifier, username)
                };

                var claimsIdentity = new ClaimsIdentity(claims, CookieAuthenticationDefaults.AuthenticationScheme);
                
                var authProperties = new AuthenticationProperties
                {
                    IsPersistent = false,
                    AllowRefresh = true
                };

                await HttpContext.SignInAsync(
                    CookieAuthenticationDefaults.AuthenticationScheme, 
                    new ClaimsPrincipal(claimsIdentity), 
                    authProperties);

                return LocalRedirect(returnUrl);
            }

            return Redirect("/login?error=true");
        }

        
        [HttpGet("Logout")]
        public async Task<IActionResult> Logout()
        {
            await HttpContext.SignOutAsync(CookieAuthenticationDefaults.AuthenticationScheme);
    
            return Redirect("/login");
        }
    }
}