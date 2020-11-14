<?php

class ModelCatalogProduct extends Model
{

    public function getProduct($product_id)
    {
        $query = $this->db->query("SELECT DISTINCT *, pd.name AS name, pd.meta_title as meta_title, pd.meta_description as meta_description, pd.meta_keywords as meta_keywords, pd.description as description, p.image, m.name AS manufacturer, ss.name AS stock_status, cd2.quote AS quote
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
            INNER JOIN " . DB_PREFIX . "category_description cd2 ON (cd2.category_id = p2c.category_id)
            LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
            LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)
            WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'  AND p.date_available <= NOW() AND p.status = 1 ");
        return $query->row;
    }

    public function getTotalAllProducts($letter = null)
    {
        $sql = "SELECT  COUNT( *) AS total
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
            LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) 
            WHERE p.status = '1'";
        if ($letter) {
            $sql .= "AND pd.name LIKE '" . $letter . "%'";
        }
        $query = $this->db->query($sql);

        return $query->row["total"];
    }

    public function getAllProducts($start = null, $limit = null, $letter = null)
    {
        $sql = "SELECT  pd.name, p.product_id, p.model, p2c.category_id, 'product/product&product_id' AS href  
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) 
            WHERE p.status = '1' ";
        if ($letter) {
            $sql .= "AND pd.name LIKE '" . $letter . "%'";
        }
        $sql .= " ORDER BY p2c.category_id, pd.name";

        if ($limit && $start) {
            $sql .= " LIMIT " . (int)$start . ', ' . (int)$limit;
        } elseif ($start) {
            $sql .= " LIMIT " . (int)$start;
        } elseif ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTotalAllParams($letter = false)
    {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "param_value WHERE param_id = 'kollektsii' AND is_active = '1' ";
        if ($letter) {
            $sql .= "AND value LIKE '" . $letter . "%'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getAllParams($start = null, $limit = null, $letter = null)
    {
        $sql = "SELECT param_value_id, value AS name, 'product/group&pv' AS href FROM " . DB_PREFIX . "param_value WHERE param_id = 'kollektsii' AND is_active = '1' ";
        if ($letter) {
            $sql .= "AND value LIKE '" . $letter . "%'";
        }
        $sql .= " ORDER BY sort_order, value";

        if ($limit && $start) {
            $sql .= " LIMIT " . (int)$start . ', ' . (int)$limit;
        } elseif ($start) {
            $sql .= " LIMIT " . (int)$start;
        } elseif ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $query = $this->db->query($sql);

        return $query->rows;
    }

//    private function getTopQuery($select_list = true) {
//        if ($select_list)
//            $select = '*, pd.name AS name, p.image, m.name AS manufacturer, ss.name AS stock_status';
//        else
//            $select = 'count(p.product_id) as total';
//        
//        $sql="SELECT DISTINCT " . $select . "
//            FROM " . DB_PREFIX . "product p
//            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
//            LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
//            LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
//            LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
//            LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)
//            WHERE pd.top_sort_order != 0 
//            AND pd.language_id = '" . (int) $this->config->get('config_language_id') . "' 
//            AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'  
//            AND p.date_available <= NOW() 
//            AND p.status = '1'";
//        return $sql;
//    }
//    public function getTopProducts($home=FALSE) {
//        $sql=  $this->getTopQuery();
//        if($home){
//            $sql.='ORDER BY pd.top_sort_order ASC';
//        }else {
//            $sql .= $this->gueryAddSortAndLimit();
//        }
//        
//        $query = $this->db->query($sql);
//        return $query->rows;
//    }
//    
//    public function getTopProductsTotal() {
//        $sql=  $this->getTopQuery(false);
//        //$sql .= $this->gueryAddSortAndLimit();
//        
//        
//        $query = $this->db->query($sql);
//        return $query->num_rows ? $query->row['total'] : 0;
//
//    }


//ALL products
    public function getProducts()
    {
        /*
                $query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, ss.name AS stock_status,
        wcd.unit AS weight_class FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd
        ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s
        ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m
        ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "stock_status ss
        ON (p.stock_status_id = ss.stock_status_id) LEFT JOIN " . DB_PREFIX . "weight_class_description wcd
        ON (p.weight_class_id = wcd.weight_class_id)
        WHERE pd.language_id = '" . (int) $this->config->get('config_language_id') . "'
        AND p2s.store_id = '" . (int) $this->config->get('config_store_id') . "'
        AND wcd.language_id = '" . (int) $this->config->get('config_language_id') . "'
        AND ss.language_id = '" . (int) $this->config->get('config_language_id') . "'
        AND p.date_available <= NOW() AND p.status = '1'");
        */

//by Novikov 2015 4 gra.ua
        $query = $this->db->query("SELECT DISTINCT * , pd.name AS name, p.image
FROM product p
LEFT JOIN product_description pd ON ( p.product_id = pd.product_id ) 
WHERE pd.language_id =  '1'
AND p.status =  '1'");
        return $query->rows;
    }


// BY id CATEGORY !!!
    public function getProductsByCategoryId($category_id)
    {
//by Novikov 4 gra.ua
        $q = "SELECT DISTINCT * , pd.name AS name, p.image
FROM product p
            LEFT JOIN product_description pd ON ( p.product_id = pd.product_id )
            INNER JOIN product_to_category p2c ON (p.product_id = p2c.product_id)
            INNER JOIN category_description cd2 ON (cd2.category_id = p2c.category_id)
WHERE pd.language_id =  '1'
AND p.status =  '1'
AND p2c.category_id=" . $category_id;
        $query = $this->db->query($q);
        return $query->rows;
    }


    public function getPath($category_id)
    {
        $string = $category_id . ',';
        $results = $this->model_catalog_category->getCategories($category_id);
        foreach ($results as $result) {
            $string .= $this->getPath($result['category_id']);
        }
        return $string;
    }

    public function getModuleProductsQuery()
    {
        $sql = "SELECT DISTINCT p.*, pd.name AS name, p.image, p.product_id, ss.name as stock_status 
                FROM " . DB_PREFIX . "product p 
                LEFT JOIN " . DB_PREFIX . "stock_status ss ON (ss.stock_status_id = p.stock_status_id) 
                INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
                INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) 
                INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
                WHERE p.status = '1' 
                AND p.date_available <= '" . date('Y-m-d H:i') . "' 
                AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
                AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
        return $sql;
    }

    public function getLatestProducts($limit, $category_id = 0)
    {
        $product_data = $this->cache->get('product.latest.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $limit . $category_id);
        if (!$product_data) {
            $sql = $this->getModuleProductsQuery();
            $sql .= "AND p.is_new = 1 ";
            if ($category_id) {
                $sql .= " AND p2c.category_id=" . $category_id;
            }
            $sql .= " ORDER BY p.date_added DESC LIMIT " . (int)$limit;

            $query = $this->db->query($sql);
            $product_data = $query->rows;
            $this->cache->set('product.latest.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $limit . $category_id, $product_data);
        }
        return $product_data;
    }

    public function getTopProducts()
    {
        $product_data = $this->cache->get('product.top.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));
        if (!$product_data) {
            $sql = $this->getModuleProductsQuery();
            $sql .= "AND p.is_top != 0    
                     ORDER BY p.is_top  ";
            $query = $this->db->query($sql);
            $product_data = $query->rows;

            $this->cache->set('product.top.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $product_data);
        }
        return $product_data;
    }

    public function getHitProducts($limit, $category_id = 0)
    {
        $product_data = $this->cache->get('product.hit.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $limit . $category_id);
        if (!$product_data) {
            $sql = $this->getModuleProductsQuery();
            $sql .= " AND p.is_hit = '1' ";
            if ($category_id) {
                $sql .= " AND p2c.category_id=" . $category_id;
            }
            $sql .= " LIMIT " . (int)$limit;
            $query = $this->db->query($sql);
            $product_data = $query->rows;
            $this->cache->set('product.hit.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $limit . $category_id, $product_data);
        }
        return $product_data;
    }

    public function getPopularProducts($limit)
    {
        $query = $this->db->query("SELECT *, pd.name AS name, p.image, m.name AS manufacturer, ss.name AS stock_status, (SELECT AVG(r.rating) FROM " . DB_PREFIX . "review r WHERE p.product_id = r.product_id GROUP BY r.product_id) AS rating FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id) WHERE p.status = '1' AND p.date_available <= '" . date('Y-m-d H:i') . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY p.viewed, p.date_added DESC LIMIT " . (int)$limit);
        return $query->rows;
    }

    public function getFeaturedProducts($limit)
    {
        $product_data = $this->cache->get('product.featured.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $limit);
        if (!$product_data) {
            $query = $this->db->query("SELECT *, pd.name AS name, p.image, m.name AS manufacturer, ss.name AS stock_status, (SELECT AVG(r.rating) FROM " . DB_PREFIX . "review r WHERE p.product_id = r.product_id GROUP BY r.product_id) AS rating FROM " . DB_PREFIX . "product_featured f LEFT JOIN " . DB_PREFIX . "product p ON (f.product_id=p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (f.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id) WHERE p.status = '1' AND p.date_available <= '" . date('Y-m-d H:i') . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT " . (int)$limit);
            $product_data = $query->rows;
            $this->cache->set('product.featured.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $limit, $product_data);
        }
        return $product_data;
    }

    public function getBestSellerProducts($limit)
    {
        $product_data = $this->cache->get('product.bestseller.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $limit);
        if (!$product_data) {
            $product_data = array();
            $query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) WHERE o.order_status_id > '0' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);
            foreach ($query->rows as $result) {
                $product_query = $this->db->query("SELECT *, ss.name as stock_status FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.product_id = '" . (int)$result['product_id'] . "' AND p.status = '1' AND p.date_available <= '" . date('Y-m-d H:i') . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

                if ($product_query->num_rows) {
                    $product_data[] = $product_query->row;
                }
            }
            $this->cache->set('product.bestseller.' . $this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $limit, $product_data);
        }
        return $product_data;
    }

    public function updateViewed($product_id)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = viewed + 1 WHERE product_id = '" . (int)$product_id . "'");
    }

    public function getProductOptions($product_id)
    {
        $product_option_data = array();
        $product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order");
        foreach ($product_option_query->rows as $product_option) {
            $product_option_value_data = array();

            $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY sort_order");

            foreach ($product_option_value_query->rows as $product_option_value) {
                $product_option_value_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value_description WHERE product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

                $product_option_value_data[] = array(
                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                    'name' => $product_option_value_description_query->row['name'],
                    'price' => $product_option_value['price'],
                    'prefix' => $product_option_value['prefix']
                );
            }

            $product_option_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_description WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

            $product_option_data[] = array(
                'product_option_id' => $product_option['product_option_id'],
                'name' => $product_option_description_query->row['name'],
                'option_value' => $product_option_value_data,
                'sort_order' => $product_option['sort_order']
            );
        }
        return $product_option_data;
    }

    public function getProductImages($product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
        return $query->rows;
    }

    public function getProductTags($product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tags WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
        return $query->rows;
    }

    public function getProductDiscount($product_id)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }
        $query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity = '1' AND ((date_start = '0000-00-00' OR date_start < '" . date('Y-m-d H:i') . "') AND (date_end = '0000-00-00' OR date_end > '" . date('Y-m-d H:i') . "')) ORDER BY priority ASC, price ASC LIMIT 1");
        if ($query->num_rows) {
            return $query->row['price'];
        } else {
            return FALSE;
        }
    }

    public function getProductDiscounts($product_id)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < '" . date('Y-m-d H:i') . "') AND (date_end = '0000-00-00' OR date_end > '" . date('Y-m-d H:i') . "')) ORDER BY quantity ASC, priority ASC, price ASC");
        return $query->rows;
    }

    public function getProductRelated($product_id)
    {
        $product_data = array();
        $product_related_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
        foreach ($product_related_query->rows as $result) {
            $product_query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, ss.name AS stock_status, (SELECT AVG(r.rating) FROM " . DB_PREFIX . "review r WHERE p.product_id = r.product_id GROUP BY r.product_id) AS rating FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id) WHERE p.product_id = '" . (int)$result['related_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= '" . date('Y-m-d H:i') . "' AND p.status = '1'");
            if ($product_query->num_rows) {
                $product_data[$result['related_id']] = $product_query->row;
            }
        }
        return $product_data;
    }

    public function getSimilarProducts($product_id, $category_id = false)
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "product p
            INNER JOIN product_description pd ON(pd.product_id=p.product_id) 
            INNER JOIN product_to_category p2c ON (p.product_id=p2c.product_id)
            WHERE p.product_id != '" . (int)$product_id . "' AND p.status = '1' AND pd.language_id='" . (int)$this->config->get('config_language_id') . "'";

        if ($category_id)
            $sql .= " AND p2c.category_id = '" . (int)$category_id . "'";

        $sql .= " LIMIT 20";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getSimilarPriceProducts($price, $product_id, $category_id)
    {

        $price_min = $price - $price * 0.10;
        $price_max = $price + $price * 0.10;

        $sql = "SELECT * FROM " . DB_PREFIX . "product p 
        INNER JOIN product_description pd ON(pd.product_id=p.product_id) 
        INNER JOIN product_to_category p2c ON (p.product_id=p2c.product_id)
        WHERE p.status = '1' 
        AND pd.language_id='" . (int)$this->config->get('config_language_id') . "'
        AND p.price BETWEEN '" . $price_min . "' AND '" . $price_max . "'
        AND p.product_id != '" . $product_id . "'
        AND p2c.category_id = '" . $category_id . "'";

        $sql .= " LIMIT 20";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getCategories($product_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
        return $query->rows;
    }

    public function getProductsCategories($products_id)
    {
        $sql = "SELECT *, count( ptc.category_id ) as count FROM " . DB_PREFIX . "product_to_category  ptc
            INNER JOIN " . DB_PREFIX . "category c ON (ptc.category_id = c.category_id)
            INNER JOIN " . DB_PREFIX . "category_description cd ON (ptc.category_id = cd.category_id)
            WHERE ptc.product_id IN( '" . implode('\',\'', $products_id) . "') GROUP BY ptc.category_id";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getCollectionProducts($collection_id, $product_id = 0, $limit = 4)
    {
        /*  $data = apc_fetch('graua_product_collection_' . $collection_id . '_' . $product_id . '_' . $limit, $has);
         if($has)
           return $data;
 */

        if (!$collection_id && $product_id) {
            $prod_col = $this->db->query("SELECT collection_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id)->row;
            $collection_id = $prod_col['collection_id'];
        }

        $sql = "SELECT DISTINCT " . $this->gueryAddSelectList() . " FROM " . DB_PREFIX . "product p 
                INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                INNER JOIN " . DB_PREFIX . "product_to_param_value p2pv ON (p.product_id = p2pv.product_id)
                INNER  JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)
                INNER  JOIN " . DB_PREFIX . "category_to_store c2s ON (c2s.category_id = p2c.category_id) 
                LEFT  JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id)
                    
                WHERE p.date_available <= '" . date('Y-m-d H:i') . "' AND p.status = '1'
                AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
                AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
                AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                AND p.collection_id = '" . (int)$collection_id . "'  
                AND p.product_id !='" . (int)$product_id . "' ";

        $sql .= 'LIMIT ' . $limit;
        $query = $this->db->query($sql);

        //    apc_store('graua_product_collection_' . $collection_id . '_' . $product_id . '_' . $limit, $query->rows, 60 * 60 * 24);
        return $query->rows;
    }

    //Название коллекции
    public function getCollectionName($collection_id)
    {
        /* $data = apc_fetch('graua_collection_name_' . $collection_id, $has);
        if($has)
            return $data;
		*/
        $name = '';
        if ($collection_id) {
            $sql = "SELECT name FROM " . DB_PREFIX . "collections 
                WHERE collection_id = " . (int)$collection_id;

            $query = $this->db->query($sql)->row;
            $name = isset($query['name']) ? $query['name'] : '';
        }

        //    apc_store('graua_collection_name_' . $collection_id, $name, 60 * 60 * 24);
        return $name;
    }

    //Название и id коллекции
    public function getCollectionIdName($product_id)
    {
        /*    $data = apc_fetch('graua_collection_id_name_' . $product_id, $has);
           if($has)
               return $data;*/

        $prod_col = $this->db->query("SELECT collection_id FROM " . DB_PREFIX . "product WHERE product_id = " . (int)$product_id)->row;
        $data = array();
        if (isset($prod_col['collection_id'])) {
            $sql = "SELECT * FROM " . DB_PREFIX . "collections 
                    WHERE collection_id = " . (int)$prod_col['collection_id'];
            $data = $this->db->query($sql)->row;
        }

        //   apc_store('graua_collection_id_name_' . $product_id, $data, 60 * 60 * 24);
        return $data;
    }

// ******************************* PRODUCT NAVIGATION *************************//
    // PRODUCT PATH
    public function getProductPath($product_id)
    {
        $query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' LIMIT 1");

        if (!$query->row)
            return array();

        $category_array = $this->getProductPathCategory($query->row['category_id']);
        return array_reverse($category_array);
    }

    public function getProductPathCategory($category_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c 
            LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) 
                WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1");
        $result = $query->row;
        $path_array[] = array(
            'category_id' => $result['category_id'],
            'name' => $result['name'],
        );
        if ($result['parent_id'])
            return array_merge($path_array, $this->getProductPathCategory($result['parent_id']));
        else
            return $path_array;
    }

    // NEXT and PREVIOUS PRODUCTS
    public function getProductNavigation($category_id, $product_id)
    {
        $product_data = $this->cache->get('product.nav.' . $category_id . '.' . (int)$this->config->get('config_store_id'));

        if (!$product_data) {
            $product_data = array();

            $sql = "SELECT p.product_id
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
                WHERE p.status = '1' AND p.date_available <= '" . date('Y-m-d H:i') . "' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2c.category_id = '" . (int)$category_id . "'
                ORDER BY p.sort_order";

            $query = $this->db->query($sql);
            $query = $query->rows;
            foreach ($query as $key => $result) {
                $product_data[$result['product_id']] = array(
                    'prev' => isset($query[$key - 1]) ? $query[$key - 1]['product_id'] : '',
                    'next' => isset($query[$key + 1]) ? $query[$key + 1]['product_id'] : '',
                    'pos' => $key + 1
                );
            }

            $this->cache->set('product.nav.' . $category_id . '.' . (int)$this->config->get('config_store_id'), $product_data);
        }
        return $product_data;
    }

// ******************************* GENERAL ************************************//

    private function gueryAddSortAndLimit()
    {
        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'price';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'asc';
        $limit = isset($this->request->get['limit']) ? $this->request->get['limit'] : $this->config->get('config_catalog_limit');
        $start = ($page - 1) * $limit;

        $sort_data = array(
            'name' => 'LCASE(pd.name)',
            'special' => 'special',
            'price' => 'p.price',
            'date' => 'p.date_added'
        );

        $sql = '';

        if (isset($sort_data[$sort]))
            $sql .= " ORDER BY p.status desc, ss.stock_status_id asc, " . $sort_data[$sort];
        else
            $sql .= " ORDER BY p.status desc, ss.stock_status_id asc, " . current($sort_data);

        if ($order == 'desc')
            $sql .= " desc";
        else
            $sql .= " asc";


        if ($start < 0)
            $start = 0;

        $sql .= " LIMIT " . (int)$start . "," . (int)$limit;
        return $sql;
    }

    private function gueryFilterPrice()
    {
        $sql = '';
        if (isset($this->session->data['price_from']))
            $sql .= " AND p.price >= " . (int)$this->session->data['price_from'];
        if (isset($this->session->data['price_to']))
            $sql .= " AND p.price <= " . (int)$this->session->data['price_to'];

        return $sql;
    }

    private function gueryAddSelectList()
    {
        return "p.product_id,
                p.model,
                p.model_name,
                pd.name,
                pd.short_description,
                p.is_legacy,
                p.is_waiting,
                p.image,
                p.price,
                p.old_price,
                p.is_new,
                p.status,
                p.stock_status_id,
                ss.name as stock_status,
                p.tax_class_id,
                cd.name as category ";
    }

    private function gueryProductList($select_total = false)
    {
        if ($select_total)
            $select = 'count(DISTINCT p.product_id) as total';
        else
            $select = $this->gueryAddSelectList();

        $sql = "SELECT " . $select . "
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                LEFT  JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)
                LEFT  JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id)
                LEFT  JOIN " . DB_PREFIX . "stock_status ss ON (ss.stock_status_id = p.stock_status_id)
                WHERE p.date_available <= '" . date('Y-m-d H:i') . "' AND p.status = '1'
                    AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        return $sql;
    }

// ******************************* BRAND ***********************************//
    // BRAND QUERY
    private function getBrandQuery($brand_id, $select_total = false)
    {
        if ($select_total)
            $select = 'count(p.product_id) as total';
        else
            $select = "p.product_id,
                p.model_name,
                pd.name,
                pd.short_description,
                p.is_legacy,
                p.is_waiting,
                p.image,
                p.price,
                ss.name as stock_status,
                p.tax_class_id,
                (SELECT ps2.price FROM " . DB_PREFIX . "product_special ps2 WHERE p.product_id = ps2.product_id AND ((ps2.date_start = '0000-00-00' OR ps2.date_start < '" . date('Y-m-d H:i') . "') AND (ps2.date_end = '0000-00-00' OR ps2.date_end > '" . date('Y-m-d H:i') . "')) ORDER BY ps2.priority ASC, ps2.price ASC LIMIT 1) AS special";

        $sql = "SELECT " . $select . "
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                LEFT  JOIN " . DB_PREFIX . "product_to_param_value p2pv ON (p2pv.product_id = p.product_id)
                LEFT  JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)";

        $sql .= " WHERE p.date_available <= '" . date('Y-m-d H:i') . "' AND p.status = '1'
                    AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                    AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
                    AND p2pv.param_value_id= '" . $this->config->get('config_brand_param_id') . "_" . $this->db->escape($brand_id) . "'";

        $sql .= $this->gueryFilterPrice();

        return $sql;
    }

    // BRAND PRODUCTS
    public function getBrandProducts($brand_id)
    {
        if (!$brand_id)
            return array();

        $sql = $this->getBrandQuery($brand_id);
        $sql .= $this->gueryAddSortAndLimit();

        $query = $this->db->query($sql);
        return $query->rows;
    }

    // BRAND PRODUCTS TOTAL
    public function getBrandProductsTotal($brand_id)
    {
        if (!$brand_id)
            return 0;

        $sql = $this->getBrandQuery($brand_id, true);

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->row['total'] : 0;
    }

// ******************************* CATEGORY ***********************************//
    // CATEGORY QUERY
    private function getCategoryQuery($category_id, $params = array(), $params_count = 0, $select_total = false)
    {
        if ($select_total)
            $select = 'count(p.product_id) as total';
        else
            $select = $this->gueryAddSelectList();

        $sql = "SELECT " . $select . " 
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                INNER  JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)
                LEFT  JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)
                LEFT  JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id)
                WHERE p.status = 1
                    AND p.date_available <=  '" . date('Y-m-d H:i') . "'  
                    AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                    AND p2c.category_id = " . (int)$category_id;

        if ($params) {
            $sql .= " AND (SELECT count(DISTINCT prv.param_id) FROM product_to_param_value p2pv
                      INNER JOIN " . DB_PREFIX . "param_value prv ON prv.param_value_id = p2pv.param_value_id
                      WHERE p2pv.product_id = p.product_id AND p2pv.param_value_id IN('" . implode('\',\'', $params) . "')";
            $sql .= " ) = " . $params_count;
        }

        $sql .= $this->gueryFilterPrice();


        return $sql;
    }

    // CATEGORY PRODUCTS
    public function getCategoryProducts($category_id, $params = array(), $params_count = 0)
    {
        if (!$category_id)
            return array();

        $sql = $this->getCategoryQuery($category_id, $params, $params_count, false);

        $sql .= $this->gueryAddSortAndLimit();

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getMinMaxProductsBudget()
    {

        $category_ids = (isset($this->request->get['path']) ? (explode('_', $this->request->get['path'])) : null);

        $sql_cat = ($category_ids != null) ? (" AND p2c.category_id IN (" . implode(',', $category_ids) . ")") : "";

        $sql = "SELECT MIN(p.price) AS min, MAX(p.price) AS max  
                FROM " . DB_PREFIX . "product p
                INNER  JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)
                WHERE p.date_available <=  '" . date('Y-m-d H:i') . "' " . $sql_cat . " AND p.status = 1";

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->row : 0;
    }

    // CATEGORY PRODUCTS TOTAL
    public function getCategoryProductsTotal($category_id, $params = array(), $params_count = 0)
    {
        if (!$category_id)
            return 0;

        $sql = $this->getCategoryQuery($category_id, $params, $params_count, true);

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->row['total'] : 0;
    }

    public function getCategoryParamProductsTotal($pv, $category_id = 0)
    {
        if (!$pv)
            return 0;

        $sql = "SELECT p.product_id 
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                INNER JOIN " . DB_PREFIX . "product_to_param_value p2pv ON (p.product_id = p2pv.product_id)
                INNER  JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)
                LEFT  JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)
                LEFT  JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id)
                WHERE p.date_available <=  '" . date('Y-m-d H:i') . "'  
                    AND p.status = 1 
                    AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                    AND p2pv.param_value_id = '" . $this->db->escape($pv) . "'";
        if ($category_id)
            $sql .= " AND p2c.category_id IN(" . $this->db->escape($category_id) . ")";

        $sql .= $this->gueryFilterPrice();

        $sql .= " GROUP BY p.product_id";

        $query = $this->db->query($sql);

        return $query->num_rows;
    }

    public function getCategoryParamProducts($pv, $category_id = 0)
    {
        if (!$pv)
            return 0;

        $select = "p.product_id,
                p.model,
                p.model_name,
                pd.name,
                pd.short_description,
                p.is_legacy,
                p.is_waiting,
                p.image,
                p.price,
                p.old_price,
                p.is_new,
                p.status,
                ss.name as stock_status,
                p.tax_class_id,
                p.model_name ";

        $sql = "SELECT DISTINCT " . $select . " 
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                INNER JOIN " . DB_PREFIX . "product_to_param_value p2pv ON (p.product_id = p2pv.product_id)
                INNER  JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)
                LEFT  JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)
                LEFT  JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id)
                WHERE p.date_available <=  '" . date('Y-m-d H:i') . "'  
                    AND p.status = 1 
                    AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                    AND p2pv.param_value_id = '" . $this->db->escape($pv) . "'";
        if ($category_id)
            $sql .= " AND p2c.category_id IN( " . $this->db->escape($category_id) . ")";

        $sql .= $this->gueryFilterPrice();

        $sql .= $this->gueryAddSortAndLimit();

        $query = $this->db->query($sql);

        return $query->num_rows ? $query->rows : 0;
    }

    //CATEGORY PARAMS
    public function getParamsByCategory($category_id)
    {
        $sql = "SELECT DISTINCT pr.param_id, pr.param_id as param_alias, pr.name as param_name, prv.param_value_id, prv.value, prv.alias
            FROM " . DB_PREFIX . "product_to_param_value p2pv
            INNER JOIN  " . DB_PREFIX . "param_to_category pr2c ON pr2c.param_id = p2pv.param_id AND pr2c.category_id = " . $category_id . "
            INNER JOIN  " . DB_PREFIX . "param_value prv ON prv.param_value_id = p2pv.param_value_id
            INNER JOIN  " . DB_PREFIX . "product p ON p.product_id = p2pv.product_id
            INNER JOIN  " . DB_PREFIX . "param pr ON pr.param_id = prv.param_id
            INNER JOIN  " . DB_PREFIX . "product_to_category p2c ON p2c.product_id = p.product_id
            WHERE p.status = 1 AND pr.is_filter = 1 AND p.date_available <=  '" . date('Y-m-d H:i') . "'  AND p2c.category_id = " . $category_id;

        $sql .= $this->gueryFilterPrice();

        $sql .= " ORDER BY pr2c.sort_order_filter, prv.value";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    //SELECTED CATEGORY PARAMS
    public function getSelectedParamsByCategory($category_id = 0, $params = array(), $params_count = 0, $current_param = false)
    {
        $sql = "SELECT DISTINCT pr.param_id as param_alias, prv.alias
            FROM " . DB_PREFIX . "product_to_param_value p2pv
            INNER JOIN  " . DB_PREFIX . "product p ON p.product_id = p2pv.product_id
            INNER JOIN  " . DB_PREFIX . "param_value prv ON prv.param_value_id = p2pv.param_value_id
            INNER JOIN  " . DB_PREFIX . "param pr ON pr.param_id = prv.param_id
            INNER JOIN  " . DB_PREFIX . "product_to_category p2c ON p2c.product_id = p.product_id";

        $sql .= " WHERE p.status = '1' AND p.date_available <=  '" . date('Y-m-d H:i') . "'  AND p2c.category_id = '" . (int)$category_id . "'";

        if ($params) {
            $sql .= " AND (SELECT count(DISTINCT prv.param_id) FROM product_to_param_value p2pv
                      INNER JOIN " . DB_PREFIX . "param_value prv ON prv.param_value_id = p2pv.param_value_id
                      WHERE p2pv.product_id = p.product_id AND p2pv.param_value_id IN('" . implode('\',\'', $params) . "')";
            $sql .= " ) = " . $params_count;
        }

        if ($current_param)
            $sql .= " AND pr.param_id = '" . $current_param . "' ";

        $sql .= $this->gueryFilterPrice();

        $query = $this->db->query($sql);

        $result = array();
        foreach ($query->rows as $row) {
            $result[$row['param_alias']][] = $row['alias'];
        }
        return $result;
    }

// ******************************* SEARCH *************************************//
    // SEARCH QUERY
    private function getSearchQuery($keywords, $price_from = 0, $price_to = 0, $select_total = false)
    {
        $sql = $this->gueryProductList($select_total);

        if ($price_from || $price_to) {
            $sql .= " AND (p.price BETWEEN " . $price_from . " AND " . $price_to . ") ";
        }
        foreach (explode(' ', $keywords) as $keyword)
            $sql .= "  AND (LCASE(pd.name) LIKE LCASE('%" . $this->db->escape($keyword) . "%')
                        OR LCASE(pd.meta_description) LIKE LCASE('%" . $this->db->escape($keyword) . "%')
                        OR LCASE(cd.name) LIKE LCASE('%" . $this->db->escape($keyword) . "%') OR LCASE(p.model) LIKE LCASE('%" . $this->db->escape($keyword) . "%')) ";

        $sql .= $this->gueryFilterPrice();

        return $sql;
    }

    // SEARCH PRODUCTS
    public function getSearchProducts($keywords, $price_from = 0, $price_to = 0)
    {
//        if (!$keywords)
//            return array();

        $sql = $this->getSearchQuery($keywords, $price_from, $price_to);
        $sql .= ' GROUP BY p.product_id ';
        $sql .= $this->gueryAddSortAndLimit();

        $query = $this->db->query($sql);
        return $query->rows;
    }

    // SEARCH PRODUCTS TOTAL
    public function getSearchProductsTotal($keywords, $price_from = 0, $price_to = 0)
    {
//        if (!$keywords)
//            return 0;

        $sql = $this->getSearchQuery($keywords, $price_from, $price_to, true);

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->row['total'] : 0;
    }

    // AUTOSUGGEST
    public function getSearchSuggest($keywords, $price_from = 0, $price_to = 0, $limit = 10)
    {
        if (!$keywords)
            return array();

        $sql = $this->getSearchQuery($keywords, $price_from, $price_to);
        $sql .= ' GROUP BY p.product_id ';
        $sql .= " LIMIT " . $limit;

        $query = $this->db->query($sql);
        return $query->rows;
    }

