<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<link rel="apple-touch-icon" sizes="76x76" href="./assets/img/apple-icon.png">
<link rel="icon" type="image/png" href="./assets/img/favicon.png">

<title>Employee Details</title>

<!--     Fonts and icons     -->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />

<!-- Nucleo Icons -->
<link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
<link href="./assets/css/nucleo-svg.css" rel="stylesheet" />

<!-- Font Awesome Icons -->
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<link href="./assets/css/nucleo-svg.css" rel="stylesheet" />

<!-- CSS Files -->
<link id="pagestyle" href="./assets/css/argon-dashboard.css?v=2.0.0" rel="stylesheet" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
<script>
$(document).ready(function(){ 

	$('.divEmpList').show();
	$('.divEmpAdd').hide();
	
	$("#ffEmployeeFile").change(function() {
			
		const csvFile = document.getElementById("ffEmployeeFile");
		const input = csvFile.files[0];
		const reader = new FileReader();

		reader.onload = function (e) {
			const text = e.target.result;
			const data = csvToArray(text);
			var dataCarrier=JSON.stringify(data); 
		
			var JsonKeys = getKeys(JSON.parse(dataCarrier)); 
			var JsonValues = JSON.parse(dataCarrier).map(Object.values); 
		
			var Head = '<thead><tr>';
			for(var i=0;i<JsonKeys.length;i++)
			{
				Head += '<th>'+JsonKeys[i].replace(/(\r\n|\n|\r)/gm, '')+'</th>';
				$('#ddEmployeeCode').append($('<option value="'+JsonKeys[i].replace(/(\r\n|\n|\r)/gm, '')+'">'+JsonKeys[i]+'</option>'));
				$('#ddEmployeeName').append($('<option value="'+JsonKeys[i].replace(/(\r\n|\n|\r)/gm, '')+'">'+JsonKeys[i]+'</option>'));
				$('#ddDepartment').append($('<option value="'+JsonKeys[i].replace(/(\r\n|\n|\r)/gm, '')+'">'+JsonKeys[i]+'</option>'));
				$('#ddDOB').append($('<option value="'+JsonKeys[i].replace(/(\r\n|\n|\r)/gm, '')+'">'+JsonKeys[i]+'</option>'));
				$('#ddDOJ').append($('<option value="'+JsonKeys[i].replace(/(\r\n|\n|\r)/gm, '')+'">'+JsonKeys[i]+'</option>'));
				$('#ddExperiance').append($('<option value="'+JsonKeys[i].replace(/(\r\n|\n|\r)/gm, '')+'">'+JsonKeys[i]+'</option>'));
				$('#ddAge').append($('<option value="'+JsonKeys[i].replace(/(\r\n|\n|\r)/gm, '')+'">'+JsonKeys[i]+'</option>'));
			}
			Head += '</tr></thead>';
		
			var Rows = '';                       
			for(var j=0;j<JsonValues.length-1;j++)
			{
				Rows += '<tr>';
				for(var k=0;k<JsonValues[j].length;k++)
				{
					Rows += '<td>'+JsonValues[j][k].replace(/(\r\n|\n|\r)/gm, '')+'</td>';
				}
				Rows += '</tr>';
			}
			
			GenerateEmployeeTable(Rows,Head);
		
		};

		reader.readAsText(input);
	  
			$('.divEmpList').hide();
			$('.divEmpAdd').show();
	});
	 
	$("#btnSave").click(function() {
	 
		var myRowsN = [];
		var $thn = $('#tblEmployee th');
        $('#tblEmployee tbody tr').each(function (i, tr) {
            var obj = {}, $tds = $(tr).find('td');
            $thn.each(function (index, th) {
                obj[$(th).text()] = $tds.eq(index).text();
            });
            myRowsN.push(obj);
        });
        var Employee = JSON.stringify(myRowsN);
		var EmployeeHeaders = getKeys(JSON.parse(Employee));
		var EmployeeParse=JSON.parse(Employee);
		if(EmployeeParse.length>0)
		{
			var form_data = new FormData();
			form_data.append('Employee', Employee);
			form_data.append('EmployeeHeaders', EmployeeHeaders);
			form_data.append('EmployeeCode', $('#ddEmployeeCode').val());
			form_data.append('EmployeeName', $('#ddEmployeeName').val());
			form_data.append('Department', $('#ddDepartment').val());
			form_data.append('DOB', $('#ddDOB').val());
			form_data.append('DOJ', $('#ddDOJ').val());
			form_data.append('Experiance', $('#ddExperiance').val());
			form_data.append('Age', $('#ddAge').val());
			
			$.ajax({
				url: "SaveEmployee.php", // point to server-side PHP script
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,                         
				type: 'post',
				success: function(data){
					
					if(data==1)
					{
						ErrorWhileSave("Not Saved!","Duplicate employee code");
					}
					else
					{
						SuccessFullySaved("Good Job!","Successfully Saved");
						setTimeout(function() { window.location.href ="index.php";}, 3000);
					}
				}
			});	
		}
		else
		{
			ErrorWhileSave("Not Saved!","please upload file contains data");
		}
		
	});
	$("#btnView").click(function() {
		$('.divEmpList').show();
		$('.divEmpAdd').hide();
	});
});
var getKeys = function(arr) {
	var key, keys = [];
	for (i=0; i<arr.length; i++) {
		if(i==1) break;
		for (key in arr[i]) {
			keys.push(key);
		}
	}
	return keys;
};
function csvToArray(str, delimiter = ",") {
  
	const headers = str.slice(0, str.indexOf("\n")).split(delimiter);
	const rows = str.slice(str.indexOf("\n") + 1).split("\n");
  
	if(headers.length<5)
	{
		$("#ffEmployeeFile").val('');
		ErrorWhileSave("Warning!","minimum 5 columns required for the file");
	}
	else if(headers.length>7)
	{
		$("#ffEmployeeFile").val('');
		ErrorWhileSave("Warning!","maximum 7 columns permitted for the file");
	}
	else if(rows.length>20)
	{
		$("#ffEmployeeFile").val('');
		ErrorWhileSave("Warning!","maximum 20 rows permitted for the file");
	}
	else if((headers.length>=5)&&(rows.length<=20))
	{  
		const arr = rows.map(function (row) {
			const values = row.split(delimiter);
			const el = headers.reduce(function (object, header, index) {
				object[header] = values[index];
				return object;
			}, {});
			return el;
		});
 
		// return the array
		return arr;
	}
}
function GenerateEmployeeTable(Rows,TableHeads) { 
    
        var $table = $('<table/>');
        $table.addClass("table Employee");  
        $table.attr('id', 'tblEmployee');
        $table.append(TableHeads);
        var $tbody = $('<tbody/>');
        $tbody.append(Rows);
        $table.append($tbody);
        $('#divEmployeeList').html($table);
}
function SuccessFullySaved(header,Message) {
            if (Message == null)
                Message = "";
            swal(header, "" + Message + "", "success")
        };

