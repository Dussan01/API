<?php 
class User extends CI_Controller{

	
public function all()
	{
		if($this->access_app()){
			echo json_encode($this->db->get("usuario")->result());
		}
	}
	
	public function get($id = null){
		if($this->access_app()){
			if($id == null){
				$this->output->set_status_header(404, "No se encontro el recurso solicitado");
				echo json_encode(array("code" => 404, "message" => "No se encontro el recurso solicitado, hace falta definir un id, ejemplo: get/{id}"));
			}else{
				$this->load->database();
				$this->db->where("idusuario", $id);
				$query = $this->db->get("usuario");
				if($query->num_rows() == 0)
				{
					$this->output->set_status_header(404, "No se encontro el recurso solicitado");
					echo json_encode(array("code" => 404, "message" => "No se encontro el recurso solicitado, hace falta definir un id, ejemplo: get/{id}"));
				}else{
					echo json_encode($query->row());
				}
				
				
				
			}
		}	
	}
	
	public function add()
	{
		if($this->access_app()){
			$dataform  = $this->input->post();
			if(count($dataform) == 0)
			{
				$this->output->set_status_header(400, "Hay un error en la peticion");
				echo json_encode(array("code" => 400, "message" => "Hay un error en la peticion"));
			}else
			{
				$this->load->database();
				$this->db->insert("usuario", $dataform);
				$last_insert_id = $this->db->insert_id();
				$dataform["idusuario"] = $last_insert_id;
				echo json_encode($dataform);
			}
			
		}
	}
	public function update($id = null)
	{
		if($this->access_app()){
			if($id == null){
				$this->output->set_status_header(404, "No se actualizado el recurso solicitado");
				echo json_encode(array("code" => 404, "message" => "No se actualizado el recurso solicitado, hace falta definir un id, ejemplo: update/{id}"));
			}else{
				$dataform = $this->input->post();
				$this->load->database();
				$this->db->where('idusuario', $id);
				$this->db->update('usuario', $dataform);
				// obtener el usuario actualizado de la base de datos
				$this->db->where('idusuario', $id);
				$userupdate = $this->db->get('usuario')->row();
				echo json_encode($userupdate);
			}
		}	
	}
	public function delete($id = null)
	{
		if($this->access_app()){
			if($id == null){
				$this->output->set_status_header(404, "No se elimino el recurso solicitado");
				echo json_encode(array("code" => 404, "message" => "No se elimino el recurso solicitado, hace falta definir un id, ejemplo: update/{id}"));
			}else{
				$dataform = $this->input->post();
				$this->load->database();
				$this->db->where('idusuario', $id);
				$this->db->delete('usuario');
				// obtener el usuario actualizado de la base de datos
				$this->db->where('idusuario', $id);
				$hasdelete = $this->db->get('usuario')->num_rows() == 0;
				echo json_encode(array("code" => 200, "message" => $hasdelete ? "Se elimino el recurso": "no fue posible eliminar el usuario"));
			}
		}	
		
	}

	public function uploadfile($name){
		
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'jpg';
		$config['max_size']  = '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';
		$new_name = $name . time() . '.jpg';
		$config['file_name'] = $new_name;
		$this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload($name)){
			$error = array('error' => $this->upload->display_errors('',''));
			$this->output->set_status_header(400. "No fue posible subir el archivo")
			echo json_encode($error);
		}
		else{
			echo json_encode(array("path" => "/uploads/$new_name"));
		}
		
	}
	public function access_app()
	{
		$headers = $this->input->request_headers();
		$keyname = "X-API-key";
		if(array_key_exists($keyname, $headers)){
				$token = $headers[$keyname];
				$this->load->database();
				$this->db->where("token", $token);
				$query = $this->db->get("app"); 
				//num_rows regresa el numero de filas que hay en la consulta
				if($query->num_rows() > 0)
				{
					return true;
				}
				
		
			
		}
		$this->output->set_status_header(401, "No se ha definido el API-key");
		echo json_encode(array("code" => 401, "message" => "no se ha concedido el accesso a la app"));
		
		
		
		
		
		
	}
	
}