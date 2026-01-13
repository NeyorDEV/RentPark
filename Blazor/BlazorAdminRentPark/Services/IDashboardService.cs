using BlazorAdminRentPark.Models;

namespace BlazorAdminRentPark.Services
{
    public interface IDashboardService
    {
        Task<int> GetTotalUsersAsync();
        Task<int> GetMonthlyRevenuesAsync();
        Task<string> GetMostRentedCarImageAsync();
        Task<List<RappelItemModel>> GetRappelsAsync();
        Task<List<PlanningItemModel>> GetPlanningAsync();
    }
}