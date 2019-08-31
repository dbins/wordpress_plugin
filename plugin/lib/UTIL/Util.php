<?php
class Util
{

	public function bloquearPagina() {
		if(session_id() == '' || !isset($_SESSION)) {
			header("Location: index.php"); 
		} else {
			if(isset($_SESSION["sessao_logado"])) {
				if ($_SESSION["sessao_logado"]!="OK"){
					header("Location: index.php");
				}
			} else {
				header("Location: index.php");
			}
		}
	}
 
   public function filtro($value) {
		$newVal = trim($value);
		$newVal = htmlspecialchars($newVal);
		//$newVal = mysqli_real_escape_string($newVal);
		if(get_magic_quotes_gpc())  {
			$newVal = stripslashes($newVal);
		}
		$newVal = $this->ValidarDados($newVal);
		return $newVal;
	}
	
	public function PrimeiroNome($texto){
		$texto_array = explode(' ', $texto);
		$resposta = $texto_array[0];
		return $resposta;
	}
	
	public function DispararEmail($To, $Subject, $corpoEmail) {
		//Fazendo o disparo do e-mail
		$Name = "FISIOAGENDA";
		$From = 'agenda@fisioagenda.com.br';
		
		$From = "'admin@" . $_SERVER["HTTP_HOST"];
						
		/* Verifica qual é o sistema operacional do servidor para ajustar o cabeçalho de forma correta. Não alterar */
		if(PHP_OS == "Linux") $quebra_linha = "\n"; //Se for Linux
		elseif(PHP_OS == "WINNT") $quebra_linha = "\r\n"; // Se for Windows
		else die("Este script nao esta preparado para funcionar com o sistema operacional de seu servidor");
				
		$headers = "MIME-Version: 1.1".$quebra_linha;
		$headers .= "Content-type: text/html; charset=iso-8859-1".$quebra_linha;
		// Perceba que a linha acima contém "text/html", sem essa linha, a mensagem não chegará formatada.
		
		$headers .= "From: ". $Name . " <" . $From .">" . $quebra_linha;
		$headers .= "Return-Path: " . $From . $quebra_linha;
		$envio =mail($To, $Subject, $corpoEmail, $headers, "-r". $From);
		if($envio) {
			$sucesso = "OK";
		} else {
			$sucesso =  "ERRO";
		}
		return $sucesso;
	}
	
	public function muda_data_amd($dt) {
		$dia = substr($dt,0,2);
		$mes = substr($dt,3,2);
		$ano = substr($dt,6,4);
		$data = $ano."/".$mes."/".$dia;
		return $data;
	}
	
	
	public function validaCnpj($cnpj) {
		$cnpj = trim($cnpj);
		$soma = 0;
		$multiplicador = 0;
		$multiplo = 0;
	   
	   
		# [^0-9]: RETIRA TUDO QUE NÃO É NUMÉRICO,  "^" ISTO NEGA A SUBSTITUIÇÃO, OU SEJA, SUBSTITUA TUDO QUE FOR DIFERENTE DE 0-9 POR "";
		$cnpj = preg_replace('/[^0-9]/', '', $cnpj);
	   
		if(empty($cnpj) || strlen($cnpj) != 14) 
			return FALSE;
	
		# VERIFICAÇÃO DE VALORES REPETIDOS NO CNPJ DE 0 A 9 (EX. '00000000000000')    
		for($i = 0; $i <= 9; $i++)
		{
			$repetidos = str_pad('', 14, $i);
		   
			if($cnpj === $repetidos)
				return FALSE;
		}
	   
		# PEGA A PRIMEIRA PARTE DO CNPJ, SEM OS DÍGITOS VERIFICADORES    
	 $parte1 = substr($cnpj, 0, 12);   
	   
		# INVERTE A 1ª PARTE DO CNPJ PARA CONTINUAR A VALIDAÇÃO    $parte1_invertida = strrev($parte1);
	   
		# PERCORRENDO A PARTE INVERTIDA PARA OBTER O FATOR DE CALCULO DO 1º DÍGITO VERIFICADOR
		for ($i = 0; $i <= 11; $i++)
		{
			$multiplicador = ($i == 0) || ($i == 8) ? 2 : $multiplicador;
	
			$multiplo = ($parte1_invertida[$i] * $multiplicador);
	
			$soma += $multiplo;
		   
			$multiplicador++;
		}
	   
		# OBTENDO O 1º DÍGITO VERIFICADOR        
		$rest = $soma % 11;
	   
		$dv1 = ($rest == 0 || $rest == 1) ? 0 : 11 - $rest;
		   
		# PEGA A PRIMEIRA PARTE DO CNPJ CONCATENANDO COM O 1º DÍGITO OBTIDO 
		$parte1 .= $dv1;
	   
		# MAIS UMA VEZ INVERTE A 1ª PARTE DO CNPJ PARA CONTINUAR A VALIDAÇÃO 
		$parte1_invertida = strrev($parte1);
		   
		$soma = 0;
	   
		# MAIS UMA VEZ PERCORRE A PARTE INVERTIDA PARA OBTER O FATOR DE CALCULO DO 2º DÍGITO VERIFICADOR       
		for ($i = 0; $i <= 12; $i++)
		{
			$multiplicador = ($i == 0) || ($i == 8) ? 2 : $multiplicador;
	
			$multiplo = ($parte1_invertida[$i] * $multiplicador);
	
			$soma += $multiplo;
		   
			$multiplicador++;
		}
		   
		# OBTENDO O 2º DÍGITO VERIFICADOR
		$rest = $soma % 11;
	   
		$dv2 = ($rest == 0 || $rest == 1) ? 0 : 11 - $rest;
	   
		# AO FINAL COMPARA SE OS DÍGITOS OBTIDOS SÃO IGUAIS AOS INFORMADOS (OU A SEGUNDA PARTE DO CNPJ)   
		return ($dv1 == $cnpj[12] && $dv2 == $cnpj[13]) ? TRUE : FALSE;
		//echo ($dv1 == $cnpj[12] && $dv2 == $cnpj[13]) ? 'TRUE' : 'FALSE';
	} 
	
