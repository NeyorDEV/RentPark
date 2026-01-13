using BlazorAdminRentPark.Components;
using BlazorAdminRentPark.Services;
using Microsoft.AspNetCore.Localization;
using Microsoft.Extensions.Options;
using MudBlazor.Services;
using System.Globalization;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
builder.Services.AddRazorComponents()
    .AddInteractiveServerComponents();

builder.Services.AddMudServices();

builder.Services.AddHttpClient();
builder.Services.AddScoped<IClientService, ClientService>(); 
builder.Services.AddScoped<IVehiculeService, VehiculeService>();
builder.Services.AddScoped<IUserService, UserService>();

builder.Services.AddControllers();

builder.Services.AddLocalization(opts => { opts.ResourcesPath = "Resources"; });

builder.Services.Configure<RequestLocalizationOptions>(options =>
{
    options.DefaultRequestCulture = new RequestCulture(new CultureInfo("fr-FR"));

    options.SupportedCultures = new List<CultureInfo> { new CultureInfo("fr-FR"), new CultureInfo("en-US") };
    options.SupportedUICultures = new List<CultureInfo> { new CultureInfo("fr-FR"), new CultureInfo("en-US") };
});

var app = builder.Build();

// Configure the HTTP request pipeline.
if (!app.Environment.IsDevelopment())
{
    app.UseExceptionHandler("/Error", createScopeForErrors: true);
    // The default HSTS value is 30 days. You may want to change this for production scenarios, see https://aka.ms/aspnetcore-hsts.
    app.UseHsts();
}



app.UseHttpsRedirection();

var options = ((IApplicationBuilder)app).ApplicationServices.GetService<IOptions<RequestLocalizationOptions>>();

if (options?.Value != null)
{
    app.UseRequestLocalization(options.Value);
}

app.MapControllers();

app.UseAntiforgery();

app.MapStaticAssets();
app.MapRazorComponents<App>()
    .AddInteractiveServerRenderMode();

app.Run();