function ErrorWhileSave(header,Message) { 
            if (Message == null)
                Message = "";
            swal(header, "" + Message + "", "error")
        };
</script>
</head>
<body class="g-sidenav-show  bg-gray-100">
    <main class="main-content border-radius-lg ">
        <div class="container-fluid py-4">
            <div class="row mt-4 divEmpList">
				<div class="col-lg-12 mb-lg-0 mb-4">
					<div class="card ">
						<div class="card-header pb-0 p-3">
							<div class="d-flex justify-content-between">
								<h6 class="mb-2">Employee Details</h6>
							</div>
						</div>
						<div class="table-responsive">
							<table class="table align-items-center ">
								<tbody>
								<?php
								$con=mysqli_connect("localhost","root","","employee_db");
								$sql="SELECT EmployeeCode, Name, Department, Experiance, Age, DOB, DOJ FROM employee_tbl";
								$exe=mysqli_query($con,$sql);
								while($fetch=mysqli_fetch_array($exe))
								{
								?>
									<tr>
									<td>
										<div class="text-center">
											<p class="text-xs font-weight-bold mb-0">Employee Code:</p>
											<h6 class="text-sm mb-0"><?php echo $fetch['EmployeeCode'] ?></h6>
										</div>
									</td>
									<td>
										<div class="text-center">
										<p class="text-xs font-weight-bold mb-0">Employee Name:</p>
										<h6 class="text-sm mb-0"><?php echo $fetch['Name'] ?></h6>
										</div>
									</td>
									<td>
										<div class="text-center">
										<p class="text-xs font-weight-bold mb-0">Department:</p>
										<h6 class="text-sm mb-0"><?php echo $fetch['Department'] ?></h6>
										</div>
									</td>
									<td>
										<div class="text-center">
										<p class="text-xs font-weight-bold mb-0">Age:</p>
										<h6 class="text-sm mb-0"><?php echo $fetch['Age'] ?></h6>
										</div>
									</td>
									<td class="align-middle text-sm">
										<div class="col text-center">
										<p class="text-xs font-weight-bold mb-0">Experiance in the organization:</p>
										<h6 class="text-sm mb-0"><?php echo $fetch['Experiance'] ?></h6>
										</div>
									</td>
									</tr>
								<?php
								}
								?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="row mt-4">
				<div class="col-lg-12 mb-lg-0 mb-4">
					<div class="card ">
						<div class="card-header pb-0 pt-3 bg-transparent">
							<h6 class="text-capitalize">Import Data</h6>
							<input type="file" id="ffEmployeeFile" name="EmployeeFile">
							<div class="row">&nbsp;</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row mt-4 divEmpAdd">
				<div class="col-lg-12 mb-lg-0 mb-4">
					<div class="card ">
						<div class="card-header pb-0 pt-3 bg-transparent">
							<h6 class="text-capitalize">Choose Columns</h6>
						</div>
						<div class="row">
							<div class="col-lg-2">
								<label class="control-label">Employee Code</label>
								<select name="EmployeeCode" id="ddEmployeeCode" class="form-control"></select>
							</div>
							<div class="col-lg-2">
								<label class="control-label">Employee Name</label>
								<select name="EmployeeName" id="ddEmployeeName" class="form-control"></select>
							</div>
							<div class="col-lg-2">
								<label class="control-label">Department</label>
								<select name="Department" id="ddDepartment" class="form-control"></select>
							</div>
							<div class="col-lg-2">
								<label class="control-label">DOB</label>
								<select name="DOB" id="ddDOB" class="form-control"></select>
							</div>
							<div class="col-lg-1">
								<label class="control-label">DOJ</label>
								<select name="DOJ" id="ddDOJ" class="form-control"></select>
							</div>
							<div class="col-lg-2">
								<label class="control-label">Experiance</label>
								<select name="Experiance" id="ddExperiance" class="form-control"></select>
							</div>
							<div class="col-lg-1">
								<label class="control-label">Age</label>
								<select name="Age" id="ddAge" class="form-control"></select>
							</div>
						</div>
						<div class="row">&nbsp;</div>
						<div class="row">
							<div class="col-lg-12" id="divEmployeeList"></div>
						</div>
						<div class="row">
							<div class="col-lg-1">
								<button type="button" name="Save" id="btnSave" class="btn btn-dark btn-sm">Save</button>
							</div>
							<div class="col-lg-2">
								<button type="button" name="View" id="btnView" class="btn btn-primary btn-sm">View List</button>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
	</main>
    
<!--   Core JS Files   -->
<script src="./assets/js/core/popper.min.js" ></script>
<script src="./assets/js/core/bootstrap.min.js" ></script>
<script src="./assets/js/plugins/perfect-scrollbar.min.js" ></script>
<script src="./assets/js/plugins/smooth-scrollbar.min.js" ></script>


<!-- Sweet-Alert  -->
<link href="./assets/sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
<script src="./assets/sweetalert/sweetalert.min.js"></script>
<script src="./assets/sweetalert/jquery.sweet-alert.custom.js"></script>

<script>
  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }
</script>

<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>


<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc --><script src="./assets/js/argon-dashboard.min.js?v=2.0.0"></script>
</body>

</html>
