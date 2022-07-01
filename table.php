<?php

require_once __DIR__."/Class/DB.php";

$db = new DB();

$result = $db->select_table();

$table = "";

if (count($result) > 0) {
	foreach ($result as $row) {
		$date = explode("-", $row['data']);
		$date = $date[2]."/".$date[1]."/".$date[0];

		if ($row['labels'] !== null) {
			$color = $row['labels'];
		}else{
			$color = "";
		}		

		$table .= "<tr id = ".$row['id']." style='background-color: ".$color."'>";
		$table .= "<td title='".$row['cliente']."' style='max-width:50px; overflow: hidden;'>".$row['cliente']."</td>";
		$table .= "<td style='max-width:50px; overflow: hidden;'>".$row['ticket']."</td>";
		$table .= "<td title='".$row['assunto']."' style='max-width:50px; overflow: hidden;'>".$row['assunto']."</td>";
		$table .= "<td style='max-width:30px; overflow: hidden;'>".$row['n_acao']."</td>";
		$table .= "<td style='max-width: 200px; overflow: hidden;'>".nl2br($row['acao'])."</td>";
		$table .= "<td style='display: none'>".$row['categoria']."</td>";
		$table .= "<td style='max-width:200px; overflow: hidden;'>".$row['agente']."</td>";
		$table .= "<td style='max-width:100px; overflow: hidden;'>".$row['equipe']."</td>";
		$table .= "<td style='max-width:100px; overflow: hidden;'>".$date."</td>";
		$table .= "<td style='max-width:70px; overflow: hidden;'>".$row['horas']."</td>";
		if ($row['apontado']) {
			$apontado = "Apontado";
			$button = '<button onclick="'."apontar('".$row['id']."')".'" disabled="">Apontar</button>';
		}else{
			$apontado = "Não";
			$button = '<button onclick="'."apontar('".$row['id']."')".'">Apontar</button>';
		}
		$table .= "<td style='display: none'>".$apontado."</td>";
		$table .= '<td>';
		$table .= '<button onclick="'."copytoclipboard('".$row['id']."')".'">Copiar</button>';
		$table .= $button;
		$table .= '<button onclick="'."ignorar('".$row['id']."')".'">Ignorar</button>';
		$table .= '</td>';
		$table .= "<td style='display: none'>".$row['classificacao']."</td>";
		$table .= "<td style='display: none'>".$row['nome']."</td>";
		$table .= "</tr>";
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Projeto GPT | Upload</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
	<img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
	<!-- Left navbar links -->
	<ul class="navbar-nav">
	  <li class="nav-item">
		<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
	  </li>
	  <li class="nav-item d-none d-sm-inline-block">
		<a href="index3.html" class="nav-link">Home</a>
	  </li>
	  <li class="nav-item d-none d-sm-inline-block">
		<a href="#" class="nav-link">Contact</a>
	  </li>
	</ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
	<!-- Brand Logo -->
	<a href="index3.html" class="brand-link">
	  <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
	  <span class="brand-text font-weight-light">GPT-SCC</span>
	</a>

	<!-- Sidebar -->
	<div class="sidebar">

	  <!-- Sidebar Menu -->
	  <nav class="mt-2">
		<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
		  <li class="nav-item">
			<a href="index.php" class="nav-link">
			  <i class="fas fa-home nav-icon"></i>
			  <p>Inicio</p>
			</a>
		  </li>
		  <li class="nav-item">
			<a href="table.php" class="nav-link">
			  <i class="fa fa-table nav-icon"></i>
			  <p>Apontamentos</p>
			</a>
		  </li>
		  <li class="nav-item">
			<a href="upload.php" class="nav-link">
			  <i class="fas fa-upload nav-icon"></i>
			  <p>Upload tabela</p>
			</a>
		  </li>
		</ul>
	  </nav>
	  <!-- /.sidebar-menu -->
	</div>
	<!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
	<section class="content">
		<div class="container-fluid">
		<br>
		<div class="row">
		  <div class="col-12">
			<div class="card">
			  <div class="card-header">
				<h3 class="card-title" id="contagem">Apontamentos</h3>

				<div class="card-tools">
					<div class="custom-control custom-switch">
						<input type="checkbox" onchange="filter_ignored()" class="custom-control-input" id="customSwitch1">
						<label class="custom-control-label" for="customSwitch1">Ignorados</label>
					</div>
					<div class="custom-control custom-switch">
						<input type="checkbox" onchange="filter_apontados()" class="custom-control-input" id="customSwitch2">
						<label class="custom-control-label" for="customSwitch2">Apontados</label>
					</div>
					<div class="custom-control custom-switch">
						<input type="checkbox" onchange="filter_scc()" class="custom-control-input" id="customSwitch3">
						<label class="custom-control-label" for="customSwitch3">SCC</label>
					</div>
				</div>
			  </div>
			  <!-- /.card-header -->
			  <div class="card-body table-responsive p-0">
				<table class="table table-hover text-nowrap" id="table">
					<thead>
						<tr>
							<th>Cliente</th>
							<th>Ticket</th>
							<th>Assunto</th>
							<th>Nº Ação</th>
							<th>Ação</th>
							<th>Agente</th>
							<th>Equipe</th>
							<th>Data</th>
							<th>Horas</th>
							<th>Opções</th>
						</tr>
					</thead>
					<tbody>
						<?=$table?>
					</tbody>
				</table>
			  </div>
			  <!-- /.card-body -->
			</div>
			<!-- /.card -->
		  </div>
		</div>   
		</div>
		</section> 
  </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
	<strong>SOLVS</strong>
	All rights reserved.
	<div class="float-right d-none d-sm-inline-block">
	  <b>Version</b> 0.0.1
	</div>
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
	<script type="text/javascript">
		var bol = true;
		var bol2 = true;
		var bol3 = true;
		var tbody = document.getElementById("table").children[1].children;

		function copytoclipboard(selector)
		{
			var x = "";

			var children = document.getElementById(selector).children;

			for (var i = 1; i < children.length-4; i++) {
				x += children[i].innerHTML.replace(/&nbsp;/gi," ") + "\t";
				navigator.clipboard.writeText(x);
			}
		}

		function filter_ignored()
		{
			if (bol) {
				for (var i = 0; i < tbody.length; i++) {
					if(tbody[i].style.backgroundColor == 'rgb(168, 101, 201)')
					{
						tbody[i].style.display = "none";
					}
				}

				bol = false;
			}else{
				for (var i = 0; i < tbody.length; i++) {
					if(tbody[i].style.backgroundColor == 'rgb(168, 101, 201)')
					{
						tbody[i].style.display = "";
					}
				}

				bol = true;
			}
			count_rows();
		}

		function filter_apontados()
		{
			if (bol2) {
				for (var i = 0; i < tbody.length; i++) {
					if(tbody[i].children[10].innerHTML == 'Apontado')
					{
						tbody[i].style.display = "none";
					}
				}

				bol2 = false;
			}else{
				for (var i = 0; i < tbody.length; i++) {
					if(tbody[i].children[10].innerHTML == 'Apontado')
					{
						tbody[i].style.display = "";
					}
				}

				bol2 = true;
			}
			count_rows();
		}

		function filter_scc()
		{
			if (bol3) {
				for (var i = 0; i < tbody.length; i++) {
					if(tbody[i].children[12].innerHTML != '' && tbody[i].children[13].innerHTML != '')
					{
						tbody[i].style.display = "none";
					}
				}

				bol3 = false;
			}else{
				for (var i = 0; i < tbody.length; i++) {
					if(tbody[i].children[12].innerHTML != '' && tbody[i].children[13].innerHTML != '')
					{
						tbody[i].style.display = "";
					}
				}

				bol3 = true;
			}
			count_rows();
		}

		function apontar(id)
		{
			var element = document.getElementById(id);
			element.style.backgroundColor = "lightgreen";
			element.children[10].innerHTML = "Apontado";
			element.children[11].children[1].disabled = true;
			if (!bol2) {
				element.style.display = "none";
			}
			count_rows();
			post('apontar',id);
		}

		function ignorar(id)
		{
			var element = document.getElementById(id);
			element.style.backgroundColor = "#A865C9";
			element.children[11].children[2].disabled = true;
			if (!bol) {
				element.style.display = "none";
			}
			count_rows();
			post('ignorar',id);
		}

		function count_rows()
		{
			var contagem = document.getElementById("contagem");
			var count = 0;
			for (var i = 0; i < tbody.length; i++) {
				if(tbody[i].style.display != 'none')
				{
					count++;
				}
			}
			contagem.innerHTML = count+" Apontamentos para lançar";
		}

		function post(type,id)
		{
			var http = new XMLHttpRequest();
			var url = 'rest.php';
			var params = type+'=1&id='+id;
			http.open('POST', url, true);

			//Send the proper header information along with the request
			http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

			http.onreadystatechange = function() {//Call a function when the state changes.
			    if(http.readyState == 4 && http.status == 200) {
			        console.log(1);
			    }
			}
			http.send(params);
		}

		count_rows();
	</script>
</body>
</html>