	public function validaCPF($cpf = null) {
		// Verifica se um número foi informado
		if(empty($cpf)) {
			return false;
		}
	 
		// Elimina possivel mascara
		$cpf = ereg_replace('[^0-9]', '', $cpf);
		$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
		 
		// Verifica se o numero de digitos informados é igual a 11 
		if (strlen($cpf) != 11) {
			return false;
		}
		// Verifica se nenhuma das sequências invalidas abaixo 
		// foi digitada. Caso afirmativo, retorna falso
		else if ($cpf == '00000000000' || 
			$cpf == '11111111111' || 
			$cpf == '22222222222' || 
			$cpf == '33333333333' || 
			$cpf == '44444444444' || 
			$cpf == '55555555555' || 
			$cpf == '66666666666' || 
			$cpf == '77777777777' || 
			$cpf == '88888888888' || 
			$cpf == '99999999999') {
			return false;
		 // Calcula os digitos verificadores para verificar se o
		 // CPF é válido
		 } else {   
			 
			for ($t = 9; $t < 11; $t++) {
				 
				for ($d = 0, $c = 0; $c < $t; $c++) {
					$d += $cpf{$c} * (($t + 1) - $c);
				}
				$d = ((10 * $d) % 11) % 10;
				if ($cpf{$c} != $d) {
					return false;
				}
			}
	 
			return true;
		}
	}
	
	public function moeda($get_valor) {		
		$source = array('.', ','); 		
		$replace = array('', '.');		
		$valor = str_replace($source, $replace, $get_valor); 
		//remove os pontos e substitui a virgula pelo ponto		
		return $valor; //retorna o valor formatado para gravar no banco	
	}
	
	public function dif_horario($horario1, $horario2) {
		$horario1 = strtotime("1/1/1980 $horario1");
		$horario2 = strtotime("1/1/1980 $horario2");
         
		if ($horario2 < $horario1) {
			$horario2 = $horario2 + 86400;
		}
  
		return ($horario2 - $horario1) / 3600;      
	}

	public function dif_horario2($horario1, $horario2) {
		$horario1 = strtotime("1/1/1980 $horario1");
		$horario2 = strtotime("1/1/1980 $horario2");
         
		if ($horario2 < $horario1) {
			$horario2 = $horario2 + 86400;
		}
		return ($horario2 - $horario1) / 60;      
	}

	public function ConverterEmSegundos($Horario) {
		$resultado = 0;
		$tmp_hora = split(":", $Horario);
		if (is_array($tmp_hora)) {
			$resultado = (intval($tmp_hora[0]) * 3600) + (intval($tmp_hora[1]) * 60) + intval($tmp_hora[2]);
		}
		return $resultado;
	}


