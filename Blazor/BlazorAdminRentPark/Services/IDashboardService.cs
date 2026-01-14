using BlazorAdminRentPark.Models;

namespace BlazorAdminRentPark.Services
{
    public interface IDashboardService
    {
        Task<int> GetTotalUsersAsync();
        Task<int> GetMonthlyRevenuesAsync();
        Task<CarDetails> GetMostRentedCarDetailsAsync();
        Task<List<RappelItemModel>> GetRappelsAsync();
        Task<List<PlanningItemModel>> GetPlanningAsync();
    }
}