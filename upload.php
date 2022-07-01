<?php

require_once __DIR__."/Class/DB.php";

$db = new DB();

$conn = $db->open();

if (isset($_FILES['file'])) {
	$dados = $apontamentos = [];

	$result = $db->select("ticket, n_acao");

	if (count($result) > 0) {
		foreach ($result as $row) {
			array_push($apontamentos, $row['ticket'].$row['n_acao']);
		}
	}

	$content = explode("\n", file_get_contents($_FILES['file']['tmp_name']));

	unset($content[0]);

	foreach ($content as $key => $value) {
		$content[$key] = explode(";", $value);
		if (array_search($content[$key][4].$content[$key][7], $apontamentos) === false) {
			$temp = [
				$content[$key][0],
				$content[$key][4],
				$content[$key][6],
				$content[$key][7],
				$content[$key][8],
				$content[$key][9],
				$content[$key][10],
				$content[$key][11],
				$content[$key][12],
				$content[$key][15],
				$content[$key][17],
				$content[$key][18],
			];

			array_push($dados, $temp);
		}
	}

	foreach ($dados as $dado) {
		$date = explode("/", $dado[8]);
		$date = $date[2].$date[1].$date[0];
		$query = $conn->prepare("insert into `tickets` (`cliente`, `ticket`, `assunto`, `n_acao`, `acao`, `categoria`, `agente`, `equipe`, `data`, `horas`, `horas_contabilizadas`, `tipo_hora`, `labels`) values (:cliente, :ticket, :assunto, :n_acao, :acao, :categoria, :agente, :equipe, :data, :horas, :horas_contabilizadas, :tipo_hora, :labels);");
		$query->bindParam(":cliente", $dado[0]);
		$query->bindParam(":ticket", $dado[1]);
		$query->bindParam(":assunto", $dado[2]);
		$query->bindParam(":n_acao", $dado[3]);
		$query->bindParam(":acao", $dado[4]);
		$query->bindParam(":categoria", $dado[5]);
		$query->bindParam(":agente", $dado[6]);
		$query->bindParam(":equipe", $dado[7]);
		$query->bindParam(":data", $date);
		$query->bindParam(":horas", $dado[9]);
		$query->bindParam(":horas_contabilizadas", $dado[10]);
		$query->bindParam(":tipo_hora", $dado[11]);

		$labels = "";

		if ($dado[9] == "00:01") {
			$labels = "#A865C9";
		}

		if ($dado[6] == "FSW Totvs Curitiba") {
			$labels = "#A865C9";
		}

		$query->bindParam(":labels", $labels);

		if ($query->execute()) {
			// return true;
		}
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
  	<div class="card-body">
		<div class="form-group">
			<label for="exampleInputFile">Enviar arquivo</label>
			<form method="post" enctype="multipart/form-data" action="upload.php">
				<div class="input-group">
					<div class="custom-file">
						<input type="file" class="custom-file-input" name="file">
						<label class="custom-file-label">Escolha o arquivo</label>
					</div>
					<div class="input-group-append">
						<button type="submit" class="btn btn-block btn-info">Upload</button>
					</div>
				</div>
			</form>
		</div>
	</div>
    
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
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="dist/js/pages/dashboard.js"></script>
</body>
</html>
