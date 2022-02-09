<?php
$con=mysqli_connect("localhost","root","","employee_db");

$EmployeeHeaders = $_POST['EmployeeHeaders'];
$EmployeeHeader = explode(",", $EmployeeHeaders);
$EmployeeHeadersCount = count($EmployeeHeader);
$EmpCode = $_POST['EmployeeCode'];
$EmpName = $_POST['EmployeeName'];
$EmpDep = $_POST['Department'];
$EmpDOB = $_POST['DOB'];
$EmpDOJ = $_POST['DOJ'];
$EmpExp = $_POST['Experiance'];
$EmpAge = $_POST['Age'];
$Employee = $_POST['Employee'];
$Duplicate=0;
$EmployeeDecode=json_decode($Employee, true);
foreach($EmployeeDecode as $Decode)
{
	$EmployeeCode=$Decode[$EmpCode];
	$Name=$Decode[$EmpName];
	$Name = mysqli_real_escape_string($con,$Name);
	$Department=$Decode[$EmpDep];
	$Department = mysqli_real_escape_string($con,$Department);
	$Experiance=$Decode[$EmpExp];
	$Age=$Decode[$EmpAge];
	$DOB=$Decode[$EmpDOB];
	$DOB=date("Y-m-d", strtotime($DOB) );
	$DOJ=$Decode[$EmpDOJ];
	$DOJ=date("Y-m-d", strtotime($DOJ) );
	
	$sql1="SELECT COUNT(*) AS 'Count' FROM employee_tbl WHERE EmployeeCode='$EmployeeCode'";
	$exe1=mysqli_query($con,$sql1);
	$fetch1=mysqli_fetch_array($exe1);
	$Count=$fetch1['Count'];
	if($Count>0)
	{
		$Duplicate=1;
	}
	else
	{
		$sql="INSERT INTO employee_tbl (EmployeeCode, Name, Department, Experiance, Age, DOB, DOJ) VALUES ('$EmployeeCode', '$Name', '$Department', '$Experiance', '$Age', '$DOB', '$DOJ')";
		$exe=mysqli_query($con,$sql);
	}
}
if($Duplicate==1)
{
	print 1;
}
?>