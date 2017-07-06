class Items {

    
    function set_item_id($item_id) {
      $this->item_id = $item_id;
      return $this;
    }
    function set_service($service) {
      $this->service = $service;
      return $this;
    }
    function set_product_id($product_id) {
      $this->product_id = $product_id;
      return $this;
    }
    function set_price($price) {
      $price = round($price, 2, PHP_ROUND_HALF_ODD);
      $this->price = $price;
      return $this;
    }
    function set_details($details) {
      $this->details = $details;
      return $this;
    }
    function set_reference($reference = array()) {
      $ref = array();
      $ref['ref1'] = (isset($reference['ref1']) ? $reference['ref1'] : '');
      $ref['ref2'] = (isset($reference['ref2']) ? $reference['ref2'] : '');
      $ref['ref3'] = (isset($reference['ref3']) ? $reference['ref3'] : '');
      $this->reference = $ref;
      return $this;
    }
  }
