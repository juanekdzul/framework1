<?php 

class UsersController extends AppController { 


	public function __construct(){
		parent::__construct();
	}
    
   /*
    *metodo index
    *metodo 
*/



	public function index(){
		//opcion 1
		$options= array(
			"conditions"=>"users.type_id=types.id"
			);

		$this->set("users", 
			$this->users->find(
				"users, types", 
				"all", 
				$options
				)
			);

		$this->set("title", "listar de usuarios");
		$this->set("usersCount", $this->users->find("users", "count"));


		//opcion 2
		// $users =  $this->users->find("users", "all", $options);
		// $this->set("users",$users);
       /*
    *metodo add
    *metodo  que medoto para agregar un usuario
*/
	}

	public function add(){
	if ($_SESSION["type_name"]=="Administradores") {
		if ($_POST) {
			$pass =new Password();
			$_POST["password"] = $pass->getPassword($_POST["password"]);
			if ($this->users->save("users", $_POST)){
				$this->redirect(array("controller"=>"users"));
			}else{
				$this->redirect(array("controller"=>"users","method"=>"add"));				
			}
		}$this->set("types", $this->users->find("types"));
	}else{
		$this->redirect(array("controller"=>"users"));
	}

		
	}

       /*
    *metodo edit
    *metodo  que permite edita al usuarios
*/

    
	public function edit($id){
		if ($_POST) {

			if (!empty($_POST["newPassword"])) {
				$pass =new Password();
			$_POST["password"] = $pass->getPassword($_POST["password"]);
				
			}
			
			if ($this->users->update("users", $_POST)) {
				$this->redirect(array("controller"=> "users"));
			}else{
				$this->redirect(
					array(
						"controller"=> "users",
						"method"=>"edit/".$_POST["id"])
					);
			}
		}
		$options = array(
			"conditions"=>"id=".$id
			);
		$this->set(
			"user",
			$this->users->find("users", "first", $options)
		);
		$this->set("types", $this->users->find("types"));
		
	}
    /**
	  *metodo delet
	  * metodo que permite eliminar a un usuario
      */
	public function delete($id){
		$options = "users.id=".$id;
		if($this->users->delete("users", $options)){
			$this->redirect(array("controller"=>"users"));
		}

	}

	/**
	*metodo login
	*metodo que permite validad 
	*/
	public function login(){
		$this->_view->setLayout("login");

		if ($_POST) {
			$pass = new Password();
			$filter = new validations();
			$auth = new Authorization();

			$username = $filter->sanitizeText($_POST["username"]);	
			$password = $filter->sanitizeText($_POST["password"]);

			$options = array(
				"field"=>
				     "users.id as user_id,
				     users.password as password,
				     users.username as username,
				     types.name as type_name",
				    "conditions"=>"username='$username' and users.type_id=types.id");
			$user = $this->users->find("users, types", 'first', $options);

			if ($pass->passwordVerify($password, $user["password"])) {
				$auth->login($user);
				$this->redirect(array("controller"=>"users"));
			}else{
				echo "Usuario no valido";
			}	
		}
	}
     /*
     *metodo logout
     *metodo que permite cerra una seccion
     *
     */
	public function logout(){
		$auth = new Authorization();
		$auth->logout();
	}

}
 


?>