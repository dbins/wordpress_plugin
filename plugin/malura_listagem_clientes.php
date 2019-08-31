<?php
include_once 'lib/DAL/acoes.php'; 
require_once('lib/BLL/Acoes.php');
$acoes = new Bll_Acoes();
$resultado = $acoes->Teste();
?>
<h1 align="center">Clientes Cadastrados</h1>
<table border="1" width="800" align="center" cellpadding="2" cellspacing="2">
	<thead>
		<tr>
			<th bgcolor="000000"><font color="#FFFFFF">Nome</font></th>
			<th bgcolor="000000"><font color="#FFFFFF">E-mail</font></th>
			<th bgcolor="000000"><font color="#FFFFFF">Telefone</font></th>
			<th bgcolor="000000"><font color="#FFFFFF">Endereço</font></th>
		</tr>
	</thead>
<?php
while ($linha=mysqli_fetch_array($resultado)) {
?>	
	<tr>
		<td><?php echo $linha["name"]?></td>
		<td><?php echo $linha["email"]?></td>
		<td><?php echo $linha["phone"]?></td>
		<td><?php echo $linha["address"]?></td>
	</tr>
<?php	
}
?>
</table>
