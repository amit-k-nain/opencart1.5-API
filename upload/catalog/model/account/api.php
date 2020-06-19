<?php
class ModelAccountApi extends Model {
	public function login($username, $key) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE `username` = '" . $this->db->escape($username) . "' AND `key` = '" . $this->db->escape($key) . "' AND `status` = '1'");

		return $query->row;
	}

	public function addApiSession($api_id, $session_id, $ip) {

		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_session` SET `api_id` = '" . (int)$api_id . "', `session_id` = '" . $this->db->escape($session_id) . "', `ip` = '" . $this->db->escape($ip) . "', `date_added` = NOW(), `date_modified` = NOW()");

		return $this->db->getLastId();
	}

	public function getApiIps($api_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_ip` WHERE `api_id` = '" . (int)$api_id . "'");

		return $query->rows;
	}

    public function addApiUser($user, $key) {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE `status` = '1'");

        $res = sizeof($query->rows);

        $this->db->query("INSERT INTO `" . DB_PREFIX . "api` SET `username` = '" . $user . "', `key` = '" . $key . "', `api_id` = '" . (int)($res+(int)1) . "', `status` = '" . (int)1 . "', `date_added` = NOW(), `date_modified` = NOW()");

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE `status` = '1'");

        $resLast = sizeof($query->rows);

        if($resLast > $res) {
            return $resLast;
        }

        return 'database error';
    }

    public function getCards($type) {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "card` WHERE `type` = '" . $this->db->escape($type) . "' AND `status` = '1'");

        if ($query->rows) {
            return $query->rows;
        }
        return 'database error';
    }

    public function getCard($card_id) {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "card` WHERE `id` = '" . $this->db->escape($card_id) . "' AND `status` = '1'");

        if ($query->rows) {
            return $query->rows;
        }
        return 'database error';
    }

    public function updateCard($card_id,$data,$image = null) {

        $name = $data['name'];
        $type = $data['type'];
        $description = $data['description'];
        $link = $data['link'];

        $query = $this->db->query("SELECT `status` FROM `" . DB_PREFIX . "card` WHERE `id` = '" . $card_id . "'");

        $result = $query->row;

        if($result['status'] == 1){
            if($image != null) {
                $this->db->query("UPDATE " . DB_PREFIX . "card SET images = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "', date_modified = NOW() WHERE id = '" . (int)$card_id . "'");
            }elseif ($name != null) {
                $this->db->query("UPDATE " . DB_PREFIX . "card SET name = '" . $this->db->escape($name) . "', date_modified = NOW() WHERE id = '" . (int)$card_id . "'");
            }elseif ($type != null) {
                $this->db->query("UPDATE " . DB_PREFIX . "card SET type = '" . $this->db->escape($type) . "', date_modified = NOW() WHERE id = '" . (int)$card_id . "'");
            }elseif ($description != null) {
                $this->db->query("UPDATE " . DB_PREFIX . "card SET description = '" . $this->db->escape($description) . "', date_modified = NOW() WHERE id = '" . (int)$card_id . "'");
            }elseif ($link != null) {
                $this->db->query("UPDATE " . DB_PREFIX . "card SET link = '" . $this->db->escape($link) . "', date_modified = NOW() WHERE id = '" . (int)$card_id . "'");
            }

            $query = $this->db->query("SELECT `type` FROM `" . DB_PREFIX . "card` WHERE `id` = '" . $card_id . "' AND `status` = '1'");

            $result = $query->row;

            if ($result){
                $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "card` WHERE `type` = '" . $this->db->escape($result['type']) . "' AND `status` = '1'");

                return $query->rows;
            }
        }else {
            return 'Error: Card not exist with this card id.!';
        }

    }

    public function removeCard($card_id) {

        $query = $this->db->query("SELECT `type` FROM `" . DB_PREFIX . "card` WHERE `id` = '" . $card_id . "' AND `status` = '1'");

        $result = $query->row;

        if ($result){
            $this->db->query("UPDATE " . DB_PREFIX . "card SET status ='" . 0 . "', date_modified = NOW() WHERE id = '" . (int)$card_id . "'");

            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "card` WHERE `type` = '" . $this->db->escape($result['type']) . "' AND `status` = '1'");

            return $query->rows;
        }
        return 'Error: No card found with this card id.!';
    }

    /*public function multipleImageUpload($name,$images) {

        $this->db->query("INSERT INTO `" . DB_PREFIX . "multi_image` SET `name` = '" . $name . "', `images` = '" . $images . "', `date` = NOW()");

        return $this->db->getLastId();
    }*/

    public function addCard($images,$data) {

        $name = $data['name'];
        $type = $data['type'];
        $description = $data['description'];
        $link = $data['link'];

        $this->db->query("INSERT INTO `" . DB_PREFIX . "card` SET `name` = '" . $this->db->escape($name) . "', `type` = '" . $this->db->escape($type) . "', `description` = '" . $this->db->escape($description) . "', `link` = '" . $this->db->escape($link) . "', `images` = '" . $this->db->escape(html_entity_decode($images, ENT_QUOTES, 'UTF-8')) . "', `status` = '" . 1 . "', `date_added` = NOW()");

        return $this->db->getLastId();
    }
}
