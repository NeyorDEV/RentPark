<?php

namespace App;
use App\Shared\Hateoas;
class VehiculeLinks
{
    public static function build(string $numSerie, string $role): array
    {
        $links = [
            "self" => Hateoas::link("/voitures/$numSerie", "GET")
        ];

        if ($role === "admin") {
            $links["update"] = Hateoas::link("/voitures/$numSerie", "PUT");
            $links["delete"] = Hateoas::link("/delete/voitures/$numSerie", "DELETE");
        }

        return $links;
    }
}