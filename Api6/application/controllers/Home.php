<?php
class Home extends CI_Controller{

    public function index(){
        $this->load->database();

        $data = $this->db->get("app");

        $json = json_encode($data->result());
        echo $json;
    }

    //POST
    public function adduser(){
        $post = $this->input->post();

        echo json_encode($post);
    }

}