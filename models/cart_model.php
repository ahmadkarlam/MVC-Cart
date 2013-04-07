<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Cart_model extends CI_Model
{
    function ambil_barang()
    {
        $query = $this->db->get('tb_barang');
        return $query->result_array();
    }
    
    function validate_add_cart_item()
    {
        $id = $this->input->post('id_barang');
        $cty = $this->input->post('quantity');
        $this->db->where('id_barang', $id);
        $query = $this->db->get('tb_barang', 1);
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row )
            {
            $data = array(
                        "id" => $id,
                        "qty" => $cty,
                        "price" => $row->harga_barang,
						
                        "name" => $row->nama_barang
                        );
            $this->cart->insert($data);
            return TRUE;
            }
        }
        else
        {
            return FALSE;
        }
    }
    
    function pageBarang($perPage, $uri)
    {
        $this->db->select('*');
        $this->db->from('tb_barang');
        $this->db->order_by('id_barang', 'DESC');
        $barangPage = $this->db->get('', $perPage, $uri);
        if ($barangPage->num_rows() > 0)
        {
            return $barangPage->result_array();
        }
        else
        {
            return null;
        }
    }
	
	function merk($perPage, $uri)
    {
		$id_merk = $_SESSION['id_merk'];
        $this->db->select('*');
        $this->db->from('tb_barang');
		$this->db->where('id_merk', $id_merk);
        $barangPage = $this->db->get('', $perPage, $uri);
        if ($barangPage->num_rows() > 0)
        {
            return $barangPage->result_array();
        }
        else
        {
            return null;
        }
    }
    
    function input_product()
    {
		$type = end(explode(".", $_FILES["userfile"]["name"]));
		$config = array(
				'file_name'=>'photobarang'.strtolower($_SESSION['merk']).'_' . substr(md5(time()), 0, 16).".".$type
			);
		$id_merk = $_SESSION['id_merk'];
		$nama = $this->input->post('nama_product');
		$harga = $this->input->post('harga');
		$photo = $config['file_name'];
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		// input database 
        $data = array(
			'nama_barang'=>$nama,
			'harga_barang'=>$harga,
			'id_merk'=>$id_merk,
			'photo'=>$photo
		);
		$this->db->insert('tb_barang', $data);
    }
	
	function delete_product($id)
	{
			$this->db->where('id_barang', $id);
			$this->db->delete('tb_barang');   
	}
	
	function edit_product($id)
	{
        $query = $this->db->query("SELECT * FROM tb_barang where id_barang='$id'");	   
    	foreach($query->result_array() as $row)
        {
            if ($_FILES['userfile']['name'] == '')
            {
                $photo=$row['photo'];
            }
            else
            {
                $type = end(explode(".", $_FILES["userfile"]["name"]));
                $photo='photobarang'.strtolower($_SESSION['merk']).'_' . substr(md5(time()), 0, 16).".".$type;
            }
            $data=array(
                'nama_barang'=>$this->input->post('nama_barang'),
                'harga_barang'=>$this->input->post('harga'),
                'photo'=>$photo
            );
            $this->db->where('id_barang', $id);
            $this->db->update('tb_barang', $data);
        }
	}
	
	function cek_keranjang($user, $date)
	{
		$q = $this->db->query("SELECT * FROM tb_cart where date='$date' and user='$user'");
	}
	
	function add_cart()
	{
		$data = array(
			'qty'=>$this->input->post('qty'),
			'nama_barang'=>$this->input->post('nama_barang'),
			'harga_barang'=>$this->input->post('harga_barang'),
			'merk_id'=>$this->input->post('merk_id'),
			'sub_total'=>$this->input->post('qty')*$this->input->post('harga_barang'),
			'user'=>$_SESSION['user'],
			'date'=>gmdate('d-M-Y', time()+3600*+7),
		);
		$this->db->insert('tb_cart', $data);
	}
	
	function empty_cart($user, $date)
	{
		$this->db->where('user', $user);
		$this->db->where('date', $date);
		$this->db->delete('tb_cart');
	}
	
	function delete_cart($user, $date, $id)
	{
		$this->db->where('id_cart', $id);
		$this->db->where('user', $user);
		$this->db->where('date', $date);
		$this->db->delete('tb_cart');
	}
	
	function update_cart($user, $date, $id)
	{
		$data = array(
			'qty'=>$this->input->post('qty_e'),
			'sub_total'=>$this->input->post('qty_e')*$this->input->post('harga_barang_e')
		);
		$this->db->where('id_cart', $id);
		$this->db->where('user', $user);
		$this->db->where('date', $date);
		$this->db->update('tb_cart', $data);
	}
	
}
/* End of file cart_model.php */
/* Location: ./app/model/cart_model.php */