// ******************************* SPECIAL *************************************//
    // SPECIAL QUERY
    private function getSpecialQuery($select_list = true)
    {
        if ($select_list)
            $select = $this->gueryAddSelectList();
        else
            $select = 'count(p.product_id) as total';

        $sql = "SELECT DISTINCT " . $select . "
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
                INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
                INNER  JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)
                LEFT  JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id)
                LEFT  JOIN " . DB_PREFIX . "stock_status ss ON (p.stock_status_id = ss.stock_status_id)
                WHERE p.date_available <=  '" . date('Y-m-d H:i') . "'  AND p.status = '1'
                    AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                    AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
                    AND p.old_price>0";

        return $sql;
    }

    // SPECIAL PRODUCTS
    public function getSpecialProducts()
    {

        $sql = $this->getSpecialQuery();
        $sql .= $this->gueryAddSortAndLimit();

        $query = $this->db->query($sql);
        return $query->rows;
    }

    // SPECIAL PRODUCTS TOTAL
    public function getSpecialProductsTotal()
    {

        $sql = $this->getSpecialQuery(false);
        //$sql .= $this->gueryAddSortAndLimit();

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->row['total'] : 0;
    }

    // SPECIAL BLOCK
    public function getSpecialModule($limit = 5, $category_id = 0)
    {

        $sql = $this->getSpecialQuery();
        if ($category_id) {
            $sql .= " AND p2c.category_id=" . $category_id;
        }
        $sql .= " LIMIT " . $limit;

        $query = $this->db->query($sql);
        return $query->rows;
    }

    // Special price for product
    public function getProductSpecial($product_id)
    {
        if ($this->customer->isLogged()) {
            $customer_group_id = $this->customer->getCustomerGroupId();
        } else {
            $customer_group_id = $this->config->get('config_customer_group_id');
        }
        $query = $this->db->query("SELECT price,old_price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start <  '" . date('Y-m-d H:i') . "' ) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
        if ($query->num_rows) {
            return $query->row;
        } else {
            return false;
        }
    }

// ******************************* BLOCKS *************************************//
    // Add to Viewed
    public function addToViewed($product_id)
    {
        if (isset($this->session->data['viewes'])) {
            $viewed = $this->session->data['viewes'];
            array_unshift($viewed, (int)$product_id);
            $viewed = array_unique($viewed);
            $viewed = array_slice($viewed, 0, (int)$this->config->get('viewed_limit'));
        } else
            $viewed = array((int)$product_id);

        $this->session->data['viewes'] = $viewed;
    }

    public function getViewedProducts()
    {
        if (!isset($this->session->data['viewes']) || !($this->session->data['viewes']))
            return array();

        $viewed = array_slice($this->session->data['viewes'], 0, $this->config->get('viewed_limit'));

        $sql = $this->gueryProductList() . "
                AND p.product_id IN (" . implode(',', $viewed) . ") " . "LIMIT " . (int)$this->config->get('viewed_limit');

        $query = $this->db->query($sql);
        $product_data = array();
        $tempo = array();
        foreach ($query->rows as $row)
            $tempo[$row['product_id']] = $row;

        foreach ($viewed as $product_id)
            if (isset($tempo[$product_id]))
                $product_data[] = $tempo[$product_id];

        return $product_data;
    }

// ******************************* PARAMS *************************************//

    public function getParams($product_id)
    {
        $results = array();
        $this->load->model('tool/image');
        $sql = "SELECT pr.name, prv.value, pg.name AS group_name
                FROM " . DB_PREFIX . "product_to_param_value p2prv
                INNER JOIN " . DB_PREFIX . "param pr ON pr.param_id = p2prv.param_id
                INNER JOIN " . DB_PREFIX . "param_value prv ON prv.param_value_id = p2prv.param_value_id
                LEFT JOIN " . DB_PREFIX . "param_group pg ON pr.param_group_id = pg.param_group_id
                WHERE p2prv.product_id = '" . (int)$product_id . "'";

        $results = $this->db->query($sql)->rows;

//        foreach ($query->rows as $row) {
//            if (isset($results[$row['group_name']][$row['param_id']])) {
//                $results[$row['group_name']][$row['param_id']]['value'] .= ', ' . $row['value'] . $row['prefix'];
//                $results[$row['group_name']][$row['param_id']]['image'] = $this->model_tool_image->resize($row['image'], 50, 50);
//            } else {
//                $results[$row['group_name']][$row['param_id']] = array(
//                    'name' => $row['name'],
//                    'image' => $this->model_tool_image->resize($row['image'], 50, 50),
//                    'value' => $row['value'] . $row['prefix']
//                );
//            }
//        }


        return $results;
    }

    public function getParamsShortDesc($product_id)
    {
//         APC
        /*  $has = false;
          apc_fetch('graua_product_short_desc_' . $product_id, $has);
          if ($has)
              return apc_fetch('graua_product_short_desc_' . $product_id);
          // .apc
          */

        $sql = "SELECT pr.param_id, pr.name, pr.prefix, IFNULL(prv.value, p2prv.value) as value
                FROM " . DB_PREFIX . "product_to_param_value p2prv
                INNER JOIN " . DB_PREFIX . "param pr ON pr.param_id = p2prv.param_id
                LEFT JOIN " . DB_PREFIX . "param_value prv ON prv.param_value_id = p2prv.param_value_id
                WHERE p2prv.product_id = '" . (int)$product_id . "' AND pr.is_sdesc = 1";
        //LEFT JOIN " . DB_PREFIX . "param_to_category pr2c ON pr2c.param_id = p2prv.param_id ORDER BY pr2c.sort_order_product
        $query = $this->db->query($sql);

        $results = array();
        foreach ($query->rows as $row)
            if (isset($results[$row['param_id']]))
                $results[$row['param_id']]['value'] .= ', ' . $row['value'] . $row['prefix'];
            else
                $results[$row['param_id']] = array(
                    'name' => $row['name'],
                    'value' => $row['value'] . $row['prefix']
                );

        // APC
        // apc_store('graua_product_short_desc_' . $product_id, $results, 60 * 60 * 24);
//
        return $results;
    }

    public function getParamsDesc($product_id, $category_id = 0)
    {
        // APC
        /*  $has = false;
          apc_fetch('graua_product_desc_' . $product_id, $has);
          if ($has)
              return apc_fetch('graua_product_desc_' . $product_id);
          */
        // .apc
        $this->load->model('tool/image');
        $sql = "SELECT pr.param_id, pr.name, pr.prefix, pv2i.image, pr.description, prg.name as group_name, IFNULL(prv.value, p2prv.value) as value
                FROM " . DB_PREFIX . "product_to_param_value p2prv
                INNER JOIN " . DB_PREFIX . "param pr ON pr.param_id = p2prv.param_id
                LEFT JOIN " . DB_PREFIX . "param_value prv ON prv.param_value_id = p2prv.param_value_id
                LEFT JOIN " . DB_PREFIX . "param_group prg ON prg.param_group_id = pr.param_group_id
                LEFT JOIN param_value_to_image pv2i ON pv2i.param_value_id = p2prv.param_value_id
                LEFT JOIN " . DB_PREFIX . "param_to_category pr2c ON (pr2c.param_id = pr.param_id AND pr2c.category_id = '" . (int)$category_id . "')
                WHERE p2prv.product_id = '" . (int)$product_id . "' AND pr.is_desc = 1
                ORDER BY pr2c.sort_order_product";

        $query = $this->db->query($sql);

        $results = array();
        foreach ($query->rows as $row) {
            if (isset($results[$row['group_name']][$row['param_id']])) {
                $results[$row['group_name']][$row['param_id']]['value'] .= ', ' . $row['value'] . $row['prefix'];
                $results[$row['group_name']][$row['param_id']]['image'] = $this->model_tool_image->resize($row['image'], 50, 50);
            } else {
                $results[$row['group_name']][$row['param_id']] = array(
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'image' => $this->model_tool_image->resize($row['image'], 50, 50),
                    'value' => $row['value'] . $row['prefix']
                );
            }
        }

        // APC
        // apc_store('graua_product_desc_' . $product_id, $results, 60 * 60 * 24);

        return $results;
    }

    public function getAllParamsDesc($category_id = 0)
    {
        // APC
        /*$has = false;
        apc_fetch('graua_product_desc_' . $product_id, $has);
        if ($has)
            return apc_fetch('graua_product_desc_' . $product_id);
		*/
        // .apc
        $this->load->model('tool/image');
        $sql = "SELECT pr.param_id, pr.name, pr.prefix, pr.description, prg.name as group_name 
                FROM " . DB_PREFIX . "param_to_category pr2c
                INNER JOIN " . DB_PREFIX . "param pr ON pr.param_id = pr2c.param_id
                LEFT JOIN " . DB_PREFIX . "param_group prg ON prg.param_group_id = pr.param_group_id
                WHERE  pr.is_desc = 1 AND pr2c.category_id = '" . (int)$category_id . "'
                ORDER BY pr2c.sort_order_product";

        $query = $this->db->query($sql);

        $results = array();
        foreach ($query->rows as $row) {
            if (isset($results[$row['group_name']][$row['param_id']])) {
                $results[$row['group_name']][$row['param_id']]['value'] .= ', ' . $row['value'] . $row['prefix'];
            } else {
                $results[$row['group_name']][$row['param_id']] = array(
                    'name' => $row['name'],
                    'description' => $row['description']
                );
            }
        }

        // APC
        //   apc_store('graua_product_desc_' . $product_id, $results, 60 * 60 * 24);

        return $results;
    }

    public function getParamSubProducts($model_name)
    {
        $sql = "SELECT " . $this->gueryAddSelectList() . "
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
            INNER  JOIN " . DB_PREFIX . "product_to_category p2c ON (p2c.product_id = p.product_id)
            LEFT  JOIN " . DB_PREFIX . "category_description cd ON (cd.category_id = p2c.category_id)
            WHERE p.date_available <= '" . date('Y-m-d H:i') . "' AND p.status = '1'
                AND p.model_name = '" . str_replace("'", "\'", $model_name) . "'
                AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
                AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getParamValuesByProductParam($products, $params)
    {
        $query = $this->db->query("SELECT p2pv.product_id, p2pv.param_id, p2pv.param_value_id, pv.value
                                    FROM product_to_param_value p2pv
                                    INNER JOIN param_value pv ON(pv.param_value_id=p2pv.param_value_id)
                                    WHERE p2pv.product_id IN(" . implode(',', $products) . ")
                                    AND p2pv.param_id IN('" . implode("','", $params) . "')");
        $results = array();
        foreach ($query->rows as $row) {
            $results[$row['product_id']][$row['param_id']] = $row['value'];
        }

        return $results;
    }

    public function getParamValue($param_value_id)
    {
        $query = $this->db->query("SELECT pv.value 
                                    FROM " . DB_PREFIX . "param_value pv
                                    
                                    WHERE pv.param_value_id = '" . $param_value_id . "'");

        return $query->num_rows ? $query->row['value'] : '';
    }

    public function getParamId($product_id)
    {
        $query = $this->db->query("SELECT * 
                                    FROM " . DB_PREFIX . "product_to_param_value p2pv
                                    
                                    WHERE p2pv.product_id = '" . $product_id . "' AND p2pv.param_id='" . $this->config->get('config_grouping_param_id') . "'");


        return $query->row ? $query->row['param_value_id'] : '';
    }

// ******************************* FORMATING **********************************//
    // Product list formating
    public function formatProductList($products, $url = '')
    {
        $product_list = array();
        $this->load->model('tool/image');
        $this->load->model('tool/seo_url');

        foreach ($products as $key => $result) {
//by Novikov - для экономии места исходники удалены!!! 10 Гигов не проверяем есть ли исходники, сайт работает на кэше !!
//            if ($result['image'] && file_exists(DIR_IMAGE . $result['image']))
            if ($result['image'])
                $image = $result['image'];
            else
                $image = 'no_image.jpg';

            $price = $result['price'];
            $old_price = $result['old_price'];

            $price = $this->currency->format($this->tax->calculate($price, $result['tax_class_id'], $this->config->get('config_tax')));
            $old_price = $this->currency->format($this->tax->calculate($old_price, $result['tax_class_id'], $this->config->get('config_tax')));

            $params = array();
            if (!$result['is_legacy'])
                $params = $this->getParamsShortDesc($result['product_id']);

            $product_list[$key] = array(
                'is_group' => false,
                'status' => isset($result['status']) ? $result['status'] : '1',
                //by Novikov 2018
                'model' => $result['model'],
                'product_id' => $result['product_id'],
                'is_waiting' => $result['is_waiting'],
                'is_new' => $result['is_new'],
                'name' => $result['name'],
                'manufacturer' => $result['manufacturer'],
                'stock_status' => (isset($result['stock_status'])) ? $result['stock_status'] : $result['ss.name'],
                'trim_name' => (strlen($result['name']) <= 40) ? $result['name'] : mb_substr($result['name'], 0, 40, 'UTF-8') . '...',
                'is_legacy' => $result['is_legacy'],
                'params' => $params,
                'short_description' => strip_tags(html_entity_decode($result['short_description'], ENT_QUOTES, 'UTF-8')),
                'thumb' => $this->model_tool_image->resize($image, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')),
                'image' => $this->model_tool_image->resize($image, 50, 50),
                'price' => $price,
                'old_price' => $result['old_price'] ? $old_price : 0,
                'add' => $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/symple_order&product=' . $result['product_id']),
                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product' . $url . '&product_id=' . $result['product_id'])
            );
            if (isset($result['is_group']) && $result['model_name']) {
                $product_list[$key]['is_group'] = true;
                $product_list[$key]['name'] = $result['model_name'];
                $product_list[$key]['min_price'] = $this->currency->format($this->tax->calculate($result['min_price'], $result['tax_class_id'], $this->config->get('config_tax')));
                $product_list[$key]['href'] = $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product' . $url . '&product_id=' . $result['product_id'] . '&mg=1');
            }
        }

        return $product_list;
    }

    // Product list formating
    public function formatProductModuleList($products, $url = '')
    {
        $product_list = array();
        $this->load->model('tool/image');
        $this->load->model('tool/seo_url');

        foreach ($products as $key => $result) {
//by Novikov - для экономии места исходники удалены!!! 10 Гигов не проверяем есть ли исходники, сайт работает на кэше !!
//            if ($result['image'] && file_exists(DIR_IMAGE . $result['image']))
            if ($result['image'])
                $image = $result['image'];
            else
                $image = 'no_image.jpg';

            $price = $result['price'];
            $old_price = $result['old_price'];

            $price = $this->currency->format($this->tax->calculate($price, $result['tax_class_id'], $this->config->get('config_tax')));
            $old_price = $this->currency->format($this->tax->calculate($old_price, $result['tax_class_id'], $this->config->get('config_tax')));

            $product_list[$key] = array(

                //by Novikov 2018
                'stock_status' => html_entity_decode($result['stock_status']),


                'name' => $result['name'],
                'product_id' => $result['product_id'],
                'trim_name' => (strlen($result['name']) <= 40) ? $result['name'] : mb_substr($result['name'], 0, 40, 'UTF-8') . '...',
                'thumb' => $this->model_tool_image->resize($image, 190, 110),
                'price' => $price,
                'is_new' => $result['is_new'],
                'old_price' => $result['old_price'] ? $old_price : 0,
                'add' => $this->model_tool_seo_url->rewrite(HTTPS_SERVER . 'index.php?route=checkout/symple_order&product=' . $result['product_id']),
                'href' => $this->model_tool_seo_url->rewrite(HTTP_SERVER . 'index.php?route=product/product' . $url . '&product_id=' . $result['product_id'])
            );
        }
        return $product_list;
    }

    public function formatSorts($url = '')
    {
        $sorts = array();

        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'price';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'asc';


        if (isset($this->request->get['page']))
            $url .= '&page=' . $this->request->get['page'];
        if (isset($this->request->get['limit']))
            $url .= '&limit=' . $this->request->get['limit'];


        $sort_items[] = array('sort' => 'name', 'order' => 'asc');
        $sort_items[] = array('sort' => 'price', 'order' => 'asc');

        foreach ($sort_items as $item)
            $sorts[] = array(
                'text' => $this->language->get('text_' . $item['sort'] . '_' . $item['order']),
                'active' => $item['sort'] == $sort && $item['order'] == $order,
                'href' => $this->model_tool_seo_url->rewrite($url . '&sort=' . $item['sort'] . '&order=' . $item['order'])
            );

        return $sorts;
    }

    public function formatPagination($total, $url = '')
    {

        $page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

        if (isset($this->request->get['sort']))
            $url .= '&sort=' . $this->request->get['sort'];
        if (isset($this->request->get['order']))
            $url .= '&order=' . $this->request->get['order'];
        if (isset($this->request->get['limit']))
            $url .= '&limit=' . $this->request->get['limit'];
        if (isset($this->request->get['limit']))
            $limit = $this->request->get['limit'];
        else
            $limit = $this->config->get('config_catalog_limit');

        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->text = $this->language->get('text_pagination');
        $pagination->url = $this->model_tool_seo_url->rewrite($url . '&page={page}');

        return $pagination->render();
    }

}

?>
