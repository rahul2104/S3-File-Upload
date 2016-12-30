<?php
include('image_check.php');
$msg='';
if($_SERVER['REQUEST_METHOD'] == "POST")
{
echo "<pre>";print_r($_FILES);die;
$name = $_FILES['file']['name'][0];
$size = $_FILES['file']['size'][0];
$tmp = $_FILES['file']['tmp_name'][0];
$ext = getExtension($name);

if(strlen($name) > 0)
{

if(in_array($ext,$valid_formats))
{
 
if($size<(1024*1024))
{
include('s3_config.php');
//Rename image name. 
$actual_image_name = time().".".$ext;
$docId      = $this->ramdomPassCreate() . '_' . time() . '.' . $new;
//echo "<pre>";print_r($tmp);die;
if($s3->putObjectFile($tmp, $bucket , $actual_image_name, S3::ACL_PUBLIC_READ) )
{
$msg = "S3 Upload Successful.";	
$s3file='http://'.$bucket.'.s3.amazonaws.com/'.$actual_image_name;
echo "<img src='$s3file' style='max-width:400px'/><br/>";
echo '<b>S3 File URL:</b>'.$s3file;

}
else
$msg = "S3 Upload Fail.";


}
else
$msg = "Image size Max 1 MB";

}
else
$msg = "Invalid file, please upload image file.";

}
else
$msg = "Please select image file.";

}

 function ramdomPassCreate() {

        $id = uniqid();

        $id = base_convert($id, 16, 2);
        $id = str_pad($id, strlen($id) + (8 - (strlen($id) % 8)), '0', STR_PAD_LEFT);

        $chunks = str_split($id, 8);
        //$mask = (int) base_convert(IDGenerator::BIT_MASK, 2, 10);

        $id = array();
        foreach ($chunks as $key => $chunk) {
            //$chunk = str_pad(base_convert(base_convert($chunk, 2, 10) ^ $mask, 10, 2), 8, '0', STR_PAD_LEFT);
            if ($key & 1) {  // odd
                array_unshift($id, $chunk);
            } else {         // even
                array_push($id, $chunk);
            }
        }
        return base_convert(implode($id), 2, 36);
    }
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Files to Amazon S3 PHP</title>
</head>

<body>
<a href='http://www.9lessons.info'>www.9lessons.info</a>
<form action="" method='post' enctype="multipart/form-data">
<h3>Upload image file here</h3><br/>
<div style='margin:10px'><input type='file' name='file'/> <input type='submit' value='Upload Image'/></div>
</form>
<?php 
echo $msg.'<br/>'; 
?>
		

</body>
</html>
