<?php

namespace App\Supplier\app\Models\Me;

use JsonSerializable;

class SupplierModel implements JsonSerializable{

    private $id;
    private $name;
    private $tax_number;
    private $street;
    private $street_number;
    private $city;
    private $zip;
    private $phone;
    private $email;
    private $country_code;
    private $logo_url;
    private $iban;
    private $website_url;
    private $notes;
    private $logo_signed_url;
    private $currency;

    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->tax_number = $data['tax_number'] ?? null;
        $this->street = $data['street'] ?? null;
        $this->street_number = $data['street_number'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->zip = $data['zip'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->country_code = $data['country_code'] ?? null;
        $this->logo_url = $data['logo_url'] ?? null;
        $this->iban = $data['iban'] ?? null;
        $this->website_url = $data['website_url'] ?? null;
        $this->notes = $data['notes'] ?? null;
        $this->logo_signed_url = $data['logo_signed_url'] ?? null;
        $this->currency = $data['currency'] ?? 'EUR';
    }

     public function jsonSerialize(): mixed
     {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tax_number' => $this->tax_number,
            'street' => $this->street,
            'street_number' => $this->street_number,
            'city' => $this->city,
            'zip' => $this->zip,
            'phone' => $this->phone,
            'email' => $this->email,
            'country_code' => $this->country_code,
            'logo_url' => $this->logo_url,
            'iban' => $this->iban,
            'website_url' => $this->website_url,
            'notes' => $this->notes,
        ];
    }


    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getTaxNumber() { return $this->tax_number; }
    public function getStreet() { return $this->street; }
    public function getStreetNumber() { return $this->street_number; }
    public function getCity() { return $this->city; }
    public function getZip() { return $this->zip; }
    public function getPhone() { return $this->phone; }
    public function getEmail() { return $this->email; }
    public function getCountryCode() { return $this->country_code; }
    public function getLogoUrl() { return $this->logo_url; }
    public function getIban() { return $this->iban; }
    public function getWebsiteUrl() { return $this->website_url; }
    public function getNotes() { return $this->notes; }
    public function getLogoSignedUrl() { return $this->logo_signed_url; }
    public function getCurrency(): string { return $this->currency ?? 'EUR'; }


}