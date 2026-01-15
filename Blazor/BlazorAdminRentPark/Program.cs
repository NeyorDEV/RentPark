using BlazorAdminRentPark.Components;
using BlazorAdminRentPark.Services;
using Blazored.LocalStorage;
using Blazored.SessionStorage;
using Microsoft.AspNetCore.Components.Authorization;
using Microsoft.AspNetCore.Authentication.Cookies;
using Microsoft.AspNetCore.Localization;
using Microsoft.Extensions.Options;
using MudBlazor.Services;
using System.Globalization;
using Serilog;
using Serilog.Events;


var builder = WebApplication.CreateBuilder(args);

builder.Host.UseSerilog((context, configuration) =>
    configuration
        .ReadFrom.Configuration(context.Configuration)
        .Enrich.FromLogContext()
        .MinimumLevel.Information()
        .MinimumLevel.Override("Microsoft", LogEventLevel.Warning)
        .MinimumLevel.Override("System", LogEventLevel.Warning)
        .MinimumLevel.Override("Microsoft.Hosting.Lifetime", LogEventLevel.Information)
        .WriteTo.Console()
        .WriteTo.File("logs/log-.txt", rollingInterval: RollingInterval.Day));


builder.Services.AddRazorComponents()
    .AddInteractiveServerComponents();
builder.Services.AddMudServices();

builder.Services.AddHttpClient();
builder.Services.AddScoped<IClientService, ClientService>(); 
builder.Services.AddScoped<IVehiculeService, VehiculeService>();
builder.Services.AddScoped<IUserService, UserService>();
builder.Services.AddScoped<IDashboardService, DashboardService>();

builder.Services.AddBlazoredLocalStorage();
builder.Services.AddBlazoredSessionStorage();

builder.Services.AddAuthorizationCore();

builder.Services.AddCascadingAuthenticationState();

builder.Services.AddAuthentication(CookieAuthenticationDefaults.AuthenticationScheme)
    .AddCookie(options =>
    {
        options.Cookie.Name = "RentParkAuth";
        options.LoginPath = "/login";
        options.ExpireTimeSpan = TimeSpan.FromMinutes(60);
        options.SlidingExpiration = true;
    });

builder.Services.AddControllers();

builder.Services.AddLocalization(opts => { opts.ResourcesPath = "Resources"; });
builder.Services.Configure<RequestLocalizationOptions>(options =>
{
    var supportedCultures = new List<CultureInfo> 
    { 
        new CultureInfo("fr-FR"), 
        new CultureInfo("en-US") 
    };
    
    options.DefaultRequestCulture = new RequestCulture("fr-FR");
    options.SupportedCultures = supportedCultures;
    options.SupportedUICultures = supportedCultures;
});

var app = builder.Build();

app.UseSerilogRequestLogging(options =>
{
    options.EnrichDiagnosticContext = (diagnosticContext, httpContext) =>
    {
        var user = httpContext.User?.Identity?.Name ?? "Anonyme";
        diagnosticContext.Set("UserName", user);
    };

    options.GetLevel = (httpContext, elapsed, ex) =>
    {
        if (ex != null || httpContext.Response.StatusCode >= 500)
            return LogEventLevel.Error;

        var path = httpContext.Request.Path.Value?.ToLower();
        if (path == null) return LogEventLevel.Information;

        if (path.StartsWith("/_blazor") || 
            path.StartsWith("/_content") || 
            path.StartsWith("/lib") ||
            path.EndsWith(".css") || 
            path.EndsWith(".js") || 
            path.EndsWith(".png") || 
            path.EndsWith(".ico") || 
            path.EndsWith(".woff2"))
        {
            return LogEventLevel.Debug;
        }

        if (httpContext.Request.Method != "GET")
        {
            return LogEventLevel.Information;
        }

        if (httpContext.Response.StatusCode == 302 || httpContext.Response.StatusCode == 304)
        {
            return LogEventLevel.Debug; 
        }

        return LogEventLevel.Information;
    };
});


if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Error", createScopeForErrors: true);
    app.UseHsts();
}

app.UseHttpsRedirection();

app.MapStaticAssets();
app.UseAntiforgery();

var locOptions = app.Services.GetService<IOptions<RequestLocalizationOptions>>();
if (locOptions?.Value != null)
{
    app.UseRequestLocalization(locOptions.Value);
}

app.UseAuthentication();
app.UseAuthorization();

app.MapControllers();

app.MapRazorComponents<App>()
    .AddInteractiveServerRenderMode();

app.Run();
