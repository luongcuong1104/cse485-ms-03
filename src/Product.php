<?php
// src/Product.php

class Product {
    public string $sku;
    public string $name;
    public int $categoryId;
    public int $price;
    public int $qty;

    public function __construct(string $sku, string $name, int $categoryId, int $price, int $qty) {
        $this->sku = $sku;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->price = $price;
        $this->qty = $qty;
    }

    /**
     * Tính thành tiền của sản phẩm này (price * qty)
     * 
     * @return int
     */
    public function lineTotal(): int {
        return $this->price * $this->qty;
    }

    /**
     * Xác định mức tồn kho dựa trên số lượng (qty)
     * 
     * @return string
     */
    public function stockLevel(): string {
        if ($this->qty >= 5) {
            return "Du";
        } elseif ($this->qty >= 2) {
            return "Sap het";
        } else {
            return "Can nhap";
        }
    }

    /**
     * Trả về mảng thông tin sản phẩm phục vụ debug
     * 
     * @return array
     */
    public function toArray(): array {
        return [
            'sku' => $this->sku,
            'name' => $this->name,
            'category_id' => $this->categoryId,
            'price' => $this->price,
            'qty' => $this->qty
        ];
    }
}
