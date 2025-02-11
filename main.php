<?php
session_start();

require_once 'database.php';

$sqlMonthHiLimit = date('Y-m-t 23:59:59');
$sqlMonthLowLimit = date('Y-m-01 00:00:00');

if(!isset($_SESSION['logged_id'])){

	//if(isset($_POST['login'])){
	if(isset($_POST['email'])){
		
		//$login = filter_input(INPUT_POST, 'login');
		$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
		$password = filter_input(INPUT_POST, 'pass');
		
		//$userQuery =  $db->prepare('SELECT id_user, user_password, user_firstname, user_reg_date FROM users WHERE user_login = :login');
		$userQuery =  $db->prepare('SELECT id_user, user_password, user_firstname, user_reg_date FROM users WHERE user_email = :email');
		$userQuery->bindValue(':email', $email, PDO::PARAM_STR);
		//$userQuery->bindValue(':login', $login, PDO::PARAM_STR);
		$userQuery->execute();
		
		$user = $userQuery->fetch();
		
		if($user && password_verify($password, $user['user_password'])){
			$_SESSION['logged_id'] = $user['id_user'];
			$_SESSION['logged_firstname'] = $user['user_firstname'];
			
			$today = new DateTime(date('Y-m-d')); 
			$reg_date = new DateTime($user['user_reg_date']); 
			$interval = $today->diff($reg_date); 
			$_SESSION['logged_days'] = $interval->days;
			
			if ($_SESSION['logged_days'] == 0){
				$_SESSION['logged_reg_date'] = "dzisiaj!";
			} else {
				$_SESSION['logged_reg_date'] = $interval->format('%y lat, %m miesięcy, %d dni');
			}
			
			unset($_SESSION['bad_attempt']);
		}else{
			$_SESSION['bad_attempt'] = true;
			$_SESSION['bad_attempt'] = $email;
			$_SESSION['logged_firstname'] = "Nieznajomy";
			header('Location: login.php');
			exit();
			
		}
		
	}else{
		header('Location: login.php');
		exit();
	}
}
				$user_id = $_SESSION['logged_id'];
				
				$queryExp = $db->prepare("SELECT SUM(exp_amount) AS total_exp FROM expense WHERE expense.id_user=:id_user AND exp_date BETWEEN :low_limit AND :hi_limit");
				$queryExp->bindValue(':id_user', $user_id, PDO::PARAM_STR);
				$queryExp->bindValue(':low_limit', $sqlMonthLowLimit , PDO::PARAM_STR);
				$queryExp->bindValue(':hi_limit', $sqlMonthHiLimit , PDO::PARAM_STR);
				$queryExp->execute();
				$resultExp = $queryExp->fetch(PDO::FETCH_ASSOC);
				$total_exp = $resultExp['total_exp'];
				
				$queryInc = $db->prepare("SELECT SUM(inc_amount) AS total_inc FROM income WHERE income.id_user=:id_user AND inc_date BETWEEN :low_limit AND :hi_limit");
				$queryInc->bindValue(':id_user', $user_id, PDO::PARAM_STR);
				$queryInc->bindValue(':low_limit', $sqlMonthLowLimit , PDO::PARAM_STR);
				$queryInc->bindValue(':hi_limit', $sqlMonthHiLimit , PDO::PARAM_STR);
				$queryInc->execute();
				$resultInc = $queryInc->fetch(PDO::FETCH_ASSOC);
				$total_inc = $resultInc['total_inc'];
?>


<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
     integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./style.css">
    <link rel="stylesheet" href="./bilans.css">

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
            <a href="#"><i class="fa-solid fa-house-chimney" style="color: #2861c3;"></i></a>
            <h1><a class="navbar-brand col-lg-3 me-0" href="#">&nbsp;Budżet online</a></h1>
            <ul class="navbar-nav col-lg-9 justify-content-lg-end">

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1" href="./addExpense.php">Nowy wydatek</a>
              </li>

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1" href="./addIncome.php">Nowy przychód</a>
              </li>

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1" href="./bilans.php">Aktualny bilans</a>
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
  <main>
    <section id="mainPageContent">
      <div class="container col-12 col-xxl-8 px-4 py-5">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
          <div class="col-10 col-sm-8 col-lg-6">
            <img src="./pics/01main.jpg" class="d-block mx-lg-auto img-fluid" alt="desk ready for work" width="450" height="300" loading="lazy">
         </div>
         <div class="col-10 col-sm-8 col-lg-4">
           <p class="display-6 text-body-emphasis lh-1 mb-0 text-left">Witaj <?php echo $_SESSION['logged_firstname'];?>!</p>
            <div class="col-12 col-sm-10 col-lg-10">
			
			<p class="lead text-left pt-4">Jesteś z nami od: <br><span class="fw-bold"><?php echo $_SESSION['logged_reg_date'];?></span><br><?= $_SESSION['logged_days']>30 ? 'Dziękujemy, doceniamy to!': 'Miło Cię poznać!';?></p>
			
              <p class="lead text-left pt-4">Finanse w aktualnym miesiącu:</p>

                <div class="row">
                  <div class="col-1">
                    <p class="lead text-start p-0">Przychody: </p>
                  </div>
                  <div class="col">
				  <p class="lead fw-bold text-end"><?= number_format($total_inc ?? 0, 2, ',','') ?></p>
                  </div>
                </div>
              
                <div class="row">
                  <div class="col-1">
                    <p class="lead text-start p-0">Wydatki: </p>
                  </div>
                  <div class="col">
					<p class="lead fw-bold text-end"><?= number_format($total_exp ?? 0, 2, ',','') ?></p>
                  </div>
                </div>

                <div class="row">
                  <div class="col-1">
                    <p class="lead fw-bold text-success">Saldo: </p>
                  </div>
                  <div class="col">
					<p class="lead fw-bold <?= (($total_inc-$total_exp) > 0) ? 'text-success':'text-danger'?> text-end"><?= number_format($total_inc-$total_exp, 2, ',','') ?></p>
                  </div>
                </div>
                
              </div>
          </div>
        </div>
      </div>
	  
    </section>
  </main>


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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

  
</body>

</html>