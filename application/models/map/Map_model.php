<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map_model extends CI_Model
{
        private $db;
    public function __construct()
    {
            parent::__construct();
            $this->db = $this->load->database('default', TRUE);
    }
    
    public function show_contacts_on_map()
    {
        $owner = $this->session->userdata('email');
        $select = 'user.id, '
                . 'first_name, '
                . 'last_name, '
                . 'email, '
                . 'phone_number, '
                . 'gps_update_time, '
                . 'latitude, '
                . 'longitude';
        $users = $this->db->select($select)
                          ->from('user')
                          ->join('contacts','contacts.owner = user.email')
                          ->join('category', 'category.owner = contacts.owner')
                          ->where('category.permission', 'enabled')
                          ->where('contacts.permission','enabled')
                          ->where('member', $owner)
                          ->where('longitude IS NOT NULL')          
                          ->order_by('email')
                          ->get()
                          ->result_array();
        $categories = $this->db->select('member')
                               ->from('contacts')
                               ->join('category', 'category.name = contacts.category')
                               ->where('contacts.owner', $owner)
                               ->where('category.owner', $owner)
                               ->where('contacts.status', 'visible')
                               ->where('category.status', 'visible')
                               ->order_by('member')
                               ->get()
                               ->result_array();
        $contacts = array();
        for ($i = 0; $i < count($users); $i++)
        {
            for ($j = 0; $j < count($categories); $j++)
            {
                if ($users[$i]['email'] == $categories[$j]['member'])
                {
                    $contacts[] = $users[$i];
                }
            }
        }
        $me = $this->db->where('email', $this->session->userdata('email'))
                       ->get('user')
                       ->result_array();
        $contacts[] = $me[0];
        return $contacts;
    }
}
