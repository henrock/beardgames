<?php
/*
Uploadify v2.1.4
Release Date: November 8, 2010

Copyright (c) 2010 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
if (!empty($_FILES)) {
    $pathUpload = $_SERVER['DOCUMENT_ROOT'] . "/import/";
	$targetFile = $pathUpload . $_FILES['Filedata']['name'];   
    if(!is_dir($pathUpload))
    {
        echo json_encode(array('error' => '1', 'msg' => 'Please make new folder "import" on root folder and CHMOD 0777.'));
        exit;
    }
    if(!is_writable($pathUpload))
    {
        echo json_encode(array('error' => '1', 'msg' => 'Pls CHMOD folder "'.$pathUpload.'" 0777 first.'));
        exit;        
    }
	move_uploaded_file($_FILES['Filedata']['tmp_name'],$targetFile);
    echo json_encode(array('error' => '0', 'msg' => $_FILES['Filedata']['name']));
    exit;
}
?>