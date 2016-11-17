<?php
$dsn = "DSN=Sample Cloudera Impala DSN 64;host=10.8.0.6;port=21050;database=autotag;Client_CSet=UTF-8";
$id = "1";

$conn = odbc_connect($dsn, '', '');
#print_r($conn);
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
} 

#odbc_exec($objConnect, "SET NAMES 'UTF8'");
#odbc_exec($objConnect, "SET client_encoding='UTF-8'");

$result = odbc_exec($conn, "select number,text from storedata WHERE id = " . $id);
echo "command : select number,text from storedata WHERE id = " . $id ;

#$sql = "SELECT btitle, basin, busurl, bukurl, bimageurl, bauthor, bauthoremail, bauthorbio, bgenre, bsdesc, bldesc  FROM book_info";
#$result = $conn->query($sql);

#echo "--result--";


while($row = odbc_fetch_array($result))
print_r($row);

/*if ($result->num_rows > 0) {
   while($rs = odbc_fetch_array($result))
   {
		if($out != "[")
		{
			$out .= ",";
		}
		$out .= '{"id":"' . $rs["id"] . '",';
		$out .= '"number":"' . $rs["number"] . '",';
		$out .= '"text":"' . $rs["text"] . '",';
    }
	
	$out .= "]";
	echo $out;
} 
else {
	echo "0 results";
}*/
$conn->close();
?>
