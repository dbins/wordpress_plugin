<?php
if (isset($_POST["acao"])){
	if ($_POST["acao"] == "LOGIN"){
		$_SESSION['malura_email'] = $_POST["email"];
		malura_lista();
	}	
} else {
?>
<div class="wrap">
<form action=""  method="POST" class="formee">
	<div >
		<label>E-mail</label>
			<input type="text" name="email">
	</div>
		 
		  <div >
			<input type="submit" value=" Enviar Arquivo">
			<input type="hidden" name="acao" value="LOGIN" />
		 </div>
	 
	 
	 </form>
</div>
<?php
}
?>