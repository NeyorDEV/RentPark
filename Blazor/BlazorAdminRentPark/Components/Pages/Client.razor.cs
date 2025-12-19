using BlazorAdminRentPark.Services;
using Microsoft.AspNetCore.Components;
using Microsoft.AspNetCore.Components.QuickGrid;
using System.ComponentModel.DataAnnotations;

namespace BlazorAdminRentPark.Components.Pages
{
    public partial class Client
    {
        public int Id { get; set; }
        public string Name { get; set; }
        public string Surname { get; set; }
        public DateTime BirthDate { get; set; }
        public string Nationality { get; set; }
        public string PhoneNumber { get; set; }
        public string Email { get; set; }
        public string LicenseNumber { get; set; }
        public string Comment { get; set; }

        private GridItemsProviderRequest<Client> request;

        [Inject]
        private IClientService ClientService { get; init; }

        protected async override Task OnAfterRenderAsync(bool firstRender)
        {
            var result = await ClientService.GetItems(request);
        }

        /* [Required]
        [StringLength(50, ErrorMessage = "Le prénom affiché ne doit pas dépasser 50 caractères.")]
        public string FirstName { get; set; }

        [Required]
        [StringLength(50, ErrorMessage = "Le nom de famille ne doit pas dépasser 50 caractères.")]
        [RegularExpression(@"^[a-z''-'\s]{1,50}$", ErrorMessage = "Seulement les caractères en minuscule sont acceptées.")]
        public string LastName { get; set; }

        public string Address { get; set; }

        public string City { get; set; }

        [Phone]
        public string Phone { get; set; }

        [EmailAddress]
        public string Email { get; set; }

        public bool IsExternal { get; set; }

        [Required]
        public string Role { get; set; }

        public List<string> Qualifications { get; set; }

        [Required(ErrorMessage = "L'image de la personne est obligatoire !")]
        public byte[] ImageContent { get; set; }

        public string Comment { get; set; }
        */
    }
}
