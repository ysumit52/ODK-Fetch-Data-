<?php
		$conn = new mysqli('localhost', 'root', 'test123', 'impact2');
		
		if ($conn->connect_error) {
   	 die("Connection failed: " . $conn->connect_error);
		} 
		//echo "Connected successfully";
		$uri_value = $conn->real_escape_string($_POST['uri_value']);
		//$uri_value = "uuid:b1ca0c40-d247-4e58-8fb8-cb3c93170d3d";
		$table_name = $conn->real_escape_string($_POST['table_name']);
		//$table_name = "BUILD_FYIELD_DATA_1532407203_ATTENDANCE_BN";
		//$dataTypeResult = array();
		$selectQuery ="SELECT CONTENT_TYPE type FROM $table_name WHERE _PARENT_AURI = '$uri_value';";
		$sql = $conn->query($selectQuery);
			if ($sql->num_rows > 0) {
				
				while ($DataTypeRow = $sql->fetch_assoc()) {
					$dataTypeResult = $DataTypeRow['type'];
				 	$result['dataTypeElement'] = $dataTypeResult;
				}
				mysqli_close($conn);
				echo json_encode($result);
			}
			else {
    		echo "Error: " . $sql . "<br>" . $conn->error;
		}
?>