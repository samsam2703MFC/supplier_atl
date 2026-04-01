<?php

namespace App\Supplier\app\Models\Price;

class PriceListItemModel implements \JsonSerializable
{
        private int $product_id;
        private ?int $price_id;
        private string $name;
        private string $sku;
        private string $package_size;
        private string $package_unit;
        private string $vat_rate;
        private string $price_net;
        private string $valid_from;
        private $is_active_to_sale;

    public function __construct($data)    {
        $this->product_id = $data['product_id'] ?? 0;
        $this->price_id = $data['price_id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->sku = $data['sku'] ?? '';
        $this->package_size = $data['package_size'] ?? '';
        $this->package_unit = $data['package_unit'] ?? '';
        $this->vat_rate = $data['vat_rate'] ?? '';
        $this->price_net = $data['price_net'] ?? '';
        $this->valid_from = $data['valid_from'] ?? '';
        $this->is_active_to_sale = $data['is_active_to_sale'] ?? '';
    }

    //getters
    public function getProductId(): int { return $this->product_id; }
    public function getPriceId(): int { return $this->price_id; }
    public function getName(): string { return $this->name; }
    public function getSku(): string { return $this->sku; }
    public function getPackageSize(): string { return $this->package_size; }
    public function getPackageUnit(): string { return $this->package_unit; }
    public function getVatRate(): string { return $this->vat_rate; }
    public function getPriceNet(): string { return $this->price_net; }
    public function getValidFrom(): string { return $this->valid_from; }
    public function getIsActiveToSaleStatus(): string { return $this->is_active_to_sale; }


    public function jsonSerialize(): mixed
    {
        return [
            'product_id' => $this->product_id,
            'price_id' => $this->price_id,
            'name' => $this->name,
            'sku' => $this->sku,
            'package_size' => $this->package_size,
            'package_unit' => $this->package_unit,
            'vat_rate' => $this->vat_rate,
            'price_net' => $this->price_net,
            'valid_from' => $this->valid_from,
            'is_active_to_sale' => $this->is_active_to_sale,
        ];
    }
}