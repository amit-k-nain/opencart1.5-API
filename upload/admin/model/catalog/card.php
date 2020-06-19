<?php
class ModelCatalogCard extends Model {
	public function addCard($data) {

		$sql = "INSERT INTO `" . DB_PREFIX . "card` SET `name` = '" . $this->db->escape($data['name']) . "', `type` = '" . $this->db->escape($data['type']) . "',";

		if (!empty($data['card_link'])) {
			$sql .= " `link` = '" . $this->db->escape($data['card_link']) . "',";
		}

		if (!empty($data['description'])) {
			$sql .= " `description` = '" . $this->db->escape($data['description']) . "',";
		}

		foreach ($data['card_description'] as $language_id => $value) {
			if ($value['description']) {
				$sql .= " `file` = '" . $this->db->escape($value['description']) . "',";
			}
		}

		foreach ($data['card_image'] as $value) {
				if ($value['image']) {
				$sql .= " `images` = '" . $this->db->escape(html_entity_decode($value['image'], ENT_QUOTES, 'UTF-8')) . "',";
			}
		}

		$sql .= " `status` = '" . 1 . "', `date_added` = NOW()";

		$this->db->query($sql);
	}

	public function editCard($card_id, $data) {

		$name = $data['name'];
        $type = $data['type'];
        $description = $data['description'];
        $link = $data['card_link'];

        $image = $file = null;

		foreach ($data['card_image'] as $value) {
			if ($value['image']) {
				$image = $value['image'];
			}
		}

		foreach ($data['card_description'] as $language_id => $value) {
			if ($value['description']) {
				$file = $value['description'];
			}
		}

		$sql = "UPDATE `" . DB_PREFIX . "card` SET `name` = '" . $this->db->escape($name) . "', `type` = '" . $this->db->escape($type) . "',";

		if ($link != null) {
			$sql .= " `link` = '" . $this->db->escape($link) . "',";
		}

		if ($description != null) {
			$sql .= " `description` = '" . $this->db->escape($description) . "',";
		}

		if ($file != null) {
			$sql .= " `file` = '" . $this->db->escape($file) . "',";
		}

		if ($image != null) {
			$sql .= " `images` = '" . $this->db->escape(html_entity_decode($image, ENT_QUOTES, 'UTF-8')) . "',";
		}

		$sql .= " `status` = '" . 1 . "', `date_modified` = NOW() WHERE id = '" . (int)$card_id . "'";

		$this->db->query($sql);

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "card`");

		return $query->rows;

	 }

	public function deleteCard($card_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "card SET status ='" . 0 . "', date_modified = NOW() WHERE id = '" . (int)$card_id . "'");
	}

	public function getCard($card_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "card` WHERE `id` = '" . $this->db->escape($card_id) . "'");

		return $query->row;
	}

	public function getCards($data = array()) {

		$sql = "SELECT * FROM `" . DB_PREFIX . "card` WHERE `id` <> ''";

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND type LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}

		$sort_data = array(
			'name',
			'type',
			'status',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}				

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCardImages($card_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "card WHERE id = '" . (int)$card_id . "'");

		return $query->rows;
	}
}
?>
