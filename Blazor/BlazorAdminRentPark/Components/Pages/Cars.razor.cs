using Microsoft.AspNetCore.Components;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class Cars
    {
        protected bool _drawerOpen = true;

        protected void ToggleDrawer()
        {
            _drawerOpen = !_drawerOpen;
        }

        public class CarModel
        {
            public int Id { get; set; }
            public string Name { get; set; } = string.Empty;
            public string Category { get; set; } = string.Empty;
            public string Range { get; set; } = string.Empty;
            public int Seats { get; set; }
            public int Bags { get; set; }
            public double PricePerDay { get; set; }
            public string ImageUrl { get; set; } = "https://via.placeholder.com/300x200?text=Voiture";
        }

        protected List<CarModel> _cars = new()
        {
            new CarModel
            {
                Id = 1,
                Name = "Citroën E-C3 ou similaire",
                Category = "Citadine SUV Automatique",
                Range = "293km",
                Seats = 4,
                Bags = 2,
                PricePerDay = 22.74,
                ImageUrl = "car_image_1.jpg"
            },
            new CarModel
            {
                Id = 2,
                Name = "VW Polo ou similaire",
                Category = "Citadine Berline Manuelle",
                Range = "350km",
                Seats = 5,
                Bags = 3,
                PricePerDay = 27.32,
                ImageUrl = "car_image_2.jpg"
            },
            new CarModel
            {
                Id = 3,
                Name = "Opel Mokka Electric",
                Category = "Compact SUV Automatique",
                Range = "340km",
                Seats = 5,
                Bags = 3,
                PricePerDay = 27.32,
                ImageUrl = "car_image_3.jpg"
            },
            new CarModel { Id = 4, Name = "Peugeot 208", Category = "Citadine", Range = "400km", Seats = 5, Bags = 2, PricePerDay = 35.00 },
            new CarModel { Id = 5, Name = "Tesla Model 3", Category = "Berline Électrique", Range = "500km", Seats = 5, Bags = 4, PricePerDay = 85.00 },
        };
    }
}
