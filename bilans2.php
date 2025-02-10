<?php

session_start();

if(isset($_SESSION['logged_id'])){

	$user_id = $_SESSION['logged_id'];
	$expSum = 0;
	$incSum = 0;

	$firstCurrentMonthDay = date('01-m-Y');
	$lastCurrentMonthDay = date('t-m-Y');
	$sqlMonthHiLimit = date('Y-m-t 23:59:59');
	$sqlMonthLowLimit = date('Y-m-01 00:00:00');

	require_once 'database.php';

	$queryExp = $db->prepare("SELECT id_exp, exp_date, exp_amount, exp_cat_name, pay_met_name, exp_comment FROM expense JOIN expense_user_category ON id_exp_cat = id_exp_user_cat JOIN payment_user_method ON id_pay_met = id_user_pay_met WHERE expense.id_user=:id_user AND exp_date BETWEEN :low_limit AND :hi_limit ORDER BY exp_date");
	$queryExp->bindValue(':id_user', $user_id, PDO::PARAM_STR);
	$queryExp->bindValue(':low_limit', $sqlMonthLowLimit , PDO::PARAM_STR);
	$queryExp->bindValue(':hi_limit', $sqlMonthHiLimit , PDO::PARAM_STR);
	$queryExp->execute();
	$resultExp = $queryExp->fetchAll(PDO::FETCH_ASSOC);
	
	$queryInc = $db->prepare("SELECT id_inc, inc_date, inc_amount, inc_cat_name, inc_comment FROM income JOIN income_user_category ON id_inc_cat = id_inc_user_cat WHERE income.id_user=:id_user AND inc_date BETWEEN :low_limit AND :hi_limit ORDER BY inc_date");
	$queryInc->bindValue(':id_user', $user_id, PDO::PARAM_STR);
	$queryInc->bindValue(':low_limit', $sqlMonthLowLimit , PDO::PARAM_STR);
	$queryInc->bindValue(':hi_limit', $sqlMonthHiLimit , PDO::PARAM_STR);
	$queryInc->execute();
	$resultInc = $queryInc->fetchAll(PDO::FETCH_ASSOC);

} else {
	header('Location: login.php');
	exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="./style.css">
  <script src="https://kit.fontawesome.com/f7c473a27a.js" crossorigin="anonymous"></script>
  <title>Budżet domowy online</title>
</head>

<body>

     <nav id="navBar">
    <!-- navBar -->

      <div class="navbar navbar-expand-lg bg-body-tertiary rounded">
        <div class="container-fluid">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#buttonNavbar" aria-controls="buttonNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
  
          <div class="collapse navbar-collapse d-lg-flex" id="buttonNavbar">
            <a href="./main.php"><i class="fa-solid fa-house-chimney" style="color: #2861c3;"></i></a>
            <h1><a class="navbar-brand col-lg-3 me-0" href="./main.php">&nbsp;Budżet online</a></h1>
            <ul class="navbar-nav col-lg-9 justify-content-lg-end">

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1" href="./addExpense.php">Nowy wydatek</a>
              </li>

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1" href="./addIncome.php">Nowy przychód</a>
              </li>

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1 disabled" href="./bilans.php">Aktualny bilans</a>
              </li>              

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1" href="#">Ustawienia</a>
              </li>

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1" href="./logout.php">Wyloguj</a>
              </li>
            </ul>
          </div>
        </div>
      </div>

    </nav>

  
  <!-- mainPageContent -->
  <section id="mainPageContent">

    <section class="py-3 text-center container">
      <div class="row py-lg-3">
        <div class="col-lg-6 col-md-8 mx-auto">
          <h2 class="fw-light fw-bold">Bilans na bieżący miesiąc</h2>
			<h3>Struktura Twoich przychodów i wydatków <br> w okresie <span class="fw-bold">od <?= $firstCurrentMonthDay ?> do
            <?= $lastCurrentMonthDay ?></span></h3>
        </div>
      </div>
	  
	    <div class="btn-group m-4" role="group" aria-label="Przełącznik raportu">
        <a href="./bilans.php" class="btn btn-lg btn-outline-primary" id="button1" onclick="toggleButtons('button1', 'button2')">Podział na kategorie</a>
        <a href="./bilans2.php" class="btn btn-lg btn-primary" id="button2" onclick="toggleButtons('button2', 'button1')">Wyświetl wszystko</a>
		</div>  
	  
    </section>
    <div class="album py-3 bg-body-tertiary">
      <div class="container">
        <div class="row row-cols-xl-2 row-cols-xl-2 g-1">
          <div class="col">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="container tableExpenses table-responsive">
                  
				  <h3>Przychody</h3>
				  <p>W okresie od <span class="fw-bold"><?= $firstCurrentMonthDay ?></span> do <span class="fw-bold"><?= $lastCurrentMonthDay ?></span></p>
				  
				    <table class="table table-hover">
                    <thead>
                     <tr>
                        <th class="text-center px-1">Nr.</th>
                       <th class="text-center px-1">Data</th>
                       <th class="text-center pe-1">Kwota</th>
                       <th class="px-1">Kategoria</th>
                       <th class="px-1">Komentarz</th>
                      </tr>
                    </thead>
					
					<tbody>
					<?php 
					$i = 0;
					foreach ($resultInc as $row): 
					$incSum += $row['inc_amount'];
					$i++;
					?>
					<tr>
                    <td class="text-center px-1 fw-bold"><?= str_pad($i, 1, "0", STR_PAD_LEFT); ?>. </td>
                    <td class="text-center px-1 text-nowrap"><?= (new DateTime($row['inc_date']))->format('d.m.y H:i') ?></td>
                    <td class="text-end pe-1 fw-bold"><?= number_format($row['inc_amount'], 2, ',', '') ?></td>
                    <td class="px-1"><?= $row['inc_cat_name'] ?></td>
                    <td class="px-1"><?= $row['inc_comment'] ?></td>
					</tr>
					<?php endforeach; ?>
					</tbody>
					
                    <tfoot>
                      <tr>
                        <th></th>
                        <th class="text-center">Suma: </th>
						<th class="text-end pe-1 text-nowrap"><?= number_format($incSum, 2, ',','') ?></th>
                        <th></th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
				  

                </div>
              </div>
            </div>
          </div>

          <div class="col">
            <div class="card shadow-sm">
              <div class="card-body">
                <div class="container tableIncomes table-responsive">
                  <h3>Wydatki</h3>
                  <p>W okresie od <span class="fw-bold"><?= $firstCurrentMonthDay ?></span> do <span class="fw-bold"><?= $lastCurrentMonthDay ?></span></p>


					<table class="table table-hover">
                    <thead>
                      <tr>
                        <th class="text-center px-1">Nr.</th>
                        <th class="text-center px-1">Data</th>
                        <th class="text-center pe-1">Kwota</th>
                        <th class="px-1">Kategoria</th>
						<th class="px-1">Płatność</th>
                        <th class="px-1">Komentarz</th>
                      </tr>
                    </thead>
					
					<tbody>
					<?php 
					$i = 0;
					foreach ($resultExp as $row): 
					$i++;
					$expSum += $row['exp_amount'];
					?>
					<tr>
                    <td class="text-center px-1 fw-bold"><?= str_pad($i, 1, "0", STR_PAD_LEFT); ?>. </td>
                    <td class="text-center px-1 text-nowrap"><?= (new DateTime($row['exp_date']))->format('d.m.y H:i') ?></td>
                    <td class="text-end pe-1 fw-bold"><?= number_format($row['exp_amount'], 2, ',', '') ?></td>
                    <td class="px-1"><?= $row['exp_cat_name'] ?></td>
					<td class="px-1"><?= $row['pay_met_name'] ?></td>
                    <td class="px-1"><?= $row['exp_comment'] ?></td>
					</tr>
					<?php endforeach; ?>
					</tbody>
					
                    <tfoot>
                      <tr>
                       <th></th>
                       <th class="text-center">Suma: </th>
					   <th class="text-end pe-1 text-nowrap"><?= number_format($expSum, 2, ',','') ?></th>
                       <th></th>
                       <th></th>
                      </tr>
                    </tfoot>
                  </table>

                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
  <section class="py-3 text-center container">
    <div class="col-lg-6 col-md-8 mx-auto">
      <div class="card mb-3 rounded-3 shadow-sm border-success">
        <div class="card-header py-3 <?= (($incSum-$expSum) > 0) ? 'text-bg-success border-success':'text-bg-danger border-danger'?>">
          <h2 class="my-0 fw-bold"><?= (($incSum-$expSum) > 0) ? 'Gratulacje!!!':'Uwaga!!!'?></h2>
        </div>
        <div class="card-body">
          <h3><?= (($incSum-$expSum) > 0) ? 'Świetnie zarządzasz swoimi finansami :)':'Ostrożnie, wpadasz w długi :('?></h3>
		  <h3>Aktualne saldo: <span class="fw-bold <?= (($incSum-$expSum) > 0) ? 'text-success':'text-danger'?>"><?= $incSum-$expSum ?> PLN</span></h3>
        </div>
      </div>
    </div>

    <div class="container-sm" style="position: relative;">
      <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto">
          <canvas id="myChart"></canvas>
        </div>
      </div>
    </div>
  </section>
  
  <!-- Footer -->
  <footer id="footer">
    <div class="footer container ">
      <div class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
        <div class="col-md-4 d-flex align-items-center">
          <span class="mb-3 mb-md-0 text-body-secondary">Budżet online &#169; 2024</span>
        </div>
        <ul class="socials nav col-md-4 justify-content-end list-unstyled d-flex ">
          <li class="ms-3"><a href="#" class="text-body-secondary"><i class="fa-brands fa-facebook"></i></a></li>
          <li class="ms-3"><a href="#" class="text-body-secondary"><i class="fa-brands fa-linkedin"></i></a></li>
          <li class="ms-3"><a href="#" class="text-body-secondary"><i class="fa-brands fa-github"></i></a></li>
        </ul>
      </div>
    </div>
  </footer>

<?php

$chartQuery = $db->prepare("SELECT exp_cat_name, SUM(exp_amount) AS total_amount FROM expense JOIN expense_user_category ON id_exp_cat = id_exp_user_cat WHERE expense.id_user = :user_id AND exp_date BETWEEN :low_limit AND :hi_limit GROUP BY exp_cat_name ORDER BY total_amount DESC");
$chartQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$chartQuery->bindValue(':low_limit', $sqlMonthLowLimit , PDO::PARAM_STR);
$chartQuery->bindValue(':hi_limit', $sqlMonthHiLimit , PDO::PARAM_STR);
$chartQuery->execute();
$chartResults = $chartQuery->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$data = [];
foreach ($chartResults as $row) {
    $labels[] = $row['exp_cat_name'];
	$data[] = (float)$row['total_amount'];

}

?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.0.0/dist/chart.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

  <script>

    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
      type: 'doughnut',
      data: {
		 labels: <?= json_encode($labels) ?>,
        datasets: [{
		  data: <?= json_encode($data) ?>,
          backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(25, 99, 13)',
            'rgb(54, 16, 235)',
            'rgb(55, 255, 255)',
            'rgb(255, 205, 86)'
          ],
          hoverOffset: 5,
          datalabels: {
			        anchor: 'center',
                    align: (context, value) => {
                        return context.dataIndex % 2 === 0 ? 'start' : 'end';

                    },
			
            backgroundColor: 'white',
            borderWidth: 5,
            borderRadius: 50,
            font: function (context) {
              var width = context.chart.width;
              var size = Math.round(width / 32);
              size = size > 14 ? 14 : size; 
              size = size < 9 ? 9 : size; 

              return {
                weight: 'bold',
                size: size
              };
            },
			 formatter: (value, context) => {
                        let total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                        let percentage = ((value / total) * 100).toFixed(2);
                        return percentage + '%';
                    },

          }
        }]
      },
      options: {

        plugins: {
          legend: {
            position: "right",
            labels: {
              font: function (context) {
                var width = context.chart.width;
                var size = Math.round(width / 32);
                size = size > 12 ? 12 : size;
                size = size < 6 ? 6 : size; 

                return {
                  weight: 'bold',
                  size: size
                };
              },
            }
          },
          title: {
            display: true,
            text: "Struktura Twoich wydatków:",
            font: {
              size: 18,
              family: 'Arial'
            }
          },
          tooltip: {
            callbacks: {
			       label: (context) => {
                            let total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                            if (total === 0) {
                                return context.label + ": " + context.raw + " (0%)";
                            }
							let percentage = ((context.raw / total) * 100).toFixed(2);
                            return context.label + ": " + context.raw + " PLN (" + percentage + "%)";
                        }
            }
          },
        },
      },
      plugins: [ChartDataLabels]
    });

  </script>
  
      <script>
        function toggleButtons(activeButtonId, inactiveButtonId) {
            const activeButton = document.getElementById(activeButtonId);
            const inactiveButton = document.getElementById(inactiveButtonId);

            activeButton.classList.remove('btn-outline-primary');
            activeButton.classList.add('btn-primary');

            inactiveButton.classList.remove('btn-primary');
            inactiveButton.classList.add('btn-outline-primary');
        }
    </script>

</body>

</html>