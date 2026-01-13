using BlazorAdminRentPark.Models;
using BlazorAdminRentPark.UiModels;

namespace BlazorAdminRentPark.Factories
{
    public static class UserFactory
    {
        public static UserUiModel ToUiModel(UserModel item)
        {
            return new UserUiModel
            {
                Id = item.Id,
                Password = item.Password,
                Role = item.Role,
                Username = item.Username
            };
        }

        public static UserModel Create(UserUiModel model)
        {
            return new UserModel
            {
                Id = model.Id,
                Password = model.Password,
                Role = model.Role,
                Username = model.Username
            };
        }
    }
}
