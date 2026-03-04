<?php

namespace App;
use App\Shared\Hateoas;
class UserLinks
{
    public static function build(string $id, string $role): array
    {
        $links = [
            "self" => Hateoas::link("/utilisateurs/$id", "GET")
        ];

        if ($role === "admin") {
            $links["update"] = Hateoas::link("/utilisateurs/$id", "PUT");
            $links["delete"] = Hateoas::link("/utilisateurs/$id", "DELETE");
        }

        return $links;
    }
}