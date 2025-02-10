<?php
	session_start();
	
	if(isset($_SESSION['logged_id'])){
		
		
		$now = date('Y-m-d\TH:i');
		$nextYear = date('Y-m-d\TH:i', strtotime('+1 year'));
	
				$amount = filter_input(INPUT_POST, 'amount');
				$datetime = filter_input(INPUT_POST, 'datetime');
				$payment = filter_input(INPUT_POST, 'payment');
				$category = filter_input(INPUT_POST, 'category');
				$comment = filter_input(INPUT_POST, 'comment');
				$user_id = $_SESSION['logged_id'];

	
				if(empty($amount) || $amount < 0.01 || $amount > 99999999.99) {
					$_SESSION['form_amount'] = $_POST['amount'];
					$_SESSION['form_datetime'] = $_POST['datetime'];
					$_SESSION['form_payment'] = $_POST['payment'];
					$_SESSION['form_category'] = $_POST['category'];
					$_SESSION['form_comment'] = $_POST['comment'];
					$_SESSION['error'] = "Podaj prawidłową kwotę!";
					$_SESSION['initial_state'] = 1;
					$_SESSION['wrong_amount'] = 1;
					header('Location: addExpense.php');
				} elseif(empty($datetime) || strtotime($datetime) < strtotime('2000-01-01T00:00') || strtotime($datetime) > strtotime($nextYear)){
					$_SESSION['form_amount'] = $_POST['amount'];
					$_SESSION['form_datetime'] = $_POST['datetime'];
					$_SESSION['form_payment'] = $_POST['payment'];
					$_SESSION['form_category'] = $_POST['category'];
					$_SESSION['form_comment'] = $_POST['comment'];
					$_SESSION['initial_state'] = 1;
					$_SESSION['wrong_date'] = 1;
					$_SESSION['error'] = "Podaj prawidłową datę i czas!";
					header('Location: addExpense.php');
				} elseif (empty($payment)){
					$_SESSION['form_amount'] = $_POST['amount'];
					$_SESSION['form_datetime'] = $_POST['datetime'];
					$_SESSION['form_payment'] = $_POST['payment'];
					$_SESSION['form_category'] = $_POST['category'];
					$_SESSION['form_comment'] = $_POST['comment'];
					$_SESSION['initial_state'] = 1;
					$_SESSION['wrong_payment'] = 1;
					$_SESSION['error'] = "Podaj prawidłową metodę płatności!";
					header('Location: addExpense.php');		
				} elseif (empty($category)){
					$_SESSION['form_amount'] = $_POST['amount'];
					$_SESSION['form_datetime'] = $_POST['datetime'];
					$_SESSION['form_payment'] = $_POST['payment'];
					$_SESSION['form_category'] = $_POST['category'];
					$_SESSION['form_comment'] = $_POST['comment'];
					$_SESSION['initial_state'] = 1;
					$_SESSION['wrong_category'] = 1;
					$_SESSION['error'] = "Podaj prawidłową kategorię!";
					header('Location: addExpense.php');
				} else {
		
					require_once 'database.php';
					
					$catQuery = $db->prepare("SELECT exp_cat_name FROM expense_user_category WHERE id_user = :id_user AND id_exp_user_cat = :id_cat");
					$catQuery->bindValue(':id_user', $user_id, PDO::PARAM_STR);
					$catQuery->bindValue(':id_cat', $category, PDO::PARAM_STR);
					$catQuery->execute();
					$catResult = $catQuery->fetch();
					
					$payQuery = $db->prepare("SELECT pay_met_name FROM payment_user_method WHERE id_user = :id_user AND id_user_pay_met = :id_met");
					$payQuery->bindValue(':id_user', $user_id, PDO::PARAM_STR);
					$payQuery->bindValue(':id_met', $payment, PDO::PARAM_STR);
					$payQuery->execute();
					$payResult = $payQuery->fetch();

					if($catResult <= 0){
						$_SESSION['form_amount'] = $_POST['amount'];
						$_SESSION['form_datetime'] = $_POST['datetime'];
						$_SESSION['form_category'] = $_POST['category'];
						$_SESSION['form_payment'] = $_POST['payment'];
						$_SESSION['form_comment'] = $_POST['comment'];
						$_SESSION['wrong_category'] = 1;
						$_SESSION['initial_state'] = 1;
						$_SESSION['error'] = "Podaj prawidłową kategorię!";
						header('Location: addExpense.php');
						exit();
						
					} elseif($payResult <= 0){
						$_SESSION['form_amount'] = $_POST['amount'];
						$_SESSION['form_datetime'] = $_POST['datetime'];
						$_SESSION['form_category'] = $_POST['category'];
						$_SESSION['form_payment'] = $_POST['payment'];
						$_SESSION['form_comment'] = $_POST['comment'];
						$_SESSION['wrong_payment'] = 1;
						$_SESSION['initial_state'] = 1;
						$_SESSION['error'] = "Podaj prawidłową metodę płatności!";
						header('Location: addExpense.php');
						exit();
			
					} else {
					
						$query = $db->prepare('INSERT INTO expense VALUES(NULL, :id_user, :datetime, :id_exp_cat, :amount, :id_pay_met, :comment)');
						$query->bindValue(':id_user', $user_id, PDO::PARAM_STR);
						$query->bindValue(':datetime', $datetime, PDO::PARAM_STR);
						$query->bindValue(':id_exp_cat', $category, PDO::PARAM_STR);
						$query->bindValue(':id_pay_met', $payment, PDO::PARAM_STR);
						$query->bindValue(':amount', $amount, PDO::PARAM_STR);
						$query->bindValue(':comment', $comment, PDO::PARAM_STR);
						$query->execute();
				}
			}

	} else {
		header('Location: login.php');
		exit();
	}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
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

  
  <!-- loginPage -->
  <main id="loginPage">


      <div class="py-3 col-5 pb-5 pt-5 text-center container">
        <div class="p-5 pb-5 pt-5 border-bottom-0">
          <p class="fw-semibold mb-0 fs-2 text-start text-success">Wydatek dodany pomyślnie!</p>
		  <p class="fw-semibold mb-0 fs-2 text-start">Czy chcesz dodać kolejny?</p>
        </div>

		<div class="modal-body p-5 pt-5 pb-5">
          <div class="d-grid gap-2 pb-5 pt-5 d-md-flex justify-content-md-center">
			<a href="./addExpense.php" class="w-100 mb-2 btn btn-lg rounded-3 btn-primary my-5 mb-5" role="button">Dodaj wydatek</a>
            <a href="./main.php" class="w-100 mb-2 btn btn-lg rounded-3 btn-outline-secondary my-5 mb-5" role="button">Anuluj</a>
          </div>
		 </div>
		  
      </div>
 

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