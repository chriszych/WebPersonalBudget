<?php

session_start();

	if(!isset($_SESSION['initial_state'])){
		$initial_state = 0;
	} else {
		$initial_state = $_SESSION['initial_state'];
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
              <a class="btn btn-lg btn-primary m-1" href="./login.php">Logowanie</a>
            </li>
            <li class="nav-item">
              <a class="btn btn-lg btn-outline-primary m-1 disabled" href="./register.php">Rejestracja</a>
            </li>
          </ul>
        </div>
      </div>
    </div>

  </nav>

  
  <!-- registerPage -->
  <main id="registerPage">


      <div class="py-3 col-12 col-md-8 col-lg-5 text-center container">

          <div class="px-5 pb-4 border-bottom-0 text-start">
          <p class="fw-bold mb-0 fs-2">Rejestracja w serwisie: </p>
           </div>

           <div class="p-5 pt-0">
           <form method="post" action="addUser.php">
			
			<div class="form-floating mb-2">
              <input type="text" class="form-control rounded-3 <?= ((empty($_SESSION['form_firstname']))&&($initial_state == 1)) ? 'border-danger':''?>" id="registerFloatingInput" placeholder="firstname" name="firstname" <?= isset($_SESSION['form_firstname']) ? 'value="'.$_SESSION['form_firstname'].'"' : '' ?>>
              <label for="registerFloatingInput">Imię</label>
            </div>
            <div class="form-floating mb-2">
              <input type="text" class="form-control rounded-3 <?= ((empty($_SESSION['form_lastname']))&&($initial_state == 1)) ? 'border-danger':''?>" id="registerFloatingInput" placeholder="surename" name="lastname" <?= isset($_SESSION['form_lastname']) ? 'value="'.$_SESSION['form_lastname'].'"' : '' ?>>
              <label for="registerFloatingInput">Nazwisko</label>
            </div>
			
            <div class="form-floating mb-2">
              <input type="email" class="form-control rounded-3 <?= (((empty($_SESSION['form_email'])) || $_SESSION['usedEmail']==1)&&($initial_state == 1)) ? 'border-danger':''?>" id="registerFloatingInput" placeholder="uzytkownik@serwer.com" name="email" <?= isset($_SESSION['form_email']) ? 'value="'.$_SESSION['form_email'].'"' : '' ?>>
              <label for="registerFloatingInput">Adres e-mail</label>
            </div>
            <div class="form-floating mb-2">
              <input type="text" class="form-control rounded-3 <?= ((empty($_SESSION['form_login']))&&($initial_state == 1)) ? 'border-danger':''?>" id="registerFloatingLogin" placeholder="Login" name="login" <?= isset($_SESSION['form_login']) ? 'value="'.$_SESSION['form_login'].'"' : '' ?>>
              <label for="registerFloatingLogin">Login</label>
            </div>
            <div class="form-floating mb-2">
              <input type="password" class="form-control rounded-3 <?= ((empty($_SESSION['form_pass']))&&($initial_state == 1)) ? 'border-danger':''?>" id="registerFloatingPass" placeholder="Password" name="password">
              <label for="registerFloatingPass">Hasło</label>
            </div>
            <div class="form-floating mb-2">
              <input type="password" class="form-control rounded-3 <?= ((empty($_SESSION['form_confPass']))&&($initial_state == 1)) ? 'border-danger':''?>" id="floatingPasswordSecond" placeholder="Confirm password" name="confirmPassword">
              <label for="floatingPasswordSecond">Potwierdź hasło</label>
            </div>
			
			<p class="text-danger fw-bold">
			 <?php 
				if (isset($_SESSION['error'])){
					echo $_SESSION['error'];
					unset($_SESSION['error']);
				}
				 unset($_SESSION['form_firstname']);
				 unset($_SESSION['form_lastname']);
				 unset($_SESSION['form_email']);
				 unset($_SESSION['form_login']);
				 unset($_SESSION['form_pass']);
				 unset($_SESSION['form_confPass']);
				 unset($_SESSION['initial_state']);
				 unset($_SESSION['usedEmail']);
				?>
				</p>
			
          <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <button class="w-100 btn btn-lg rounded-3 btn-primary my-0 mb-0" role="button" type="submit">Zarejestruj</button>
            <a href="./index.php" class="w-100 btn btn-lg rounded-3 btn-outline-secondary my-0 mb-0" role="button">Anuluj</a>
    
          </div>

          <small class="text-body-secondary">Klikając "Zarejestruj", wyrażasz zgodę na warunki użytkowania oraz regulamin serwisu.</small>

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