<?php
	session_start();

	if((isset($_POST['firstname']))&&
		(isset($_POST['lastname']))&&
		(isset($_POST['email']))&&
		(isset($_POST['login']))&&
		(isset($_POST['password']))&&
		(isset($_POST['confirmPassword'])))
		{
	
	$firstname = filter_input(INPUT_POST, 'firstname');
	$lastname = filter_input(INPUT_POST, 'lastname');
	$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
	$login = filter_input(INPUT_POST, 'login');
	$password = filter_input(INPUT_POST, 'password');
	$confirmPassword = filter_input(INPUT_POST, 'confirmPassword');
	$passwordHash = password_hash($password,PASSWORD_DEFAULT);
	$date = date('Y-m-d');
	$_SESSION['usedEmail'] = 0;
	
	if(empty($email)) {
		$_SESSION['form_firstname'] = $_POST['firstname'];
		$_SESSION['form_lastname'] = $_POST['lastname'];
		$_SESSION['form_email'] = $_POST['email'];
		$_SESSION['form_login'] = $_POST['login'];
		$_SESSION['error'] = "Podaj prawidłowy adres email!";
		$_SESSION['initial_state'] = 1;
		header('Location: register.php');
	} elseif (empty($firstname)){
		$_SESSION['form_firstname'] = $_POST['firstname'];
		$_SESSION['form_lastname'] = $_POST['lastname'];
		$_SESSION['form_email'] = $_POST['email'];
		$_SESSION['form_login'] = $_POST['login'];
		$_SESSION['error'] = "Podaj prawidłowe imię!";
		$_SESSION['initial_state'] = 1;
		header('Location: register.php');
	} elseif (empty($lastname)){
		$_SESSION['form_firstname'] = $_POST['firstname'];
		$_SESSION['form_lastname'] = $_POST['lastname'];
		$_SESSION['form_email'] = $_POST['email'];
		$_SESSION['form_login'] = $_POST['login'];
		$_SESSION['error'] = "Podaj prawidłowe nazwisko!";
		$_SESSION['initial_state'] = 1;
		header('Location: register.php');
		
	} elseif (empty($login)){
		$_SESSION['form_firstname'] = $_POST['firstname'];
		$_SESSION['form_lastname'] = $_POST['lastname'];
		$_SESSION['form_email'] = $_POST['email'];
		$_SESSION['form_login'] = $_POST['login'];
		$_SESSION['error'] = "Podaj prawidłowy login!";
		$_SESSION['initial_state'] = 1;
		header('Location: register.php');
	} elseif (empty($password)){
		$_SESSION['form_firstname'] = $_POST['firstname'];
		$_SESSION['form_lastname'] = $_POST['lastname'];
		$_SESSION['form_email'] = $_POST['email'];
		$_SESSION['form_login'] = $_POST['login'];
		$_SESSION['form_pass'] = $_POST['password'];
		$_SESSION['form_confPass'] = $_POST['confirmPassword'];
		$_SESSION['error'] = "Podaj prawidłowe hasło!";
		$_SESSION['initial_state'] = 1;
		header('Location: register.php');
	} elseif ($password != $confirmPassword){
		$_SESSION['form_firstname'] = $_POST['firstname'];
		$_SESSION['form_lastname'] = $_POST['lastname'];
		$_SESSION['form_email'] = $_POST['email'];
		$_SESSION['form_login'] = $_POST['login'];
		$_SESSION['initial_state'] = 1;
		$_SESSION['error'] = "Hasło się nie zgadza!";
		header('Location: register.php');
		
	} else {
		
		require_once 'database.php';
		
		$preQuery = $db->prepare("SELECT id_user FROM users WHERE user_email = :email");
		$preQuery->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
		$preQuery->execute();
		$result = $preQuery->fetch();

		if($result > 0){
			$_SESSION['usedEmail'] = 1;
			$_SESSION['email']=$_POST['email'];
			$_SESSION['error'] = "Podany e-mail jest już używany!";
			$_SESSION['form_firstname'] = $_POST['firstname'];
			$_SESSION['form_lastname'] = $_POST['lastname'];
			$_SESSION['form_email'] = $_POST['email'];
			$_SESSION['form_login'] = $_POST['login'];
			$_SESSION['initial_state'] = 1;
			header('Location: register.php');
			exit();
			
		} else {
		
			$query = $db->prepare('INSERT INTO users VALUES(NULL, :firstname, :lastname, :email, :login, :pass, :date)');
			$query->bindValue(':firstname', $firstname, PDO::PARAM_STR);
			$query->bindValue(':lastname', $lastname, PDO::PARAM_STR);
			$query->bindValue(':email', $email, PDO::PARAM_STR);
			$query->bindValue(':login', $login, PDO::PARAM_STR);
			$query->bindValue(':pass', $passwordHash, PDO::PARAM_STR);
			$query->bindValue(':date', $date, PDO::PARAM_STR);
			$query->execute();
			
			$preQuery = $db->prepare("SELECT id_user FROM users WHERE user_email = :email");
			$preQuery->bindValue(':email', $email, PDO::PARAM_STR);
			$preQuery->execute();
			$result = $preQuery->fetch();
			
			$query = $db->prepare('INSERT INTO expense_user_category (exp_cat_name, id_user) SELECT exp_cat_name, :user_id FROM default_expense_category');
			$query->bindValue(':user_id', $result['id_user'], PDO::PARAM_STR);
			$query->execute();
			
			$query = $db->prepare('INSERT INTO income_user_category (inc_cat_name, id_user) SELECT inc_cat_name, :user_id FROM default_income_category');
			$query->bindValue(':user_id', $result['id_user'], PDO::PARAM_STR);
			$query->execute();
			
			$query = $db->prepare('INSERT INTO payment_user_method (pay_met_name, id_user) SELECT pay_met_name, :user_id FROM default_payment_method');
			$query->bindValue(':user_id', $result['id_user'], PDO::PARAM_STR);
			$query->execute();
		}
	}
	
} else {
	header('Location: register.php');
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
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#logNavbar" aria-controls="logNavbar" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
  
        <div class="collapse navbar-collapse d-lg-flex" id="logNavbar">
          <a href="./index.php"><i class="fa-solid fa-house-chimney" style="color: #2861c3;"></i></a>
          <h1><a class="navbar-brand col-lg-3 me-0" href="./index.php">&nbsp;Budżet online</a></h1>
          <ul class="navbar-nav col-lg-9 justify-content-lg-end">
            <li class="nav-item">
              <a class="btn btn-lg btn-primary m-1 disabled" href="./login.php">Logowanie</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-lg btn-outline-primary m-1" href="./register.php">Rejestracja</a>
            </li>
          </ul>
        </div>
      </div>
    </div>

  </nav>

  
  <!-- loginPage -->
  <main id="loginPage">


      <div class="py-3 col-5 text-center container">
        <div class="p-5 pb-4 border-bottom-0">
          <p class="error fw-bold mb-0 fs-2 text-start">Użytkownik dodany pomyślnie!</p>
		  <p class="error fw-bold mb-0 fs-2 text-start">Możesz się teraz zalogować:</p>
        </div>

        <div class="modal-body p-5 pt-0">
          <form method="post" action="main.php">
            <div class="form-floating mb-3">
              <input type="text" class="form-control rounded-3" id="loginFloatingName" name="login" <?= isset($_SESSION['bad_attempt']) ? 'value="'.$_SESSION['bad_attempt'].'"' : '' ?>>
              <label for="loginFloatingName">Login</label>
            </div>
            <div class="form-floating mb-3">
              <input type="password" class="form-control rounded-3" id="LoginFloatingPass" placeholder="Password" name="pass">
              <label for="LoginFloatingPass">Hasło</label>
			  
			  <p class="text-danger fw-bold">
			  <?php 
				if (isset($_SESSION['bad_attempt'])){
					echo 'Niepoprawny login lub hasło!';
					unset($_SESSION['bad_attempt']);
				}
				?>
				</p>

          <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary my-5 mb-5" role="button" type="submit">Zaloguj się</button>
            <a href="./index.html" class="w-100 mb-2 btn btn-lg rounded-3 btn-outline-secondary my-5 mb-5" role="button">Anuluj</a>
          </div>
		  


          </form>
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