<?php

class ControllerApiCard extends Controller {

    public function add() {
        $this->load->language('api/card');
        $json = array();
        $this->load->model('account/api');

        if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {

            $output = $data = array();
            if(is_array($_FILES))
            {
                $data = array(
                    'name' => (isset($this->request->post['name']) && (strlen($this->request->post['name']) > 0)) ? $this->request->post['name'] : null,
                    'type' => (isset($this->request->post['type'])  && (strlen($this->request->post['type']) > 0)) ? $this->request->post['type'] : null,
                    'description' => (isset($this->request->post['description'])  && (strlen($this->request->post['description']) > 0)) ? $this->request->post['description'] : null,
                    'link' => (isset($this->request->post['link'])  && (strlen($this->request->post['link']) > 0)) ? $this->request->post['link'] : null,
                );

                $image = null;
                if(is_array($_FILES) && isset($_FILES['images']['name']))
                {
                    foreach ($_FILES['images']['name'] as $name => $value)
                    {
                        $file_name = explode(".", $_FILES['images']['name'][$name]);
                        $allowed_ext = array("bmp", "pdf", "doc", "ppt", "jpg", "jpeg", "png", "gif");
                        if(in_array($file_name[1], $allowed_ext))
                        {
                            $new_name = md5(rand()) . '.' . $file_name[1];
                            $sourcePath = $_FILES['images']['tmp_name'][$name];
                            $targetPath = DIR_IMAGE.$new_name;
                            if(move_uploaded_file($sourcePath, $targetPath))
                            {
                                //$a = [$new_name];
                                //array_push($output,$a);
                                $image = $new_name;
                            }
                        }
                    }
                }

                $video = null;
                if(is_array($_FILES) && isset($_FILES['videos']['name']))
                {
                    foreach ($_FILES['videos']['name'] as $name => $value)
                    {
                        $file_name = explode(".", $_FILES['videos']['name'][$name]);
                        $allowed_ext = array("mp4", "3gp", "flv", "mkv", "webm", "avi");
                        if(in_array($file_name[1], $allowed_ext))
                        {
                            $new_name = md5(rand()) . '.' . $file_name[1];
                            $sourcePath = $_FILES['videos']['tmp_name'][$name];
                            $targetPath = DIR_IMAGE.$new_name;
                            if(move_uploaded_file($sourcePath, $targetPath))
                            {
                                //$a = [$new_name];
                                //array_push($output,$a);
                                $image = $new_name;
                            }
                        }
                    }
                }

                $res = $this->model_account_api->addCard($image,$data);
                if($res) {
                    $json['response'] = 'Upload Successfully';
                }
            }else {
                $json['response'] = $this->language->get('error_upload');
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCards() {
        $this->load->language('api/card');
        $json = array();
        $this->load->model('account/api');

        if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            if (isset($this->request->post['type'])) {
                $res = $this->model_account_api->getCards($this->request->post['type']);
                if($res) {
                    $json['response'] = $res;
                }else {
                    $json['response'] = 'Error: Wrong card type entered!';
                }

            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCard() {
        $this->load->language('api/card');
        $json = array();
        $this->load->model('account/api');

        if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            if (isset($this->request->post['card_id'])) {
                $res = $this->model_account_api->getCard($this->request->post['card_id']);
                if($res) {
                    $json['response'] = $res;
                }else {
                    $json['response'] = 'Error: Wrong card id entered!';
                }

            }
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

	public function update() {
        $this->load->language('api/card');
        $json = array();
        $this->load->model('account/api');

        if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            if(isset($this->request->post['card_id'])){
                $card_id = $this->request->post['card_id'];
                $output = $data = array();
                $data = array(
                    'name' => (isset($this->request->post['name']) && (strlen($this->request->post['name']) > 0)) ? $this->request->post['name'] : null,
                    'type' => (isset($this->request->post['type'])  && (strlen($this->request->post['type']) > 0)) ? $this->request->post['type'] : null,
                    'description' => (isset($this->request->post['description'])  && (strlen($this->request->post['description']) > 0)) ? $this->request->post['description'] : null,
                    'link' => (isset($this->request->post['link'])  && (strlen($this->request->post['link']) > 0)) ? $this->request->post['link'] : null,
                );

                $image = null;
                if(is_array($_FILES) && isset($_FILES['images']['name']))
                {
                    foreach ($_FILES['images']['name'] as $name => $value)
                    {
                        $file_name = explode(".", $_FILES['images']['name'][$name]);
                        $allowed_ext = array("bmp", "pdf", "doc", "ppt", "jpg", "jpeg", "png", "gif");
                        if(in_array($file_name[1], $allowed_ext))
                        {
                            $new_name = md5(rand()) . '.' . $file_name[1];
                            $sourcePath = $_FILES['images']['tmp_name'][$name];
                            $targetPath = DIR_IMAGE.$new_name;
                            if(move_uploaded_file($sourcePath, $targetPath))
                            {
                                //$a = [$new_name];
                                //array_push($output,$a);
                                $image = $new_name;
                            }
                        }
                    }
                }

                $video = null;
                if(is_array($_FILES) && isset($_FILES['videos']['name']))
                {
                    foreach ($_FILES['videos']['name'] as $name => $value)
                    {
                        $file_name = explode(".", $_FILES['videos']['name'][$name]);
                        $allowed_ext = array("mp4", "3gp", "flv", "mkv", "webm", "avi");
                        if(in_array($file_name[1], $allowed_ext))
                        {
                            $new_name = md5(rand()) . '.' . $file_name[1];
                            $sourcePath = $_FILES['videos']['tmp_name'][$name];
                            $targetPath = DIR_IMAGE.$new_name;
                            if(move_uploaded_file($sourcePath, $targetPath))
                            {
                                //$a = [$new_name];
                                //array_push($output,$a);
                                $video = $new_name;
                            }
                        }
                    }
                }

                if($image){
                    $res = $this->model_account_api->updateCard($card_id,$data,$image);
                }else {
                    $res = $this->model_account_api->updateCard($card_id,$data);
                }

                if(is_array($res)) {
                    $json['response'] = 'Card Updated Successfully';
                    $json['data'] = $res;
                }else{
                    $json['response'] = $res;
                }
            }else {
                $json['response'] = $this->language->get('error_id');
            }
		}

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
	}

	public function remove() {
        $this->load->language('api/card');
        $json = array();
        $this->load->model('account/api');

        if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {
            if(isset($this->request->post['card_id'])){
                $card_id = $this->request->post['card_id'];
                $res = $this->model_account_api->removeCard($card_id);
                if(is_array($res)){
                    $json['data'] = $res;
                    $json['success'] = 'Card Deleted Successfully';
                }else{
                    $json['response'] = $res;
                }

		    }else {
                $json['response'] = $this->language->get('error_id');
            }
        }

        if (isset($this->request->server['HTTP_ORIGIN'])) {
            $this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
            $this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            $this->response->addHeader('Access-Control-Max-Age: 1000');
            $this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
	}

}
