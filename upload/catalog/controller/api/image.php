<?php
// catalog/controller/api/card.php
class ControllerApiImage extends Controller {
    public function index() {
        $this->load->language('api/image');
        $json = array();
        $this->load->model('account/api');

        if (!isset($this->session->data['token']) && strlen($_REQUEST['token']) == 0) {
            $json['error']['warning'] = $this->language->get('error_permission');
        } else {

            $output = [];
            if(is_array($_FILES))
            {
                foreach ($_FILES['files']['name'] as $name => $value)
                {
                    $file_name = explode(".", $_FILES['files']['name'][$name]);
                    $allowed_ext = array("jpg", "jpeg", "png", "gif");
                    if(in_array($file_name[1], $allowed_ext))
                    {
                        $new_name = md5(rand()) . '.' . $file_name[1];
                        $sourcePath = $_FILES['files']['tmp_name'][$name];
                        $targetPath = DIR_IMAGE.$new_name;
                        if(move_uploaded_file($sourcePath, $targetPath))
                        {
                            $a = [$targetPath];
                            array_push($output,$a);
                        }
                    }
                }
                $res = $this->model_account_api->multipleImageUpload(json_encode($file_name),json_encode($output));
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

}