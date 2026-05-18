<?php
namespace App\Supplier\app\Models\Catalog;


class ProductModel implements \JsonSerializable
{
    private $id;
    private $supplier_id;
    private $sku;
    private $name;
    private $package_size;
    private $package_unit;
    private $vat_rate;
    private $created_at;
    private $active;
    private $weight_grams;
    private $weight_unit;
    private $shelf_life_days;
    private $specification = [];
    private $allergens = [];
    private $photo_url;

    public function __construct($data)
    {
        $this->id               = $data['id'];
        $this->name             = $data['name'];
        $this->supplier_id      = $data['supplier_id'] ?? null;
        $this->sku              = $data['sku'];
        $this->package_size     = $data['package_size'];
        $this->package_unit     = $data['package_unit'] ?? null;
        $this->vat_rate         = $data['vat_rate'] ?? null;
        $this->created_at       = $data['created_at'] ?? null;
        $this->active           = $data['is_active'] ?? false;
        $this->weight_grams     = $data['weight_grams'] ?? null;
        $this->weight_unit      = $data['weight_unit'] ?? 'g';
        $this->shelf_life_days  = $data['shelf_life_days'] ?? null;
        $this->photo_url        = $data['photo_url'] ?? null;

        if (isset($data['allergens'])) {
            foreach ($data['allergens'] as $allergen) {
                $this->allergens[] = new AllergenModel($allergen);
            }
        }

        if(isset($data['specification']) && is_array($data['specification'])) {
            foreach ($data['specification'] as $spec) {
                $this->specification[] = new SpecificationModel($spec);
            }
        }

    }

    public function getId()              { return $this->id; }
    public function getName()            { return $this->name; }
    public function getSupplierId()      { return $this->supplier_id; }
    public function getSku()             { return $this->sku; }
    public function getPackageSize()     { return $this->package_size; }
    public function getPackageUnit()     { return $this->package_unit; }
    public function getVatRate()         { return $this->vat_rate; }
    public function getCreatedAt()       { return $this->created_at; }
    public function getAllergens()        { return $this->allergens; }
    public function getActive()          { return $this->active; }
    public function getWeightGrams()     { return $this->weight_grams; }
    public function getWeightUnit()      { return $this->weight_unit; }
    public function getShelfLifeDays()   { return $this->shelf_life_days; }
    public function getPhotoUrl()         { return $this->photo_url; }
    public function getSpecification()     { return $this->specification; }

    public function jsonSerialize(): mixed
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'allergens'        => $this->allergens,
            'supplier_id'      => $this->supplier_id,
            'sku'              => $this->sku,
            'package_size'     => $this->package_size,
            'package_unit'     => $this->package_unit,
            'vat_rate'         => $this->vat_rate,
            'created_at'       => $this->created_at,
            'active'           => $this->active,
            'weight_grams'     => $this->weight_grams,
            'weight_unit'      => $this->weight_unit,
            'shelf_life_days'  => $this->shelf_life_days,
            'photo_url'        => $this->photo_url,
            'specification'    => $this->specification,
        ];
    }
}

