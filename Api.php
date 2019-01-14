<?php 

	require_once 'DbConnect.php';
	
	//an array to display response
	$response = array();
	
	//if it is an api call 
	//that means a get parameter named api call is set in the URL 
	//and with this parameter we are concluding that it is an api call

	if(isset($_GET['apicall'])){
		
		switch($_GET['apicall']){
			
			case 'signup':
				//checking the parameters required are available or not 
				if(isTheseParametersAvailable(array('name','email','password'))){
					
					//getting the values 
					$name = $_POST['name']; 
					$email = $_POST['email']; 
					$password = md5($_POST['password']);
					
					
					//checking if the user is already exist with this username or email
					//as the email and username should be unique for every user 
					$stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email ");
					$stmt->bindParam(':email', $email, PDO::PARAM_STR);
					$stmt->execute();   

					//if the user already exist in the database 
					
					if($stmt->fetchColumn() > 0){
						$response['error'] = true;
						$response['message'] = 'Email already registered';
					
						
					}else{
					    
					    try {
					        
					        $sql = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (? ,? ,?)");
                            $sql->execute(array($name, $email, $password));

                         
                        } catch(Exception $e) {
                            echo $e->getMessage();
                            $response['error'] = $e;
                        }
					  
						
						

                			$stmt = $pdo->prepare("SELECT user_id, name,email FROM users WHERE email = :email AND password = :password ");
					$stmt->bindParam(':email', $email, PDO::PARAM_STR);
					$stmt->bindParam(':password', $password, PDO::PARAM_STR);
					$stmt->execute();
					$array = $stmt->fetch(PDO::FETCH_OBJ);
					$json = json_encode( $array );
					
                    
					
        			$response['error'] = false;
        			$response['user'] = $array; 
                		
							$response['message'] = 'User registered successfully'; 
				
						}
					}
					
				else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break; 
			
			case 'signin':
				//for login we need the username and password 
				if(isTheseParametersAvailable(array('email', 'password'))){
					//getting values 
					$email = $_POST['email'];
					$password = md5($_POST['password']); 
					
					//creating the query 
					$stmt = $pdo->prepare("SELECT user_id, name,email FROM users WHERE email = :email AND password = :password ");
					$stmt->bindParam(':email', $email, PDO::PARAM_STR);
					$stmt->bindParam(':password', $password, PDO::PARAM_STR);
					$stmt->execute();
					
					
	   
					
					//if the user exist with given credentials 
					if($stmt->fetchColumn() > 0){
						
					$stmt = $pdo->prepare("SELECT user_id, name,email FROM users WHERE email = :email AND password = :password ");
					$stmt->bindParam(':email', $email, PDO::PARAM_STR);
					$stmt->bindParam(':password', $password, PDO::PARAM_STR);
					$stmt->execute();
					$array = $stmt->fetch(PDO::FETCH_OBJ);
					$json = json_encode( $array );
					
                    
					
        			$response['error'] = false;
        			$response['user'] = $array; 
                    $response['message'] = 'Login successfull'; 
						
					}else{
						//if the user not found 
						$response['error'] = false; 
						$response['message'] = 'Invalid email or password';
					}
				}
			break;
			
			case 'getdatashipment':
				//for login we need the username and password 
				if(isTheseParametersAvailable(array('user_id'))){
					//getting values 
					$user_id = $_POST['user_id'];
				
				
    			    $stmt_waiting_package = $pdo->prepare("SELECT count(*) FROM shipments WHERE user_id = :user_id and status_shipment = 0 ");
					$stmt_waiting_package->bindParam(':user_id', $user_id, PDO::PARAM_STR);
					$stmt_waiting_package->execute();
					
				    $result_waiting_package = $stmt_waiting_package->fetchColumn();
				    
					
				  
				    
				
					
					$stmt_waiting_payment = $pdo->prepare("SELECT count(*) FROM shipments WHERE user_id = :user_id and status_shipment = 1 ");
					$stmt_waiting_payment->bindParam(':user_id', $user_id, PDO::PARAM_STR);
					$stmt_waiting_payment->execute();
				    $result_waiting_payment = $stmt_waiting_payment->fetchColumn();
					
					
					$stmt_unconfirmed_payment = $pdo->prepare("SELECT count(*) FROM shipments WHERE user_id = :user_id and status_shipment = 2 ");
					$stmt_unconfirmed_payment->bindParam(':user_id', $user_id, PDO::PARAM_STR);
					$stmt_unconfirmed_payment->execute();
				    $result_unconfirmed_payment = $stmt_unconfirmed_payment->fetchColumn();
				
					    
					$stmt_total_shipment = $pdo->prepare("SELECT count(*) FROM shipments WHERE user_id = :user_id and status_shipment = 3 ");
					$stmt_total_shipment->bindParam(':user_id', $user_id, PDO::PARAM_STR);
					$stmt_total_shipment->execute();
				    $result_total_shipment = $stmt_total_shipment->fetchColumn();
				    
				    $json = json_encode( $result_waiting_package,$result_waiting_payment,$result_unconfirmed_payment,$result_total_shipment );
				
					
					$datashipment = array(
								'waiting_package'=>$result_waiting_package, 
								'waiting_payment'=>$result_waiting_payment, 
								'unconfirmed_payment'=>$result_unconfirmed_payment,
								'total_shipment'=>$result_total_shipment
								);
					$response['error'] = false;
				
				    $response['datashipment'] = $datashipment; 
                  
				
				}
				
			break;
			
			
			case 'addnewshipment':
				//checking the parameters required are available or not 
				if(isTheseParametersAvailable(array('courier','tracking_no','product_category','product_name','product_quantity','product_price','user_id'))){
					
					//getting the values 
					$courier = $_POST['courier']; 
					$tracking_no = $_POST['tracking_no']; 
					$product_category = $_POST['product_category']; 
					$product_name = $_POST['product_name']; 
					$product_quantity = $_POST['product_quantity']; 
					$product_price = $_POST['product_price'];
					$user_id = $_POST['user_id'];
					
					
				
					
					//checking if the tracking no is already exist with this username or email
					//as the tracking no should be unique for every user 
					$stmt = $pdo->prepare("SELECT * FROM shipments WHERE tracking_no = ? ");
					
					$stmt->bindParam(1, $tracking_no, PDO::PARAM_STR);
					$stmt->execute();
				
					
					
					//if the tracking no already exist 
					if($stmt->fetchColumn() > 0){
						$response['error'] = true;
						$response['message'] = 'Tracking already registered';
						
					}else{
						
						//if user is new creating an insert query 
						$sql = $pdo->prepare("INSERT INTO shipments (user_id,courier,tracking_no,product_category,product_name,product_quantity,product_price) VALUES (?,?,?,?,?,?,?)");
						$sql->bindParam(1, $user_id, PDO::PARAM_STR);
						$sql->bindParam(2, $courier, PDO::PARAM_STR);
						$sql->bindParam(3, $tracking_no, PDO::PARAM_STR);
						$sql->bindParam(4, $product_category, PDO::PARAM_STR);
						$sql->bindParam(5, $product_name, PDO::PARAM_STR);
						$sql->bindParam(6, $product_quantity, PDO::PARAM_STR);
						$sql->bindParam(7, $product_price, PDO::PARAM_INT);
						$sql->execute();
						$response['error'] = false; 
						$response['message'] = 'Shipment registered successfully'; 
						
					
				
					}
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break; 
			
			
			case 'addneworder':
				//checking the parameters required are available or not 
				if(isTheseParametersAvailable(array('name_receiver','address','phone_number','postal_code','shipment_id','total_charge'))){
					
					//getting the values 
					$name_receiver = $_POST['name_receiver']; 
					$address = $_POST['address']; 
					$postal_code = $_POST['postal_code']; 
					$phone_number = $_POST['phone_number']; 
					$shipment_id = $_POST['shipment_id']; 
					$total_charge = $_POST['total_charge'];
				
					
					
				
					
					//checking if the tracking no is already exist with this username or email
					//as the tracking no should be unique for every user 
					$stmt = $pdo->prepare("SELECT * FROM orders WHERE shipment_id = ? ");
					
					$stmt->bindParam(1, $shipment_id, PDO::PARAM_STR);
					$stmt->execute();
				
					
					
					//if the tracking no already exist 
					if($stmt->fetchColumn() > 0){
						$response['error'] = true;
						$response['message'] = 'Shipment Order Already Created';
						
					}else{
						
						//if user is new creating an insert query 
						$sql = $pdo->prepare("INSERT INTO orders (shipment_id,total_charge,name_receiver,address,postal_code,phone_number) VALUES (?,?,?,?,?,?)");
						$sql->bindParam(1, $shipment_id, PDO::PARAM_STR);
						$sql->bindParam(2, $total_charge, PDO::PARAM_STR);
						$sql->bindParam(3, $name_receiver, PDO::PARAM_STR);
						$sql->bindParam(4, $address, PDO::PARAM_STR);
						$sql->bindParam(5, $postal_code, PDO::PARAM_STR);
						$sql->bindParam(6, $phone_number, PDO::PARAM_STR);
					
						$sql->execute();
						$response['error'] = false; 
						$response['message'] = 'Order created successfully';
						
						$edit = $pdo->prepare("UPDATE shipments SET status_shipment = 2 where shipment_id = ?");
						$edit->bindParam(1, $shipment_id,PDO::PARAM_STR);
						$edit->execute();
						
						
				
					}
					
				}else{
					$response['error'] = true; 
					$response['message'] = 'required parameters are not available'; 
				}
				
			break; 
			
			
			
			
			case 'showwaitingpackage':
				if(isTheseParametersAvailable(array('user_id'))){
					//getting values 
					$user_id = $_POST['user_id'];
					
					
					
        		    $stmt = $pdo->prepare("SELECT shipment_id,courier,tracking_no FROM shipments WHERE user_id = :user_id AND status_shipment = 0");
        		    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    
        			$stmt->execute();
        	
        			$resultArray = $stmt->fetchAll();
        			
        			$response['error'] = false;
        			$response['showwaitingshipment'] = $resultArray; 
        						
        				
        				}else{
        					$response['error'] = true; 
        					$response['message'] = 'required parameters are not available'; 
        				}
				
			break;
			
			case 'showwaitingpayment':
				if(isTheseParametersAvailable(array('user_id'))){
					//getting values 
					$user_id = $_POST['user_id'];
					
					
					
        		    $stmt = $pdo->prepare("SELECT shipment_id,billed_weight from shipments WHERE user_id = :user_id AND status_shipment = 1");
        		    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    
        			$stmt->execute();
        	
        			$resultArray = $stmt->fetchAll();
        			
        			$response['error'] = false;
        			$response['showwaitingpayment'] = $resultArray; 
        						
        				
        				}else{
        					$response['error'] = true; 
        					$response['message'] = 'required parameters are not available'; 
        				}
				
			break;
			
			
			
			
			
			default: 
				$response['error'] = true; 
				$response['message'] = 'Invalid Operation Called';
		}
		
	}else{
		//if it is not api call 
		//pushing appropriate values to response array 
		$response['error'] = true; 
		$response['message'] = 'Invalid API Call';
	}
	
	//displaying the response in json structure 
	echo json_encode($response);
	
	//function validating all the paramters are available
	//we will pass the required parameters to this function 
	function isTheseParametersAvailable($params){
		
		//traversing through all the parameters 
		foreach($params as $param){
			//if the paramter is not available
			if(!isset($_POST[$param])){
				//return false 
				return false; 
			}
		}
		//return true if every param is available 
		return true; 
	}