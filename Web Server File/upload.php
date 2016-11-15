<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
///home/strikermx/upload/

$uploaddir = '/var/www/html/upload/';
$uploadfile = $uploaddir . basename($_FILES['fileToUpload']['name']);
$tmptxt = substr($uploadfile, 0, -3);
$txt = $tmptxt.'txt';
$exec = 'ls';
$exec_java = 'java -jar /var/www/html/upload/pdfbox-app-2.0.3.jar ExtractText '.$uploadfile;
$exec_gettext = 'cat '.$txt;

echo '<pre>';
if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Can't upload!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);

print "</pre>";
print_r($uploadfile);
print_r($txt);

echo "\n-----------------\n";

$old_path = getcwd();
//chdir('/my/path/');
$output_java = shell_exec($exec_java);
print_r($exec_java);
echo "<pre>$output_java</pre>";
$output_text = shell_exec($exec_gettext);
print_r($exec_gettext);
echo "<pre>$output_text</pre>";
chdir($old_path);

?>