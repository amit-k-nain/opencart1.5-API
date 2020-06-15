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

        return 'error';
    }

    public function multipleImageUpload($name,$images) {

        $this->db->query("INSERT INTO `" . DB_PREFIX . "multi_image` SET `name` = '" . $name . "', `images` = '" . $images . "', `date` = NOW()");

        return $this->db->getLastId();
    }
}
