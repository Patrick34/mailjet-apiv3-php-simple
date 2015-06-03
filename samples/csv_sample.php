<?php
	/*
	 *	This script will add the contacts contained in the sample.csv file
	 *  to the contactslist which ID is defined in $listID.
	 *
	 */

	include("php-mailjet-v3-simple.class.php");

	$mj = new Mailjet('MJ_APIKEY_PUBLIC', 'MJ_APIKEY_PRIVATE');


	$listID = 45;
	$CSVContent = file_get_contents('sample.csv');

	function uploadCSV ($listID, $CSVContent)
    {
    	$uploadParams = array(
            "method" => "POST",
            "ID" => $listID,
            "csv_content" => $CSVContent
        );

	    $csvUpload = $mj->uploadCSVContactslistData($uploadParams);

	    if ($mj->_response_code == 200)
	    {
	    	echo "success - uploaded CSV file ";
	    	return $csvUpload->ID;
	    }
	    else
			exit("error - Couldn't upload the data - code ".$mj->_response_code);
  	}

  	function assignContactsToList ($listID, $uploadID)
    {
    	$assignParams = array(
            "method" => "POST",
            "ContactsListID" => $listID,
            "DataID" => $csvUpload->ID,
            "Method" => "addnoforce"
        );
    
        $csvAssign = $mj->csvimport($assignParams);
    
        if ($mj->_response_code == 201)
        {
        	echo "success - CSV data ".$csvUpload->ID." assigned to contactslist ".$listID;
        	return $csvAssign->Data[0]->ID;
        }
        else
        	exit("error - Couldn't assign contacts to list - code ".$mj->_response_code;)
    }

   	function monitor ($jobID)
   	{
   		$monitorParmas = array (
	        "method" => "VIEW",
	        "ID" => $jobID
	    );

	    $res = $mj->batchjob($monitorParmas);

	    if ($mj->_response_code == 200)
	    {
	    	echo "job ".$res->Data[0]->Status."\n";
	    	return $res->Data[0]->Status;	    	
	    }
	    else
	        exit("error - Couldn't monitor the job - code ".$mj->_response_code."\n");
   	}


   	$uploadID = uploadCSV($listID, $CSVContent);

   	$jobID = assignContactsToList($listID, $uploadID);

   	$status = monitor($jobID);

?>