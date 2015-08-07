<?php
class ModelPaymentTodopago extends Model {
  public function getMethod($address, $total) {
    $this->load->language('payment/todopago');
    $method_data = array(
      'code'     => 'todopago',
      'title'    => $this->language->get('text_title'),
      'sort_order' => $this->config->get('todopago_sort_order')
      );
    
    return $method_data;
  }
    
    public function getProducts($order_id){
        $products = $this->db->query("SELECT op.product_id, op.total, op.name, op.price, op.quantity, pd.description FROM `".DB_PREFIX."order_product` op INNER JOIN `".DB_PREFIX."product_description` pd ON op.product_id = pd.product_id  WHERE `order_id`=$order_id");  
        return $products->rows;
    }
  
  public function getProductCode($productId){
      $productCode = $this->getAttribute($productId, "codigo del producto");
      return ($productCode != null)? $productCode : "default";
  }
    
  private function getAttribute($productId, $attribute){
    try{
      $query = $this->db->query("SELECT ".DB_PREFIX."product_attribute.text FROM ".DB_PREFIX."product_attribute JOIN ".DB_PREFIX."attribute ON ".DB_PREFIX."attribute.attribute_id = ".DB_PREFIX."product_attribute.attribute_id JOIN ".DB_PREFIX."attribute_description ON ".DB_PREFIX."attribute.attribute_id = ".DB_PREFIX."attribute_description.attribute_id JOIN ".DB_PREFIX."attribute_group_description ON ".DB_PREFIX."attribute.attribute_group_id = ".DB_PREFIX."attribute_group_description.attribute_group_id WHERE product_id = 31 AND ".DB_PREFIX."attribute_description.name = '".$attribute."' AND ".DB_PREFIX."attribute_group_description.name = 'Prevencion de Fraude'");
      
      if(array_key_exists ( 'text' , $query->row )){
          return $att = $query->row['text'];  
      }     
    }catch (Exception $e){
        return "default";
      }
    }
    
    public function getCouponCode($order_id){
        $coupon_id = $this->db->query("SELECT coupon_id FROM  `".DB_PREFIX."coupon_history` WHERE `order_id` = $order_id");
            if(isset($coupon_id->row['coupon_id'])){
                $coupon_id = $coupon_id->row['coupon_id'];
                $coupon_code = $this->db->query("SELECT code FROM `".DB_PREFIX."coupon` WHERE `coupon_id` = $coupon_id");
                return $coupon_code->row['code'];
            }
        else{
            return null;
        }
    }
    
    public function getProvinceCode($ocCode, $order_id){
        $csCode = $this->db->query("select z.cs_code from ".DB_PREFIX."zone z inner join ".DB_PREFIX."country c on  z.country_id = c.country_id where c.iso_code_2 = 'AR' and code = '".$ocCode."';");
        
        return $csCode->row['cs_code'];
    }
    
    public function getQtyOrders($customerId){
        $qty = $this->db->query("SELECT COUNT(*) AS qty FROM ".DB_PREFIX."order WHERE customer_id = $customerId;");
        return $qty->row['qty'];
    }
    
    public function getDeadLine(){
        return $this->config->get('deadline');
    }
    
    public function getVersion(){
        $actualVersionQuery = $this->db->query("SELECT value FROM `".DB_PREFIX."setting` WHERE `group` = 'todopago' AND `key` = 'version'");
        $actualVersion = ($actualVersionQuery->num_rows == 0)? "0." : $actualVersionQuery->row['value'];
        if($actualVersion == "0."){
            $todopagoclavecolumn = $this->db->query("SHOW COLUMNS FROM `".DB_PREFIX."order` LIKE 'todopagoclave'"); //Esta consulta sólo es válida para MySQL 5.0.1+
        //En caso de ya tener instalada la v0.9.0 del plugin al instalarlo de nuevo la columna todopagoclave ya se econtraría creada.
            
            $actualVersion .= ($todopagoclavecolumn->num_rows != 1)? "0.0" : "9.0";
        }
	   return $actualVersion;
    }
  }
