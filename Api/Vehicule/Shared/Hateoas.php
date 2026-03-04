<?php
namespace App\Shared;
class Hateoas
{
    public static function link(string $href, string $method): array
    {
        return [
            "href" => $href,
            "method" => $method
        ];
    }
}