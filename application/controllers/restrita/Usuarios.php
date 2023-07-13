<?php 

defined('BASEPATH') or exit('Ação não permitida');

class Usuarios extends CI_Controller{

    public $ion_auth;
    public $session;
    public $form_validation;
    public $input;
    public $core_model;

    public function __construct() {
        parent::__construct();
    }

    public function index(){
        
        $data = array(
            'titulo' => 'Usuários cadastrados',
            'styles' => array(
                'bundles/datatables/datatables.min.css',
                'datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css'
            ),
            'scripts' => array(
                'bundles/datatables/datatables.min.js',
                'bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js',
                'bundles/jquery-ui/jquery-ui.min.js',
                'js/page/datatables.js'
            ),
            'usuarios' => $this->ion_auth->users()->result()
        );

        // echo '<pre>';
        // print_r($data['usuarios']);
        // exit();

        $this->load->view('restrita/layout/header', $data);
        $this->load->view('restrita/usuarios/index');
        $this->load->view('restrita/layout/footer'); 
    }

    public function core($usuario_id = NULL) {
        if(!$usuario_id){

        }else{
            if(!$usuario = $this->ion_auth->user($usuario_id)->row()){

                $this->session->set_flashdata('erro','Usuário não foi encontrado');
                redirect('restrita/usuarios');

            }else{

                $this->form_validation->set_rules('first_name','Nome', 'trim|required|min_length[4]|max_length[45]');
                $this->form_validation->set_rules('last_name','Sobrenome', 'trim|required|min_length[4]|max_length[45]');
                $this->form_validation->set_rules('email','E-mail', 'trim|required|min_length[4]|max_length[100]|valid_email|callback_valida_email');
                $this->form_validation->set_rules('usernamename','Nome', 'trim|required|min_length[4]|max_length[45]');

                if($this->form_validation->run()){
                    echo '<pre>';
                    print_r($this->input->post());
                    exit();
                }else{
                    $data = array(
                        'titulo' => 'Editar usuário',
                        'usuario' => $usuario,
                        'perfil' => $this->ion_auth->get_users_groups($usuario_id)->row(),
                        'grupos' => $this->ion_auth->groups()->result()
                    );
                    $this->load->view('restrita/layout/header', $data);
                    $this->load->view('restrita/usuarios/core');
                    $this->load->view('restrita/layout/footer');
                }

                
                
            }
        }
    }

    public function valida_email($email){
        $usuario_id = $this->input->post('usuario_id');
        if(!$usuario_id){
            if($this->core_model->get_by_id('users',array('email' => $email))) {
                $this->form_validation->set_message('valida_email', 'Esse email já existe');
                return false;
            } else{
                return true;
            }
        }else {
            if($this->core_model->get_by_id('users',array('email' => $email, 'id !=' => $usuario_id))) {
                $this->form_validation->set_message('valida_email', 'Esse email já existe');
                return false;
            } else{
                return true;
            }
        }
    }

}

?>