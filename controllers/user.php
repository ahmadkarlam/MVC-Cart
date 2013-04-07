<?php
if (!defined('BASEPATH'))exit('No direct script access allowed');
class User extends CI_Controller
{
	function __construct()
	{
		@session_start();
		parent::__construct();
		if (isset ($_SESSION['user']))
        {
			$user = $_SESSION['user'];
			$query = $this->db->query("SELECT * FROM tb_member where user='$user'");
			foreach($query->result_array() as $r)
			{
				if ($r['id_level']!=3)
				{
            		redirect('index.php/home');
				}
				else
				{
				    $_SESSION['id_level']=$r['id_level'];
				    $_SESSION['user']= $r['user'];
                    $query_data = $this->db->query("SELECT * FROM tb_data where user='$_SESSION[user]'");
                    foreach($query_data->result_array() as $row_data)
                    {
                        $_SESSION['id_data']=$row_data['id_data'];
                    }
					return TRUE;
				}
			}
        }
	}
	
	function index()
	{
		$data['title']="Home";
        $this->load->view('user/header' ,$data);
        $this->load->view('user/home' ,$data);
        $this->load->view('user/footer' ,$data);
	}
    
    function panduan()
    {
        $data['title']="Panduan";
        $this->load->view('user/header' ,$data);
        $this->load->view('home/panduan' ,$data);
        $this->load->view('user/footer' ,$data);
    }
    
    function kontak_admin()
    {
        $data['title']="Contact Admin";
        $this->load->view('user/header' ,$data);
        $this->load->view('operator/kontak_admin' ,$data);
        $this->load->view('user/footer' ,$data);
    }
    
    function profile()
    {
        $id_data = $_SESSION['id_data'];
        $query = $this->db->query("SELECT * FROM tb_data where id_data='$id_data'");
        $data['detail']=$query->result_array();
        $data['title']='Profile';
        $this->load->view('user/header' ,$data);
        $this->load->view('user/profile' ,$data);
        $this->load->view('user/footer' ,$data);
    }
    
    function edit_profile()
    {
        $query = $this->db->query("SELECT * FROM tb_data where id_data='$_SESSION[id_data]'");
        if ($this->input->post('edit'))
        {
            $this->load->model('data_model');
            foreach($query->result_array() as $row)
            {
                $id = $_SESSION['id_data'];
                chmod('./webroot/img/user/', 0777);
                if ($_FILES['userfile']['name']=='')
                {
                    $this->data_model->edit_profile($id);
                    redirect('index.php/user/profile');
                }
                else
                {
                    $config = array(
				        'allowed_types' => 'jpg|jpeg|gif|png',
				        'upload_path' => './webroot/img/user/',
				        'max_size' => 2000,
				        'file_name'=>md5('userphoto'.strtolower($_SESSION['user'])).'_' . substr(md5(time()), 0, 16)
			         );
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if(!$this->upload->do_upload())
                    {
                        $data['error'] = $this->upload->display_errors();
                    }
                    else
                    {
                        if ($row['photo']!="default.png")
                        {
                            @unlink('./webroot/img/user/'.$row['photo']);
                        }
                        $this->data_model->edit_profile($id);
                        redirect('index.php/user/profile');
                    }
                }
            }
        }
        $data['edit']=$query->result_array();
        $data['error']='';
        $data['title']='Edit Profile';
        $this->load->view('user/header' ,$data);
        $this->load->view('user/edit_profile' ,$data);
        $this->load->view('user/footer' ,$data);
    }
    
    function shopping()
    {
    	$this->load->model('cart_model');
		$this->load->model('check_model');
    	$user = $_SESSION['user'];
    	$date = gmdate('d-M-Y', time()+3600*+7);
    	$query = $this->db->query("SELECT * FROM tb_barang");
        $jumRow = $query->num_rows();
        $config['base_url'] = base_url()."index.php/user/shopping/";
        $config['total_rows'] = $jumRow;
        $config['per_page'] = 7;
        $config['uri_segment']=3;
        $this->pagination->initialize($config);
        $data['paging']= $this->pagination->create_links();
        $data['tampil']=$this->cart_model->pageBarang($config['per_page'], $this->uri->segment(3));
    	$data['title']='Shopping';
    	$this->load->view('admin/rupiah', $data);
    	$this->load->view('user/header' ,$data);
        $this->load->view('home/tires' ,$data);
        $this->load->view('user/footer' ,$data);
        if($this->input->post('add_to_cart'))
        {
        	$this->cart_model->add_cart();
        	redirect('index.php/user/shopping');
        }
        if($this->input->post('empty'))
        {
        	$this->cart_model->empty_cart($user, $date);
        	redirect('index.php/user/shopping');
        }
        if($this->input->post('edit'))
        {
        	if($this->input->post('qty_e')==0)
        	{
        		$this->cart_model->delete_cart($user, $date, $this->input->post('id_cart'));
        		redirect('index.php/user/shopping');
        	}
        	else
        	{
        		$this->cart_model->update_cart($user, $date, $this->input->post('id_cart'));
        		redirect('index.php/user/shopping');
        	}
        }
		if($this->input->post('check'))
		{
			redirect('index.php/user/checkout/'.md5($date.$_SESSION['user']));
		}
    }
    
	function checkout($dateuser)
	{
		$date = gmdate('d-M-Y', time()+3600*+7);
		$user = $_SESSION['user'];
		$data['query'] = $this->db->query("SELECT * FROM tb_cart where date='$date' and user='$user'");
		$data['sum'] = $this->db->query("SELECT sub_total, SUM(sub_total) as total FROM tb_cart where date='$date' and user='$user'");
		$data['compare'] = $dateuser;
		$data['title']='CheckOut';
		$this->load->view('admin/rupiah', $data);
        $this->load->view('user/header' ,$data);
        $this->load->view('user/checkout' ,$data);
        $this->load->view('user/footer' ,$data);
	}
	
   	function logout()
	{
		$_SESSION=array();
		@session_destroy();
		redirect('index.php/home');
	}
	
}
/* End of file user.php */
/* Location: ./app/controller/user.php */