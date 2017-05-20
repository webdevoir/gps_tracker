<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('settings/contact_model');
        $this->load->model('settings/category_model');
        $this->load->helper('my_table_helper');
    }
        
    public function users()
    {

        $this->data['records'] = $this->contact_model->get_users();
        if (count($this->data['records']) == 0)
        {
            $this->data['message'] = 'No records.';
        }
        $this->send_messages();

        $this->load->view('settings/contact/users', $this->data);
    }
    
    public function get_user($id)
    {
        $this->data['categories']  = $this->category_model->get_categories();
        $this->data['records'] = $this->contact_model->get_user($id);
        $this->data['photo']   = "web\pics\users\\".$this->data['records']['email'].".jpg";
        $this->load->view('settings/contact/get_user', $this->data);
    }
    
    public function modify_contact_settings()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('status', 'Status', 'required');
            
            if ($this->form_validation->run())
            {
                if ($this->contact_model->modify_contact_settings())
                {
                    $this->data['message'] = 'Form modification has been successfully saved. ';
                }
                else
                {
                    $this->data['message'] = 'Something went wrong. '
                                           . 'The username or email address may already exist. '
                                           . 'Please try another username and/or email address. ';
                }
            }
        }
        $this->send_messages();
    }
    
    public function remove_user()
    {
        if ($this->contact_model->remove_user())
        {
            $this->data['message'] = 'Contact has been successfully removed. ';
        }
        else
        {
            $this->data['message'] = 'Something went wrong. Please try again.';
        }
        $this->send_messages();
    }
}