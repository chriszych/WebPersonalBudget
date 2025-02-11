<?php
	session_start();
	if(!isset($_SESSION['logged_id'])){
		header('Location: login.php');
	} else {
		
		if(!isset($_SESSION['initial_state'])){
			$initial_state = 0;
		} else {
			$initial_state = $_SESSION['initial_state'];
		}
			
		if(!isset($_SESSION['wrong_amount'])){
			$wrong_amount = 0;
		} else {
			$wrong_amount = $_SESSION['wrong_amount'];
		}
		
		if(!isset($_SESSION['wrong_date'])){
			$wrong_date = 0;
		} else {
			$wrong_date = $_SESSION['wrong_date'];
		}
		if(!isset($_SESSION['wrong_category'])){
			$wrong_category = 0;
		} else {
			$wrong_category = $_SESSION['wrong_category'];
		}
		
		$now = date('Y-m-d\TH:i');
		$nextYear = date('Y-m-d\TH:i', strtotime('+1 year'));
		
		require_once 'database.php';
		
		$incQuery = $db->prepare("SELECT id_inc_user_cat, inc_cat_name FROM income_user_category WHERE id_user = :user");
		$incQuery->bindValue(':user', $_SESSION['logged_id'], PDO::PARAM_STR);
		$incQuery->execute();
		$incCat = $incQuery->fetchAll(PDO::FETCH_ASSOC);
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
            <a href="./main.php"><i class="fa-solid fa-house-chimney" style="color: #2861c3;"></i></a>
            <h1><a class="navbar-brand col-lg-3 me-0" href="./main.php">&nbsp;Budżet online</a></h1>
            <ul class="navbar-nav col-lg-9 justify-content-lg-end">

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1" href="./addExpense.php">Nowy wydatek</a>
              </li>

              <li class="nav-item">
                <a class="btn btn-lg btn-outline-primary m-1 disabled" href="./addIncome.php">Nowy przychód</a>
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
  <section id="mainPageContent">

    <section class="py-3 col-12 col-md-8 col-lg-5 text-center container">
 <!-- Subpage AddIncome -->


        <div class="text-start p-5 py-3 mb-2 border-bottom-0">
          <p class="fw-bold mb-0 fs-2">Dodaj nowy przychód: </p>
        </div>
        
        <div class="p-5 py-0">
          <form method="post" action="insertNewIncome.php">
            <div class="form-floating mb-3">
			<input type="number" id="registerFloatingInput" placeholder="kwota w PLN" step="0.01" min="0.01" max="99999999.99" name="amount" class="form-control rounded-3 <?= (($wrong_amount == 1)&&($initial_state == 1)) ? 'border-danger':''?>" <?= isset($_SESSION['form_amount']) ? 'value="'.$_SESSION['form_amount'].'"' : '' ?>>
              <label for="registerFloatingInput">Kwota w PLN:</label>
            </div>
            <div class="form-floating mb-3">
              <input type="datetime-local" id="registerFloatingLogin" min="2000-01-01T00:00" max="<?php echo $nextYear;?>" placeholder="<?php echo $now; ?>" name="datetime" class="form-control rounded-3 <?= (($wrong_date == 1)&&($initial_state == 1)) ? 'border-danger':''?>" <?= isset($_SESSION['form_datetime']) ? 'value="'.$_SESSION['form_datetime'].'"' : 'value="'.$now.'"' ?>>
              <label for="registerFloatingLogin">Data:</label>
            </div>


            <div class="form-floating form-group mb-3">
			  <select id="incomeCategory" name="category" class="form-control rounded-3 <?= (($wrong_category == 1)&&($initial_state == 1)) ? 'border-danger':''?>" <?= isset($_SESSION['form_category']) ? 'value="'.$_SESSION['form_category'].'"' : '' ?>>
                <option disabled selected>Wybierz kategorię...</option>
				
				<?php foreach ($incCat as $row): ?> 
				<option value="<?php echo $row['id_inc_user_cat']; ?>" 
				<?= (isset($_SESSION['form_category']) && $_SESSION['form_category'] == $row['id_inc_user_cat']) ? 'selected' : '' ?>>
				<?php echo $row['inc_cat_name']; ?>
				 </option> 
				<?php endforeach; ?>
				
              </select>
              <label for="incomeeCategory">Kategoria:</label>
            </div>

            <div class="form-floating mb-3">
			  <textarea id="expenseComment" name="comment" rows="2" cols="30" placeholder="dodaj komentarz (opcja)" class="form-control rounded-3"><?= isset($_SESSION['form_comment']) ? $_SESSION['form_comment'] : '' ?></textarea>
              <br><br>
              <label for="incomeComment">Komentarz:</label>
			  
			  <p class="text-danger fw-bold">
			 <?php 
				if (isset($_SESSION['error'])){
					echo $_SESSION['error'];
					unset($_SESSION['error']);
				}
				 unset($_SESSION['form_amount']);
				 unset($_SESSION['form_datetime']);
				 unset($_SESSION['form_category']);
				 unset($_SESSION['form_comment']);
				 unset($_SESSION['initial_state']);
				 unset($_SESSION['wrong_amount']);
				 unset($_SESSION['wrong_date']);
				 unset($_SESSION['wrong_category']);
				?>
				</p>
			  
			  
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary my-1 mb-1 mb-md-5" type="submit">Dodaj</button>
            <a href="./main.php" class="w-100 mb-2 btn btn-lg rounded-3 btn-outline-secondary my-1 mb-1 mb-md-5" role="button">Anuluj</a>
          </form>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>


</body>

</html>