	public function getWeekday($date) {
		return date('w', strtotime($date));
	}

	public function getNameWeekday($int) {
		$dowMap = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
		return $dowMap[$int];
	}	
	
	public function dateRange($first, $last, $step = '+1 day', $format = 'd/m/Y' ) { 

		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);

		while( $current <= $last ) { 

			$dates[] = date($format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}
	
	function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
	}

	public function getToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    for($i=0;$i<$length;$i++){
        $token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
    }
		return $token;
	}
	
	public function RandomString($length) {
	$key = "";
    $keys = array_merge(range(0,9), range('a', 'z'));

    for($i=0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;
	}
	
	public function ValidarDados($input) {
	  $textoOK=$input;
	  $lixo=array("select", "drop", "shutdown", "where", "create table", "show table", "show tables", ";", "--", "insert", "delet", "delete", "xp_", "&", "--", "'OR USERNAME IS NOT NULL OR USERNAME='", "UNION ALL", "DESC USERS", "ODBC", "SYSCOLUMNS", "SYSTYPES", "SYSOBJECTS", "SYS.OBJECTS", "INFORMATION_SCHEMA.COLUMNS", "INFORMATION_SCHEMA.TABLES", "INFORMATION_SCHEMA.ROUTINES", "ON SELECT ALL FROM WHERE", "UPDATE ", "DECLARE ", "syscolumns");
	  for ($i=0; $i<=count($lixo)-1; $i++) {
		$textoOK=str_replace($lixo[$i], "", $textoOK); 
	  }
	  if (!($textoOK == "" || !isset($textoOK))) {
		$textoOK=str_replace("convert(","",$textoOK);
		$textoOK=str_replace("CONVERT(","",$textoOK);
		$textoOK=str_replace("char(","",$textoOK);
		$textoOK=str_replace("CHAR(","",$textoOK);
		$textoOK=str_replace("'or'1'='1'","",$textoOK);
		$textoOK=str_replace("'1='1'","",$textoOK);
		$textoOK=str_replace("1=1","",$textoOK);
		$textoOK=str_replace("1'1","",$textoOK);
	  }
	  //Remover HTML do texto que foi validado
	  $textoOK=$this->RemoveHTML($textoOK); 
	  //Remover JavaScript do texto que foi validado
	  $textoOK=$this->clearJS($textoOK); 
	  return $textoOK;
	}
	
	public function RemoveHTML($strText) {
		return strip_tags($strText);
	}	

	public function clearJS($s) {
		 $do = true;
		while ($do) {
			$start = stripos($s,'<script');
			$stop = stripos($s,'</script>');
			if ((is_numeric($start))&&(is_numeric($stop))) {
				$s = substr($s,0,$start).substr($s,($stop+strlen('</script>')));
			} else {
				$do = false;
			}
		}
		return trim($s);

	}
	
	public function Mask($mask,$str){
		$str = str_replace(" ","", $str);

		for($i=0;$i<strlen($str);$i++){
			$mask[strpos($mask,"#")] = $str[$i];
		}

		return $mask;
	}
	
	public function utf8_encode_deep($input) {
		if (is_string($input)) {
			$input = utf8_encode($input);
		} else if (is_array($input)) {
			foreach ($input as &$value) {
				$this->utf8_encode_deep($value);
			}
			
			unset($value);
		} else if (is_object($input)) {
			$vars = array_keys(get_object_vars($input));
			
			foreach ($vars as $var) {
				$this->utf8_encode_deep($input->$var);
			}
		}
		return $input;
	}
	
	public function utf8_decode_deep($input) {
		if (is_string($input)) {
			$input = utf8_decode($input);
		} else if (is_array($input)) {
			foreach ($input as &$value) {
				$this->utf8_decode_deep($value);
			}
			
			unset($value);
		} else if (is_object($input)) {
			$vars = array_keys(get_object_vars($input));
			
			foreach ($vars as $var) {
				$this->utf8_decode_deep($input->$var);
			}
		}
		return $input;
	}
	
	public function sanitizeString($string) {

		// matriz de entrada
		$what = array( 'ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','À','Á','É','Í','Ó','Ú','ñ','Ñ','ç','Ç',' ','-','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º' );

		// matriz de saída
		$by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','E','I','O','U','n','n','c','C','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-' );

		// devolver a string
		return str_replace($what, $by, $string);
	}
 
}
?>