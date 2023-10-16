<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cricket Statistics | Dashboard</title>
  <?php 
    //including css script
    include 'include/css.php';
  ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <!-- Navbar -->
  <?php

    //including navbar
    include 'include/nav.php';
    
  ?> 
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php

    //including navbar
    include 'include/sidebar.php';
    
  ?> 
  <!--Sidebar Container end-->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <?php

      // Mapping team id with team name
      $teamNameMapping = [];
      if (($handle = fopen("data/Teams.csv", "r")) !== false) {
          $header = fgetcsv($handle, 1000, ",");
          while (($row = fgetcsv($handle, 1000, ",")) !== false) {
              $teamData = array_combine($header, $row);
              $teamNameMapping[$teamData["teamID"]] = $teamData["name"];
          }
          fclose($handle);
      }

      //check button is clicked or not
      if (isset($_POST["import"])) {
          $filename = $_FILES["file"]["tmp_name"];
          $data = [];
          if (($handle = fopen($filename, "r")) !== false) {
              $header = fgetcsv($handle, 1000, ",");
              while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                  $data[] = array_combine($header, $row);
              }
              fclose($handle);
          }

          // Player data 
          $player_data = [];
          foreach ($data as $value) {
              $playerID = $value['playerID'];
              $yearID = $value['yearID'];
              $teamName = $teamNameMapping[$value['teamID']];
              
              $battingAverage = ($value['AB'] != 0) ? ($value['H'] / $value['AB']) : 0;

              if (!isset($player_data[$playerID])) {
                  $player_data[$playerID] = [
                      'playerID' => $playerID,
                      'yearID' => $yearID,
                      'teamNames' => [],
                      'battingAverageSum' => 0,
                      'numStints' => 0
                  ];
              }

              $player_data[$playerID]['teamNames'][] = $teamName;
              $player_data[$playerID]['battingAverageSum'] += $battingAverage;
              $player_data[$playerID]['numStints']++;
          }

          // Sort players by batting average
          usort($player_data, function($a, $b) {
              $avgA = $a['battingAverageSum'] / $a['numStints'];
              $avgB = $b['battingAverageSum'] / $b['numStints'];
              return $avgB <=> $avgA;
          });

      ?>
          <table class="dataTableLoad table table-bordered table-striped ">
              <thead>
                  <tr align="center">
                      <th scope="row"><b>Player Id</b></th>
                      <th scope="row"><b>Year Id</b></th>
                      <th scope="row"><b>Team Names</b></th>
                      <th scope="row"><b>Batting Average</b></th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($player_data as $playerInfo){ ?>
                      <?php 
                      $playerID = $playerInfo['playerID'];
                      $yearID = $playerInfo['yearID'];
                      $teamNames = implode(', ', array_unique($playerInfo['teamNames']));
                      $battingAverage = $playerInfo['numStints'] ? number_format($playerInfo['battingAverageSum'] / $playerInfo['numStints'], 3) : 0;
                      ?>
                      <tr align="center">
                          <td scope="row"><?php echo $playerID; ?></td>
                          <td scope="row"><?php echo $yearID; ?></td>
                          <td scope="row"><?php echo $teamNames; ?></td>
                          <td scope="row"><?php echo $battingAverage; ?></td>
                      </tr>
                  <?php } ?>
              </tbody>
          </table>
      <?php
      }
      ?>

        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!--footer-->
  <?php
    //including footer
    include 'include/footer.php';
  ?>
  <!--footer end-->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<?php

  //loading all js script
  include 'include/js.php';

?>
</body>
</html>
