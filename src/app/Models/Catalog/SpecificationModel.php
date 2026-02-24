<?php

namespace App\Supplier\app\Models\Catalog;


class SpecificationModel implements \JsonSerializable
{

    private $id;
    private $product_id;
    private $lang_code;
    private $storage_info;
    private $preparation_info;
    private $composition;
    private $created_at;
    private $updated_at;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->product_id = $data['product_id'];
        $this->lang_code = $data['lang_code'];
        $this->storage_info = $data['storage_info'];
        $this->preparation_info = $data['preparation_info'];
        $this->composition = $data['composition'];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'lang_code' => $this->lang_code,
            'storage_info' => $this->storage_info,
            'preparation_info' => $this->preparation_info,
            'composition' => $this->composition,
        ];
    }

    public function getId() { return $this->id; }
    public function getProductId() { return $this->product_id; }
    public function getLangCode() { return $this->lang_code; }
    public function getStorageInfo() { return $this->storage_info; }
    public function getPreparationInfo() { return $this->preparation_info; }
    public function getComposition() { return $this->composition; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }
}