namespace BlazorAdminRentPark.Controllers
{
    using Microsoft.AspNetCore.Localization;
    using Microsoft.AspNetCore.Mvc;

    /// <summary>
    /// The culture controller.
    /// </summary>
    [Route("[controller]/[action]")]
    public class CultureController : Controller
    {
        public IActionResult SetCulture(string culture, string redirectUri)
        {
            if (culture != null)
            {
                // Define a cookie with the selected culture
                this.HttpContext.Response.Cookies.Append(
                    CookieRequestCultureProvider.DefaultCookieName,
                    CookieRequestCultureProvider.MakeCookieValue(
                        new RequestCulture(culture)));
            }

            return this.LocalRedirect(redirectUri);
        }
    }
}
