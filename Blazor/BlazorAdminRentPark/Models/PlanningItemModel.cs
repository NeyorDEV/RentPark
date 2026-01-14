public class PlanningItemModel
{
    public string DateDebut { get; set; }
    public string DateFin { get; set; }
    public string Marque { get; set; }
    public string Modele { get; set; }

    // Propriétés calculées pour l'affichage dans l'interface
    public string DateAffichage => DateDebut;
    public string Description => $"Location : {Marque} {Modele}";
}