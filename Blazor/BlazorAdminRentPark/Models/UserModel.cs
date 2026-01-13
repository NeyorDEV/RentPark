using System.Text.Json.Serialization;

namespace BlazorAdminRentPark.Models
{
    public class UserModel
    {
        public int Id { get; init; }
        public string Username { get; set; }
        public string Password { get; set; }
        public string Role { get; set; }
    }
}
