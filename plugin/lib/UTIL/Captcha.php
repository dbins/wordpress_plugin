<?php
class Captcha
{
 
    public function gerar_captcha($largura,$altura,$tamanho_fonte,$quantidade_letras, $fonte){
        $imagem = imagecreate($largura,$altura); // define a largura e a altura da imagem
        $preto  = imagecolorallocate($imagem,0,0,0); // define a cor preta
        $branco = imagecolorallocate($imagem,255,255,255); // define a cor branca
       
        // define a palavra conforme a quantidade de letras definidas no parametro $quantidade_letras
        $palavra = substr(str_shuffle("abcdefghijklmnpqrstuvyxwz23456789"),0,($quantidade_letras));
        $_SESSION["palavra"] = $palavra; // atribui para a sessao a palavra gerada
        for($i = 1; $i <= $quantidade_letras; $i++){
            imagettftext($imagem,$tamanho_fonte,rand(-25,25),($tamanho_fonte*$i),($tamanho_fonte + 10),$branco,$fonte,substr($palavra,($i-1),1)); // atribui as letras a imagem
        }
        imagejpeg($imagem); // gera a imagem
        imagedestroy($imagem); // limpa a imagem da memoria
    }
 
}
?>