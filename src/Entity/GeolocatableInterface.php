<?php

namespace App\Entity;

interface GeolocatableInterface
{
    public function getLatitude(): ?string;
    public function getLongitude(): ?string;
    public function getAddress(): ?string;
}