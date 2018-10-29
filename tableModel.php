	<?php
	 if (isset($_POST['getData'])) {
		$conn = new mysqli('localhost', '', '', '');
		 $getData = $conn->real_escape_string($_POST['getData']);
		// $getData = "uuid:c6155b3a-fb28-4647-99d0-7d2f72674ebb";
		$start = $conn->real_escape_string($_POST['start']);
		$limit = $conn->real_escape_string($_POST['limit']);
		$no = $conn->real_escape_string($_POST['no']);
		$headerStartValue = $conn->real_escape_string($_POST['headerStartValue']);
		$filterModalData = $conn->real_escape_string($_POST['filterData']);
		// $filterModalData ="STORE_NAME=Big Basket";
		$arr = explode("=", $filterModalData);
		$filterData = $arr[0]; 
		$filterData2 = $arr[1];
		$filter =$filterData.'="'.$filterData2.'"';

		
		$i =0;
		$j = 0;
		$l =0;
		$m =0;
		$uriValue = array();
		$sql = $conn->query("SELECT  info.FORM_ID formId, model.ELEMENT_NAME name, model.PERSIST_AS_TABLE_NAME tableName, model.PERSIST_AS_COLUMN_NAME columnName, model.URI_SUBMISSION_DATA_MODEL dataModel,model.ELEMENT_TYPE type, sub.SUBMISSION_FORM_ID form from _form_data_model model left join _form_info_submission_association sub on model.URI_SUBMISSION_DATA_MODEL= sub.URI_SUBMISSION_DATA_MODEL left join _form_info info on sub.URI_MD5_FORM_ID = info._URI left join _form_info_fileset file on sub.URI_MD5_FORM_ID = file._PARENT_AURI where sub.URI_SUBMISSION_DATA_MODEL ='$getData' LIMIT 2,100");
if(strcmp($filterData, "All")==0){
		if ($sql->num_rows > 0) {
			
			$k=0;
			while($row = $sql->fetch_assoc()) {

				if ($row['type'] == "BINARY_CONTENT_REF_BLOB" || $row['type'] == "REF_BLOB" || $row['type'] == "GEOPOINT" || $row['type'] == "GROUP" || $row['type'] =="SELECTNg" )
				{
						 //$response = '<th>'.$row['e'].'</th>';
				}
				
				else{
					
		// Start of Header Column ----------------------------------------------------
					$columnDetails = $row['columnName'];
					if(!empty($columnDetails )){
						$headerResponse = '<th>'.$row['columnName'].'</th>';
						$headerResult [$k] = $headerResponse;
						$k++;
					
					}
					else{
						$string = $row['tableName'];
						$columnNameBlank = substr($string, strpos($string, "_") + 1);
						$headerResponse = '<th>'.$row['name'].'</th>';
						$headerResult [$k] = $headerResponse;
						$k++;

					}
					
					$return['header'] = $headerResult;
					
		// End of Header Column	--------------------------------------------Start of Table Data 

					$value = preg_replace('/\s+/', '_', $row['tableName']);
					$modalName = preg_replace('/\s+/', '_', $row['name']);
					$formId = preg_replace('/\s+/', '_', $row['formId']);
					$j =0;
					$uriValue = '';
					$dataResponse =array();
					$columnDetails = $row['columnName'];
					$tableDetails [] = $row['tableName'];
					$uriDetails ='';
					
		// End of Table data core------------------------------------------If Column Contain Image Data
					
						if($row['type'] =="BINARY"){
						
						$temp = explode("_", $row['tableName']);
						$short = '';
						for ($o=0; $o < sizeof($temp); $o++) { 
							$short .=$temp[$o];

							if ((int)$temp[$o] == true) {
								break;
							}
							else {
								$short .= "_";
							}
						}
						$tableValue  = $short."_core";

						$rotateTable = "SELECT _URI uri from ".$tableValue." order by _CREATION_DATE LIMIT $start,$limit";

						$rotateTableSql = $conn->query($rotateTable);
						if($rotateTableSql->num_rows>0){
							
							while ($rotateTableRow = $rotateTableSql->fetch_assoc()) {
								
								$imageDataResponse = '<td><img onclick="imageV(this)" id="myImg" class="myImg"
								data-uri='.$rotateTableRow['uri'].'  data-table='.$row['tableName'].' src="http://18.221.206.80:8080/ODKAggregate/view/binaryData?blobKey='.$formId.'[@version=null+and+@uiVersion=null]/data[@key='.$rotateTableRow['uri'].']/'.$modalName.'&previewImage=true" alt="image" width="50" "/></td>';

										$dataResult [$i][$j] = $imageDataResponse;
										$j++;

									}					
							}
							
					}

// End of Image ----------------------------------------------------If Column Contain Selective data 
					
					else if ($row['type'] =="SELECTN") 
					{
						$selecA = array();
						
						$re = '/\w.*\d{7,}/m';
						$str = $row['tableName'];
						preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
						$em	=array_shift( $matches[0] );
						$tableValue  = $em."_core";
						$distinctDataUriQuery = "SELECT _URI parent from ".$tableValue." order by _CREATION_DATE LIMIT $start,$limit";
						$distinctDataSql = $conn->query($distinctDataUriQuery);
						if($distinctDataSql->num_rows>0)
						{
							
							while ($distinctDataRow = $distinctDataSql->fetch_assoc()) 
							{
								
								$show = $distinctDataRow['parent'];
								$selectQuery ="SELECT value FROM ".$row['tableName']." where _PARENT_AURI ='$show' order by _CREATION_DATE LIMIT $start,$limit";
								$selectSql = $conn->query($selectQuery);
								if($selectSql->num_rows>0)
								{
									//echo " Inside Selcet: ".$row['tableName']." ";
									while ($selectDataRow = $selectSql->fetch_assoc()) 
									{
										$selectDataResponse = $selectDataRow['value'];
										$selectA  .= $selectDataResponse." ";
									}
									$dataResult [$i][$j] = '<td>'.$selectA.'</td>';
									$j++;
									$selectA ="";
								}
								else{
									$dataResult [$i][$j] = '<td><img></img></td>';
									$j++;
							
									}

							}
						}

						
					}

// End of Selection Data ----------------------------------------Start of Other Data					
					else
					{
						$columnQuery = "SELECT ".$row['columnName']." cName, _URI FROM ".$row['tableName']." LIMIT $start,$limit";

						$columnDataSql = $conn->query($columnQuery);
						if ($columnDataSql->num_rows > 0) 
						{
							
							while($columnDataRow = $columnDataSql->fetch_assoc()) 
							{

								$dataResponse = '<td>'.$columnDataRow['cName'].'</td>';
								$uriValue = $columnDataRow['_URI'];
								//echo "Inside URI-deta ".$uriValue;
								$dataResult [$i][$j] = $dataResponse;
								$j++;

							}
							
						}
						

					}

					$i++;

					$return['dataArray'] = $dataResult;  // return data of table 
				}

			}
		}  	
// Selection Data in drop down -----------------------------------------------		
		if($start == 0)
		{
			//$userId = $_COOKIE['user_id'];
			
			$selectionSql = $conn->query('SELECT fileset.form_name form_name, sub.URI_SUBMISSION_DATA_MODEL _uri FROM _form_info_fileset fileset inner join _form_info_submission_association sub on sub.URI_MD5_FORM_ID = fileset._PARENT_AURI ');
				 // where user_id ='.$userId);
			$titleSql =$conn->query("SELECT fileset.form_name form_name, sub.URI_SUBMISSION_DATA_MODEL _uri FROM _form_info_fileset fileset inner join _form_info_submission_association sub on sub.URI_MD5_FORM_ID = fileset._PARENT_AURI where sub.URI_SUBMISSION_DATA_MODEL ='$getData'");

			if ($selectionSql->num_rows > 0) {
				$selectionResponse =array();
				while($selectionRow = $selectionSql->fetch_assoc()) {
					$rowId = 1;
					if($rowId == 1){
						$selectionResponse ='<option value= '.$selectionRow['_uri'].' >'.$selectionRow['form_name'].'</option>';
						$rowId++;
					}
					else{
						$selectionResponse ='<option value= '.$selectionRow['_uri'].'>'.$selectionRow['form_name'].'</option>';
					}
					
					$selectionResult [] = $selectionResponse;
				}

				$return['selectionArray'] = $selectionResult;
			} 

			if($titleSql->num_rows>0){
				while ($titleRow = $titleSql->fetch_assoc()) {
					$titleResponse ='<h1>'.$titleRow['form_name'].'</h1>';
					$return['titleData'] = $titleResponse;
				}
			}
		}
	}

	//******************************Modal Filter******************************
	else{
		$modalSql = $conn->query("SELECT  info.FORM_ID formId, model.ELEMENT_NAME name, model.PERSIST_AS_TABLE_NAME tableName, model.PERSIST_AS_COLUMN_NAME columnName, model.URI_SUBMISSION_DATA_MODEL dataModel,model.ELEMENT_TYPE type, sub.SUBMISSION_FORM_ID form from _form_data_model model left join _form_info_submission_association sub on model.URI_SUBMISSION_DATA_MODEL= sub.URI_SUBMISSION_DATA_MODEL left join _form_info info on sub.URI_MD5_FORM_ID = info._URI left join _form_info_fileset file on sub.URI_MD5_FORM_ID = file._PARENT_AURI where sub.URI_SUBMISSION_DATA_MODEL ='$getData' and model.PERSIST_AS_COLUMN_NAME = '$filterData'");
		if($modalSql->num_rows>0){
			while ($modalRow = $modalSql->fetch_assoc()) {
				$columnQuery = "SELECT _URI FROM ".$modalRow['tableName']." where ".$filter;
				$columnDataSql = $conn->query($columnQuery);
						if ($columnDataSql->num_rows > 0) {
							
							while($allDataRow = $columnDataSql->fetch_assoc()) {

								if ($sql->num_rows > 0) {
			
			$k=0;
			while($row = $sql->fetch_assoc()) {

				if ($row['type'] == "BINARY_CONTENT_REF_BLOB" || $row['type'] == "BINARY" || $row['type'] == "GEOPOINT" || $row['type'] == "GROUP" || $row['type'] =="SELECTNg" )
				{
						 //$response = '<th>'.$row['e'].'</th>';
				}
				
				else{
					
		// Modal Data Start of Header Column ----------------------------------------------------
					$columnDetails = $row['columnName'];
					if(!empty($columnDetails )){
						$headerResponse = '<th>'.$row['columnName'].'</th>';
						$headerResult [$k] = $headerResponse;
						$k++;
					
					}
					else{
						$string = $row['tableName'];
						$columnNameBlank = substr($string, strpos($string, "_") + 1);
						$headerResponse = '<th>'.$row['name'].'</th>';
						$headerResult [$k] = $headerResponse;
						$k++;

					}
					
					$return['header'] = $headerResult;
					
		// End of Header Column	--------------------------------------------Start of Table Data 

					$value = preg_replace('/\s+/', '_', $row['tableName']);
					$modalName = preg_replace('/\s+/', '_', $row['name']);
					$formId = preg_replace('/\s+/', '_', $row['formId']);
					$j =0;
					$uriValue = '';
					$dataResponse =array();
					$columnDetails = $row['columnName'];
					$tableDetails [] = $row['tableName'];
					$uriDetails ='';
					
		// End of Table data core------------------------------------------If Column Contain Image Data
					
				if($row['type'] =="REF_BLOB")
					{
						
						$temp = explode("_", $row['tableName']);
						$short = '';
						for ($o=0; $o < sizeof($temp); $o++) 
						{ 
							$short .=$temp[$o];

							if ((int)$temp[$o] == true) 
							{
								break;
							}
							else 
							{
								$short .= "_";
							}
						}
						$tableValue  = $short."_core";

						$rotateTable = "SELECT _URI uri from ".$tableValue." where ".$filter." order by _CREATION_DATE";

						$rotateTableSql = $conn->query($rotateTable);
					if($rotateTableSql->num_rows>0)
						{
							
							while ($rotateTableRow = $rotateTableSql->fetch_assoc()) 
							{
								
								$imageDataResponse = '<td><img onclick="imageV(this)" id="myImg" class="myImg" src="http://18.221.206.80:8080/ODKAggregate/view/binaryData?blobKey='.$formId.'[@version=null+and+@uiVersion=null]/data[@key='.$rotateTableRow['uri'].']/'.$modalName.'&previewImage=true" alt="image" width="50" "/></td>';

								$dataResult [$i][$j] = $imageDataResponse;
								$j++;

							}					
						}
							
					}

// End of Image ----------------------------------------------------If Column Contain Selective data 
					
					else if ($row['type'] =="SELECTN") {
						$selecA = array();
						// $distinctDataUriQuery = "SELECT DISTINCT _PARENT_AURI parent from ".$row['tableName']." order by _CREATION_DATE";
						$re = '/\w.*\d{7,}/m';
						$str = $row['tableName'];
						preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
						$em	=array_shift( $matches[0] );
						$tableValue  = $em."_core";
						$distinctDataUriQuery = "SELECT _URI parent from ".$tableValue." where ".$filter." order by _CREATION_DATE ";
						$distinctDataSql = $conn->query($distinctDataUriQuery);
						if($distinctDataSql->num_rows>0){
							
							while ($distinctDataRow = $distinctDataSql->fetch_assoc()) {
								
								$show = $distinctDataRow['parent'];
								$selectQuery ="SELECT value FROM ".$row['tableName']." where _PARENT_AURI ='$show' order by _CREATION_DATE ";
								$selectSql = $conn->query($selectQuery);
								if($selectSql->num_rows>0){
									//echo " Inside Selcet: ".$row['tableName']." ";
									while ($selectDataRow = $selectSql->fetch_assoc()) {
										$selectDataResponse = $selectDataRow['value'];

									$selectA  .= $selectDataResponse." ";
									}
									$dataResult [$i][$j] = '<td>'.$selectA.'</td>';
									$j++;
									$selectA ="";
								}
								else{
									$dataResult [$i][$j] = '<td><img></img></td>';
									$j++;
							
						}

							}
						}

						
					}

// End of Selection Data ----------------------------------------Start of Other Data					
					else{
						$columnQuery = "SELECT ".$row['columnName']." cName, _URI FROM ".$row['tableName']." where ".$filter;

						$columnDataSql = $conn->query($columnQuery);
						if ($columnDataSql->num_rows > 0) {
							
							while($columnDataRow = $columnDataSql->fetch_assoc()) {

								$dataResponse = '<td>'.$columnDataRow['cName'].'</td>';
								$uriValue = $columnDataRow['_URI'];
								//echo "Inside URI-deta ".$uriValue;
								$dataResult [$i][$j] = $dataResponse;
								$j++;

							}
							
						}
						

					}

					$i++;

					$return['dataArray'] = $dataResult;  // return data of table 
				}

			}
		}  	
// Selection Data in drop down -----------------------------------------------		
		if($start == 0)
		{
		// 	//$userId = $_COOKIE['user_id'];
			
		// 	$selectionSql = $conn->query('SELECT fileset.form_name form_name, sub.URI_SUBMISSION_DATA_MODEL _uri FROM _form_info_fileset fileset inner join _form_info_submission_association sub on sub.URI_MD5_FORM_ID = fileset._PARENT_AURI ');
		// 		 // where user_id ='.$userId);
			$titleSql =$conn->query("SELECT fileset.form_name form_name, sub.URI_SUBMISSION_DATA_MODEL _uri FROM _form_info_fileset fileset inner join _form_info_submission_association sub on sub.URI_MD5_FORM_ID = fileset._PARENT_AURI where sub.URI_SUBMISSION_DATA_MODEL ='$getData'");

		// 	if ($selectionSql->num_rows > 0) {
		// 		$selectionResponse =array();
		// 		while($selectionRow = $selectionSql->fetch_assoc()) {
		// 			$rowId = 1;
		// 			if($rowId == 1){
		// 				$selectionResponse ='<option value= '.$selectionRow['_uri'].' >'.$selectionRow['form_name'].'</option>';
		// 				$rowId++;
		// 			}
		// 			else{
		// 				$selectionResponse ='<option value= '.$selectionRow['_uri'].'>'.$selectionRow['form_name'].'</option>';
		// 			}
					
		// 			$selectionResult [] = $selectionResponse;
		// 		}

		// 		$return['selectionArray'] = $selectionResult;
		// 	} 

			if($titleSql->num_rows>0){
				while ($titleRow = $titleSql->fetch_assoc()) {
					$titleResponse ='<h1>'.$titleRow['form_name'].'</h1>';
					$return['titleData'] = $titleResponse;
				}
			}
		}
	

//******************************************************************while end********************************************
							}
						}

			}
		}
		
	}
// End of selection data in drop down-------------------------------		
		mysqli_close($conn);
		echo json_encode($return); // data returned to html
	}

	else
	{
		exit('reachedMax');
	}
	?>
