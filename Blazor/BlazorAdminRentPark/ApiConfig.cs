namespace BlazorAdminRentPark;

/// <summary>URLs de l'API et des images, surchargeables par variables d'environnement (API_URL, IMAGES_URL).</summary>
public static class ApiConfig
{
    public static readonly string Api = (Environment.GetEnvironmentVariable("API_URL") ?? "http://localhost:8880").TrimEnd('/');
    public static readonly string Images = (Environment.GetEnvironmentVariable("IMAGES_URL") ?? "http://localhost:9990").TrimEnd('/');
}
